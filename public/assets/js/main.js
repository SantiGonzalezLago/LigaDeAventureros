/*!
  * LigaDeAventureros
  * Copyright (C) 2023 Santiago González Lago

  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.

  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.

  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <https://www.gnu.org/licenses/>.
  */

function loadAdventure(adventure) {
    if (adventure.thumbnail) {
        $('#loaded-adventure-image').attr('src', baseUrl + "img/adventures/" + adventure.thumbnail);
        $('#loaded-adventure-image').attr('alt', adventure.name);
        $('#loaded-adventure-image').show(); 
    } else {
        $('#loaded-adventure-image').hide();
    }
    $('#loaded-adventure-title').text(adventure.name);
    $('#loaded-aventure-rank').text(adventure.rank_text);
    $('#loaded-adventure-players #min').text(adventure.players_min_recommended);
    $('#session_min_players').val(adventure.players_min_recommended);
    $('#loaded-adventure-players #max').text(adventure.players_max_recommended);
    $('#session_max_players').val(adventure.players_max_recommended);
    $('#loaded-adventure-duration span').text(adventure.duration);
    if (adventure.themes) {
        $('#loaded-adventure-themes span').text(adventure.themes);
        $('#loaded-adventure-themes').show(); 
    } else {
        $('#loaded-adventure-themes').hide();
    }
    $('#loaded-adventure-description i').html(adventure.description.replace(/\r\n/g, '<br>'));
    if (adventure.rewards) {
        $('#loaded-adventure-rewards').html(adventure.rewards.replace(/\r\n/g, '<br>'));
        $('#loaded-adventure-rewards').show(); 
    } else {
        $('#loaded-adventure-rewards').hide();
    }
}

function preloadJoinSession() {
    $('.js-select-join-session').on('change', function() {
        let session_uid = $(this).data('session-uid');
        let adventure_name = $(this).data('adventure-name');
        let joined = $(this).data('joined');
        let adventure_rank = $(this).data('adventure-rank');

        let char_uid = $(this).val();
        let char_name = $(this).find('option:selected').data('char-name');
        let char_rank = $(this).find('option:selected').data('rank');
    
        if (char_uid == '__cancel') {
            var modal = '#cancel-inscription-modal';
        } else if (joined) {
            var modal = '#swap-inscription-modal';
        } else {
            var modal = '#join-inscription-modal';
        }

        $(`${modal} #adventure-name`).text(adventure_name);
        $(`${modal} .js-char-name`).text(char_name);
        $(`${modal} #adventure-rank`).text(adventure_rank);
        $(`${modal} #char-rank`).text(char_rank);
        if (adventure_rank == 'Todos' || adventure_rank == char_rank) {
            $(`${modal} .alert`).hide();
        } else {
            $(`${modal} .alert`).show();
        }

        $(`${modal} #session-uid`).val(session_uid);
        $(`${modal} #char-uid`).val(char_uid);

        let element = $(this).attr('id');
        $(modal).modal('show');
        $(modal).on('hidden.bs.modal', function () {
            var option = joined || '__default';
            $(`#${element}`).val(option);
        });

    });
}

$(function() {
    $('[data-toggle="tooltip"]').tooltip();

    $('.js-validate-btn').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        
        $('#validate-sheet-modal #modal-character-name').text(name);
        $('#validate-sheet-modal #modal-uid').val(uid);

        $('#validate-sheet-modal').modal('show');
    });

    $('.js-reject-btn').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");

        $('#reject-sheet-modal #modal-character-name').text(name);
        $('#reject-sheet-modal #modal-uid').val(uid);

        $('#reject-sheet-modal').modal('show');
    });

    $('.js-update-character-btn').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        let cclass = $(this).data("class");
        let level = $(this).data("level");
        
        $("#modal-character-name").text(name);
        $("#update-character-modal #uid").val(uid);
        $("#update-character-modal #class").val(cclass);
        $("#update-character-modal #level").val(level);

        $('#update-character-modal').modal('show');
    });

    $('#new-session-adventure').on('change', function() {
        let uid = $(this).val();
        if (uid == '__new') {
            $('.js-required').prop('required', true);
            $('#adventure-data').hide();
            $('#adventure-form').show();
        } else {
            $('.js-required').prop('required', false);
            $('#adventure-form').hide();
            $.ajax({
                method: "POST",
                url: baseUrl + "adventure/data-ajax",
                dataType:'json',
                data: {
                    uid: uid,
                },
                success: function(data) {
                    if (data.error) {
                        alert('Se ha producido un error al cargar la aventura seleccionada.');
                        $('#new-session-adventure').val('__new').trigger('change');
                    } else {
                        loadAdventure(data.adventure);
                        $('#adventure-data').show();
                    }
                }  
            });
        }
    });

    $('#adventure_players_min_recommended').on('keyup', function() {
        $('#session_min_players').val($(this).val());
    });

    $('#adventure_players_max_recommended').on('keyup', function() {
        $('#session_max_players').val($(this).val());
    });

    preloadJoinSession();

    $('.js-adventure-info').on('click', function() {
        let uid = $(this).data('uid');
        $.ajax({
            method: "POST",
            url: baseUrl + "adventure/data-ajax",
            dataType:'json',
            data: {
                uid: uid,
            },
            success: function(data) {
                if (!data.error) {
                    loadAdventure(data.adventure);
                    $('#adventure-info-modal').modal('show');
                }
            }  
        });
    });

    $('.js-setting-input').on('keyup', function() {
        let id = $(this).data('id');
        let btn = `#button-${id}`;
        $(btn).prop('disabled', false);
    });

    $('.js-master-add').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        
        $("#master-add-modal #user-name").text(name);
        $("#master-add-modal #uid").val(uid);

        $('#master-add-modal').modal('show');
    });

    $('.js-master-rm').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        
        $("#master-rm-modal #user-name").text(name);
        $("#master-rm-modal #uid").val(uid);

        $('#master-rm-modal').modal('show');
    });

    $('.js-admin-add').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        
        $("#admin-add-modal #user-name").text(name);
        $("#admin-add-modal #uid").val(uid);

        $('#admin-add-modal').modal('show');
    });

    $('.js-admin-rm').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        
        $("#admin-rm-modal #user-name").text(name);
        $("#admin-rm-modal #uid").val(uid);

        $('#admin-rm-modal').modal('show');
    });

    $('.js-ban').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        
        $("#ban-modal #user-name").text(name);
        $("#ban-modal #uid").val(uid);

        $('#ban-modal').modal('show');
    });

    $('.js-unban').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        
        $("#unban-modal #user-name").text(name);
        $("#unban-modal #uid").val(uid);

        $('#unban-modal').modal('show');
    });

    $('#checkbox-select-all').on('click', function() {
        $('.js-checkbox-select-all').prop('checked', this.checked);
    });

    $('.js-checkbox-select-all').on('click', function() {
        var totalCheckboxes = $('.js-checkbox-select-all').length;
        var checkedCheckboxes = $('.js-checkbox-select-all:checked').length;

        $('#checkbox-select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#checkbox-select-all').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    $('.js-session-rm').on('click', function() {
        let uid = $(this).data("uid");
        $("#session-rm-modal #uid").val(uid);
        $('#session-rm-modal').modal('show');
    });

    $('.js-session-kick').on('click', function() {
        let uid = $(this).data("uid");
        let name = $(this).data("name");
        $("#session-kick-modal #player-uid").val(uid);
        $("#session-kick-modal #character-name").text(name);
        $('#session-kick-modal').modal('show');
    });


    $('.js-calendar-load-session').on('click', function() {
        let uid = $(this).data("uid");
        $('#session-data').hide();
        $.ajax({
            method: "GET",
            url: baseUrl + "session/view/" + uid,
            success: function(data) {
                $('#session-data').html(data);
                $('#session-data').show();
                preloadJoinSession();
            }  
        });
    });

    $('#all-characters-search').on('keyup', function() {
        let search = $(this).val().toLowerCase();
        let searchWords = search.split(/\s+/).filter(word => word.trim() !== '');

        if (searchWords.length === 0) {
            $('.js-all-characters-search').show();
            return;
        }
        
        $('.js-all-characters-search').each(function() {
            let query = $(this).data('query');
            let queryWords = query.split(/\s+/).filter(word => word.trim() !== '');

            let matchFound = false;
            for (let i = 0; i < searchWords.length; i++) {
                for (let j = 0; j < queryWords.length; j++) {
                    if (queryWords[j].includes(searchWords[i])) {
                        matchFound = true;
                        break;
                    }
                }
                if (matchFound) {
                    break;
                }
            }

            if (matchFound) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});