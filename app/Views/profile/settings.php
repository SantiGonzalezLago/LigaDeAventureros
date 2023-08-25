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
?>

<form class="col-md-6 offset-md-3" method="post">
    <div class="col-12">
        <div class="form-group">
            <label for="display_name">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="display_name" id="display_name" class="form-control" value="<?= $user->display_name ?>" required>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</form> 