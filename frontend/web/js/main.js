$(document).ready(function() {
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
                url: 'index.php?r=site/municipality',
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
                        $("#signupform-subdivision_id").append($(data));
                    }
                }
            });
        }
    }

    $( "#signupform-subject_id" ).change(function()
    {
        changeValues();
    });

    $( "#signupform-agency_id" ).change(function()
    {
        changeValues();
    });
});
