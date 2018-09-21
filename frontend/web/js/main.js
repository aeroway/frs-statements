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

});
