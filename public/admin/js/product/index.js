$('a[id-token]').on('click', function(){
    const idToken = $(this).attr('id-token');
    const nameProduct = $(this).parents('tr').find('td:first').text();
    $('#deleteProduct #formDelete input[name="idToken"]').val(idToken);
    $('#deleteProduct #formDelete h6 strong.product').text(nameProduct);

    $('#deleteProduct').modal();
    
    return false;
});