$(document).ready(function() {

    (function ($) {
        "use strict";   
        var $currentTr = $('tr').first();
        $('body').on('click', 'tr', function (e) {
            $currentTr.removeClass('selected');
            $currentTr = $(this);
            $currentTr.addClass('selected');
        });
    })(jQuery);

    /* Ведомости */
    $('#modalVedCreate').click(function () {
        $('#modalVed').modal('show')
            .find('#modalVedContent')
            .load($(this).attr('value'));
    });

    $('#modalVedExtDocCreate').click(function () {
        $('#modalVedExtDoc').modal('show')
            .find('#modalVedExtDocContent')
            .load($(this).attr('value'));
    });

    /* Муниципальное образование (отдел) */
    function changeValues() {
        if($("select#signupform-subject_id").val() != '' && $("select#signupform-agency_id").val() != '')
        {
            $.ajax(
            {
                type: 'GET',
                url: '/site/municipality',
                data: 'subject_id=' + $("select#signupform-subject_id").val() + '&agency_id=' + $("select#signupform-agency_id").val(),
                success: function(data)
                {
                    if (data == 0)
                    {
                        //alert('Данные отсутствуют.');
                        $("#signupform-subdivision_id").empty();
                        $("#signupform-subdivision_id").append( $('<option value="">Нет данных</option>'));
                    }
                    else
                    {
                        //alert('Данные получены.');
                        $("#signupform-subdivision_id").empty();
                        $("#signupform-address_id").empty();
                        $("#signupform-subdivision_id").append("<option disabled selected>Выберите или введите название</option>");
                        $("#signupform-subdivision_id").append($(data));
                    }
                }
            });
        }
    }

    /* Список адресов */
    function changeAddress() {
        if($("select#signupform-subdivision_id").val() != null) {
            $.ajax(
            {
                type: 'GET',
                url: '/site/address',
                data: 'subdivision_id=' + $("select#signupform-subdivision_id").val(),
                success: function(data)
                {
                    if (data == 0)
                    {
                        //alert('Данные отсутствуют.');
                        $("#signupform-address_id").empty();
                        $("#signupform-address_id").append( $('<option value="">Нет данных</option>'));
                    }
                    else
                    {
                        //alert('Данные получены.');
                        $("#signupform-address_id").empty();
                        $("#signupform-address_id").append("<option disabled selected>Выберите адрес</option>");
                        $("#signupform-address_id").append($(data));
                    }
                }
            });
        }
    }

    $( "#signupform-subject_id" ).change(function() {
        changeValues();
    });

    $( "#signupform-agency_id" ).change(function() {
        changeValues();
    });

    $( "#signupform-subdivision_id" ).change(function() {
        changeAddress();
    });

});
