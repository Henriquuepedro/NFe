$('a[id-token]').on('click', function(){
    const idToken = $(this).attr('id-token');
    const nameClient = $(this).parents('tr').find('td:first').text();
    $('#deleteClient #formDelete input[name="idToken"]').val(idToken);
    $('#deleteClient #formDelete h6 strong.client').text(nameClient);

    $('#deleteClient').modal();
    
    return false;
});