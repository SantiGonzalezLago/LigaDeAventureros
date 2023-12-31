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

function uid_generate(): string {
    $bytes = random_bytes(8);
    $base64 = base64_encode($bytes);
    return rtrim(strtr($base64, '+/', '-_'), '=');
}

function uid_generate_unique($table): string {
    $valid_uid = false;
    while (!$valid_uid) {
        $uid = uid_generate();
        $db = \Config\Database::connect();
        $row = $db->table($table)->where('uid',$uid)->get()->getRow();
        if (!$row) {
            $valid_uid = true;
        }
    }
    return $uid;
}