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

<form class="card" method="post" enctype="multipart/form-data">
    <div class="card-header text-center">
        <h2 class="d-inline-block mb-0">Nueva sesión</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="adventure">Aventura <span class="text-danger">*</span></label>
                    <select id="new-session-adventure" name="adventure" class="form-control" required>
                        <option value="__new">Nueva aventura</option>
                        <? foreach ($adventures as $adventure) : ?>
                            <option value="<?= $adventure->uid ?>"><?= $adventure->name ?> - <?= rank_full_text($adventure->rank) ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div id="adventure-form" style="display:block;">
                    <div class="form-group">
                        <label for="adventure_name">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="adventure_name" id="adventure_name" class="form-control js-required" required>
                    </div>
                    <div class="form-group">
                        <label for="adventure_rank">Rango</label>
                        <select name="adventure_rank" id="adventure_rank" class="form-control">
                            <option value=""><?= rank_full_text(0) ?></option>
                            <option value="1"><?= rank_full_text(1) ?></option>
                            <option value="2"><?= rank_full_text(2) ?></option>
                            <option value="3"><?= rank_full_text(3) ?></option>
                            <option value="4"><?= rank_full_text(4) ?></option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Número de jugadores recomendado</label>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="adventure_players_min_recommended">Mínimo <span class="text-danger">*</span></label>
                                <input type="text" name="adventure_players_min_recommended" id="adventure_players_min_recommended" value="<?= setting('default_min_players') ?>" class="form-control js-required" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="adventure_players_max_recommended">Máximo <span class="text-danger">*</span></label>
                                <input type="text" name="adventure_players_max_recommended" id="adventure_players_max_recommended" value="<?= setting('default_max_players') ?>" class="form-control js-required" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="adventure_duration">Duración <span class="text-danger">*</span></label>
                        <input type="text" name="adventure_duration" id="adventure_duration" class="form-control js-required" required>
                    </div>
                    <div class="form-group">
                        <label for="adventure_themes">Temas</label>
                        <input type="text" name="adventure_themes" id="adventure_themes" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="adventure_description">Descripción <span class="text-danger">*</span></label>
                        <textarea name="adventure_description" id="adventure_description" class="form-control js-required" rows="10" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="adventure_rewards">Recompensas</label>
                        <textarea name="adventure_rewards" id="adventure_rewards" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="adventure_thumbnail">Imagen (Tamaño máximo: <strong>50MB</strong>)</label>
                        <input type="file" name="adventure_thumbnail" id="adventure_thumbnail" class="form-control-file" accept="image/jpeg, image/png">
                    </div>
                </div>
                <div id="adventure-data" class="text-center" style="display:none;">
                    <img class="img-fluid" id="loaded-adventure-image">
                    <h2 id="loaded-adventure-title"></h2>
                    <h4 id="loaded-aventure-rank"></h4>
                    <p>
                        <div id="loaded-adventure-players"><strong>Jugadores: </strong> <span id="min"></span>-<span id="max"></span></div>
                        <div id="loaded-adventure-duration"><strong>Duración: </strong><span></span></div>
                        <div id="loaded-adventure-themes"><strong>Temas: </strong><span></span></div>
                    </p>
                    <p id="loaded-adventure-description"><i></i></p>
                    <p id="loaded-adventure-rewards"></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="session_master">Master <span class="text-danger">*</span></label>
                    <select id="session_master" name="session_master" class="form-control" required>
                        <? foreach ($masters as $master) : ?>
                            <option value="<?= $master->uid ?>" <?= $userdata['uid'] == $master->uid ? 'selected' : '' ?>><?= $master->display_name ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session_date">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="session_date" id="session_date" class="form-control" value="<?= date('Y-m-d', strtotime('tomorrow')) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session_time">Hora <span class="text-danger">*</span></label>
                            <input type="time" name="session_time" id="session_time" class="form-control" value="<?= setting('default_time') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="location">Ubicación <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="location" class="form-control" list="locations" required>
                            <datalist id="locations">
                                <? foreach ($locations as $location) : ?>
                                    <option value="<?= $location->location ?>">
                                <? endforeach; ?>
                            </datalist>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label>Número de jugadores</label>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session_min_players">Mínimo <span class="text-danger">*</span></label>
                            <input type="text" name="session_min_players" id="session_min_players" value="<?= setting('default_min_players') ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session_max_players">Máximo <span class="text-danger">*</span></label>
                            <input type="text" name="session_max_players" id="session_max_players" value="<?= setting('default_max_players') ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="published" id="published" class="form-check-input">
                                <label class="form-check-label" for="published">Publicar inmediatamente</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>
