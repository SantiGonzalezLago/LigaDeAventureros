<div class="card">
    <div class="card-header text-center">
        <h3 class="d-inline-block mb-0"><?= $adventure->name ?></h1>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="text-center">
                    <p><strong><?= rank_full_text($adventure->rank) ?></strong></p>
                    <p>
                        <?= weekday(date('N', strtotime($session->date))) ?> <?= date('d', strtotime($session->date)) ?><br/>
                        <?= date('H:i', strtotime($session->time)) ?><br/>
                        <?= $session->location ?>
                    </p>
                    <p><a href="<?= base_url('profile') ?>/<?= $session->master_uid ?>"><?= $session->master ?></a></p>
                    <? if ($adventure->thumbnail) : ?>
                        <img class="img-fluid" src="<?= base_url('img/adventures') ?>/<?= $adventure->thumbnail ?>">
                    <? endif; ?>
                    <p>
                        <div><strong>Duración: </strong><?= $adventure->duration ?></div>
                        <div><strong>Temas: </strong><?= $adventure->themes ?></div>
                    </p>
                    <p><i><?= str_replace("\r\n", '<br>', $adventure->description) ?></i></p>
                    <p><?= str_replace("\r\n", '<br>', $adventure->rewards) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center text-secondary mb-2">De <?= $session->players_min ?> a <?= $session->players_max ?> jugadores</span>
                    </div>
                    <div class="col-md-6">
                        <div class="registered-players-block">
                            <? foreach ($players['playing'] as $player) : ?>
                                <? if ($player) : ?>
                                    <?= view('partials/user_badge', ['player' => $player]) ?>
                                <? else : ?>
                                    <span class="badge badge-secondary badge-player">------</span>
                                <? endif; ?>
                            <? endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="registered-players-block">
                            <? if ($players['waitlist']) : ?>
                                <h5 class="text-center text-secondary">Lista de espera</h5>
                                <? foreach ($players['waitlist'] as $player) : ?>
                                    <?= view('partials/user_badge', ['player' => $player]) ?>
                                <? endforeach; ?>
                            <? endif; ?>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <? if (strtotime($session->date . ' ' . $session->time) <= time()) : ?>
                            <div class="text-center text-secondary">Ya no te puedes anotar a esta partida</div>
                        <? elseif (!$characters) : ?>
                            <div class="text-center text-secondary"><a href="<?= base_url('profile') ?>">¡Crea un personaje!</a></div>
                        <? elseif (!$userdata['confirmed']) : ?>
                            <div class="text-center text-secondary">Necesitas verificación por parte de un master para poder anotarte.</div>
                        <? else : ?>
                            <select id="select-session-<?= $session->uid ?>" data-session-uid="<?= $session->uid ?>" data-adventure-name="<?= $adventure->name ?>" data-joined="<?= $session->joined ?>" data-adventure-rank="<?= rank_name($adventure->rank) ?>" class="js-select-join-session form-control">
                                <? if (!$session->joined) : ?>
                                    <option selected disabled value="__default">¡Anótate!</option>
                                <? endif; ?>
                                <? foreach ($characters as $char) : ?>
                                    <option value="<?= $char->uid ?>" data-rank="<?= rank_name(rank_get($char->level)) ?>" data-char-name="<?= $char->name ?>" <?= $char->uid == $session->joined ? 'selected disabled' : '' ?>><?= $session->joined ? ($char->uid == $session->joined ? 'Anotado con ' : 'Cambiar a ') : '' ?><?= $char->name ?></option>
                                <? endforeach; ?>
                                <? if ($session->joined) : ?>
                                    <option value="__cancel">CANCELAR INSCRIPCIÓN</option>
                                <? endif; ?>
                            </select>
                        <? endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('partials/modals/session_inscription') ?>