$('a[id-token]').on('click', function(){
    const idToken = $(this).attr('id-token');
    const nameNature = $(this).parents('tr').find('td:eq(1)').text();
    $('#deleteNature #formDelete input[name="idToken"]').val(idToken);
    $('#deleteNature #formDelete h6 strong.nature').text(nameNature);

    $('#deleteNature').modal();
    
    return false;
});