$('a[id-token]').on('click', function(){
    const idToken = $(this).attr('id-token');
    const codSale = $(this).parents('tr').find('td:eq(0)').text();
    const client = $(this).parents('tr').find('td:eq(1)').text();
    $('#deleteSale #formDelete input[name="idToken"]').val(idToken);
    $('#deleteSale #formDelete h6 strong.cod_sale').text(codSale);
    $('#deleteSale #formDelete h6 strong.name_client').text(client);

    $('#deleteSale').modal();

    return false;
});
