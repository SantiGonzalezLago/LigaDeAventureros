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

class Profile extends BaseController {

    public function __construct() {
        $this->UserModel = model('UserModel');
        $this->EmailSettingModel = model('EmailSettingModel');
        $this->CharacterModel = model('CharacterModel');
    }

    public function index($uid = NULL) {
        if (!$uid) {
            if ($this->isUserLoggedIn()) {
                $uid = session('user_uid');
            } else {
                return redirect()->to('/');
            }
        }
        $user = $this->UserModel->getUser($uid);

        if (!$user) {
            session()->setFlashdata('error', 'No se ha encontrado el usuario.');
            return redirect()->to(base_url());
        }

        $this->setData('user', $user);
        $this->setData('characters', $this->CharacterModel->getPlayerCharacters($uid));
        $this->setData('isOwner', $user->uid == session('user_uid'));
        $this->setTitle('Perfil de ' . $user->display_name);
        return $this->loadView('profile/view');
    }

    public function settings() {
        if (!$this->getUserData()) {
            session()->setFlashdata('error', 'No has iniciado sesión.');
            return redirect()->to(base_url());
        }

        $user = $this->UserModel->getUser(session('user_uid'));

        $this->setData('user', $user);
        $this->setData('email_settings', $this->EmailSettingModel->getUserSettings(session('user_uid')));
        $this->setData('tab', $this->request->getVar('tab') ?: 'settings');
        $this->setTitle('Configuración');
        return $this->loadView('profile/settings');
    }

    public function settings_post() {
        $validation = \Config\Services::validation();
        $validation->setRule('display_name', 'nombre', 'trim|required');
        if ($this->request->getVar('password')) {
            $validation->setRule('repeat_password', 'repetir contraseña', 'trim|required|matches[password]');
        }

        if ($validation->withRequest($this->request)->run()) {
            $data = array(
                'display_name' => $this->request->getVar('display_name'),
            );
            if ($this->request->getVar('password')) {
                $data['password'] = password_hash($this->request->getVar('password'), PASSWORD_BCRYPT);
            }
            $this->UserModel->updateUser(session('user_uid'), $data);
            session()->setFlashdata('success', 'Configuración actualizada.');
            return redirect()->to('settings');
        } else {
            session()->setFlashdata('error', 'Se ha producido un error al guardar la configuración.');
            session()->setFlashdata('validation_errors', $validation->getErrors());
            return redirect()->back();
        }
    }

    public function settings_email() {
        $input_email_settings = $this->request->getVar('email_settings') ?: [];
        $db_email_settings = $this->EmailSettingModel->getUserSettings(session('user_uid'));

        $add_email_settings = array_diff($input_email_settings, $db_email_settings);
        $delete_email_settings = array_diff($db_email_settings, $input_email_settings);

        $added = $this->EmailSettingModel->addSettings(session('user_uid'), $add_email_settings);
        $deleted = $this->EmailSettingModel->deleteSettings(session('user_uid'), $delete_email_settings);

        if ($added + $deleted > 0) {
            session()->setFlashdata('success', 'Se han actualizado tus opciones de email.');
        } else {
            session()->setFlashdata('error', 'No has modificado ninguna opción.');
        }
        
        return redirect()->to('settings?tab=email');
    }

    public function new_character() {
        $validation = \Config\Services::validation();
        $validation->setRule('name', 'nombre', 'trim|required');
        $validation->setRule('class', 'clase', 'trim|required');
        $validation->setRule('level', 'Nivel', 'trim|required|greater_than_equal_to[1]|less_than_equal_to[20]');
        $validation->setRule('character_sheet', 'Hoja de personaje', 'uploaded[character_sheet]|ext_in[character_sheet,pdf]|max_size[character_sheet,5120]');

        if ($validation->withRequest($this->request)->run()) {
            $uid = uid_generate_unique('player_character');
            $data = array(
                'uid'            => $uid,
                'user_uid'       => session('user_uid'),
                'name'           => $this->request->getVar('name'),
                'class'          => $this->request->getVar('class'),
                'level'          => $this->request->getVar('level'),
                'uploaded_sheet' => $uid . '_' . $this->request->getVar('level') . '_' . date('YmdHis') . '.pdf',
                'date_uploaded'  => date('c'),
                'active'         => 1,
            );

            $characterSheet = $this->request->getFile('character_sheet');
            $characterSheet->move(ROOTPATH . 'public/character_sheets', $data['uploaded_sheet']);
            upload_log('public/character_sheets', $data['uploaded_sheet']);

            $this->CharacterModel->addCharacter($data);
            session()->setFlashdata('success', 'Personaje creado correctamente.');
            return redirect()->to('profile/'.session('user_uid'));
        } else {
            session()->setFlashdata('error', 'No se ha podido crear el personaje.');
            session()->setFlashdata('validation_errors', $validation->getErrors());
            return redirect()->back();
        }
    }

    public function update_character() {
        $uid = $this->request->getVar('uid');
        $character = $this->CharacterModel->getCharacter($uid);

        if (!$character) {
            session()->setFlashdata('error', 'No se ha encontrado el personaje.');
            return redirect()->back();
        }

        if ($character->user_uid != session('user_uid')) {
            session()->setFlashdata('error', 'Ese personaje no te pertenece.');
            return redirect()->back();
        }

        $validation = \Config\Services::validation();
        $validation->setRule('class', 'clase', 'trim|required');
        $validation->setRule('level', 'Nivel', 'trim|required|greater_than_equal_to[1]|less_than_equal_to[20]');
        $validation->setRule('character_sheet', 'Hoja de personaje', 'uploaded[character_sheet]|ext_in[character_sheet,pdf]|max_size[character_sheet,5120]');

        if (!$validation->withRequest($this->request)->run()) {    
            session()->setFlashdata('error', 'No se ha podido actualizar el personaje.');
            session()->setFlashdata('validation_errors', $validation->getErrors());
            return redirect()->back();
        }
    
        $data = array(
            'class'          => $this->request->getVar('class'),
            'level'          => $this->request->getVar('level'),
            'uploaded_sheet' => $uid . '_' . $this->request->getVar('level') . '_' . date('YmdHis') . '.pdf',
            'date_uploaded'  => date('c'),
            'active'         => 1,
        );

        $characterSheet = $this->request->getFile('character_sheet');
        $characterSheet->move(ROOTPATH . 'public/character_sheets', $data['uploaded_sheet']);
        upload_log('public/character_sheets', $data['uploaded_sheet']);

        $this->CharacterModel->updateCharacter($uid, $data);
        session()->setFlashdata('success', 'Se ha actualizado el personaje ' . $character->name . '.');
        return redirect()->to('profile/'.session('user_uid'));
    }

    public function all_characters() {
        $characters = $this->CharacterModel->getAllActiveCharacters();
        $total = $this->CharacterModel->countAllActiveCharacters();;

        $this->setData('characters',$characters);
        $this->setData('total',$total);

        $this->setTitle('Todos los personajes');
        return $this->loadView('profile/all_characters');
    }

    public function delete_account() {
        $date = strtotime('+ 15 days');
        $this->email->confirm_delete_account(session('user_uid'));
        $this->UserModel->updateUser(session('user_uid'), [
            'delete_on' => date('Y-m-d', $date),
        ]);
        session()->destroy();
        return redirect()->to('/');
    }

}