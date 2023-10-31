<?php
// LigaDeAventureros
// Copyright (C) 2023 Santiago González Lago

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

namespace App\Controllers;

class Session extends BaseController {

    public function __construct() {
        $this->SessionModel = model('SessionModel');
        $this->AdventureModel = model('AdventureModel');
        $this->CharacterModel = model('CharacterModel');
    }

    public function calendar($year = NULL, $month = NULL) {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');

        $date_from = date('Y-m-01', strtotime("$year-$month-01"));
        $date_to = date('Y-m-t', strtotime("$year-$month-01"));

        $month_name = strftime('%B', strtotime("$year-$month-01"));

        $sessions_by_date = [];
        $current_date = $date_from;
        while ($current_date <= $date_to) {
            $sessions_by_date[$current_date] = []; 
            $current_date = date('Y-m-d', strtotime($current_date . ' + 1 day'));
        }

        $sessions = $this->SessionModel->getSessions($date_from, $date_to);
        foreach ($sessions as $session) {
            $sessions_by_date[$session->date][] = $session;
        }

        $weeks = array();
        $current_date = $date_from;
        while (date('N', strtotime($current_date)) != 1) {
            $current_date = date('Y-m-d', strtotime($current_date . ' - 1 day'));
        }
        while ($current_date <= $date_to) {
            $week = array();
            for ($i = 0; $i < 7; $i++) {
                if (date('Y-m', strtotime($current_date)) === date('Y-m', strtotime($date_from))) {
                    $week[date('j', strtotime($current_date))] = $sessions_by_date[date('Y-m-d', strtotime($current_date))];
                }
                $current_date = date('Y-m-d', strtotime($current_date . ' + 1 day'));
            }
            $weeks[] = $week;
        }

        $this->setData('weeks', $weeks);
        $this->setData('sessions_by_date', $sessions_by_date);
        $this->setData('total_sessions', count($sessions));

        $this->setData('header_title', "Partidas de $month_name de $year");
        $this->setData('header_title_mobile', ucfirst("$month_name $year"));
        $this->setData('prev_month', date('Y/m', strtotime("$year-$month-01 -1 month")));
        $this->setData('prev_month_name', ucfirst(strftime('%B', strtotime("$year-$month-01 -1 month"))));
        $this->setData('next_month', date('Y/m', strtotime("$year-$month-01 +1 month")));
        $this->setData('next_month_name', ucfirst(strftime('%B', strtotime("$year-$month-01 +1 month"))));

        $this->setTitle("Partidas de $month_name de $year");
        return $this->loadView('sessions/calendar');
    }

    public function view($uid) {
        $session = $this->SessionModel->getSession($uid);
        $adventure = $this->AdventureModel->getAdventure($session->adventure_uid);
        $players = $this->SessionModel->getSessionPlayers($session->uid);

        $session->joined = false;
        foreach ($players as $player) {
            if ($player->uid === session('user_uid')) {
                $session->joined = $player->character_uid;
            }

            if (!$adventure->rank || $adventure->rank == rank_get($player->level)) {
                $player->badge_color = 'success';
            } else if ($adventure->rank > rank_get($player->level)) {
                $player->badge_color = 'warning';
            } else {
                $player->badge_color = 'danger';
            }
        }

        $players = [
            'playing' => array_pad(array_slice($players, 0, $session->players_max), $session->players_max, NULL),
            'waitlist' => array_slice($players, $session->players_max),
        ];

        $this->setData('session', $session);
        $this->setData('adventure', $adventure);
        $this->setData('players', $players);
        $this->setData('characters', $this->CharacterModel->getPlayerCharacters(session('user_uid')));

        $this->setTitle('Información de partida ' . $adventure->name);
        return $this->loadView('sessions/session');
    }

    public function join() {
        $session_uid = $this->request->getVar('session-uid');
        $player_uid = session('user_uid');
        $character_uid = $this->request->getVar('character-uid');

        $this->SessionModel->addPlayerSession([
            'session_uid' => $session_uid,
            'player_uid' => $player_uid,
            'player_character_uid' => $character_uid,
        ]);

        session()->setFlashdata('success', 'Te has anotado a una partida');
        session()->setFlashdata('session_updated', $session_uid);
        return redirect()->back();
    }

    public function swap() {
        $session_uid = $this->request->getVar('session-uid');
        $player_uid = session('user_uid');
        $character_uid = $this->request->getVar('character-uid');

        $this->SessionModel->updatePlayerSession($session_uid, $player_uid, [
            'player_character_uid' => $character_uid,
        ]);

        session()->setFlashdata('success', 'Se ha cambiado el personaje con el que estabas anotado');
        session()->setFlashdata('session_updated', $session_uid);
        return redirect()->back();

    }

    public function cancel() {
        $session_uid = $this->request->getVar('session-uid');
        $player_uid = session('user_uid');

        $this->SessionModel->deletePlayerSession($session_uid, $player_uid);

        session()->setFlashdata('success', 'Se ha cancelado tu inscripción.');
        session()->setFlashdata('session_updated', $session_uid);
        return redirect()->back();
    }

}