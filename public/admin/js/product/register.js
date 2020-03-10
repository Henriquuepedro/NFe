$(document).ready(function(){
    $('#amount, #price, #icms_1, #icms_2, #icms_3, #ipi_saida, #fcp, #lucro_pres, #inci_imposto, #imposto_impor').maskMoney({thousands:'.', decimal:',', allowZero:true});
    // Verifica se existe tabela de preço
    if($('.tablePriceInput input').length > 0){
        let valueTablePrice;
        let idTablePrice;
        let nameTablePrice;
        $('.tablePriceInput input[name="tablesPrice[]"]').each(function(){
            idTablePrice    = parseInt($(this).val());
            valueTablePrice = $(`.tablePriceInput input[name="valuesPrice[]"][id-table-price="${idTablePrice}"]`).val();
            valueTablePrice = formatCurrencyBr(valueTablePrice);
            nameTablePrice  = $(`#tablesPrice  option[value="${idTablePrice}"]`).text();

            addRowTablePrice(idTablePrice, nameTablePrice, valueTablePrice);
        });

        $('.tablePriceList').show();
        functionsActionTablePrice();
    }
});

// Adicionar valor de venda
$('.addTablePrice').on('click', function(){
    let addTablePreco       = true; // Verifica erros no poscesso
    const el                = $(this).parents('.row');
    const value             = el.find('#price').val();
    const valueFormat       = value.replace('.', '').replace(',', '.');
    const tablePrice_name   = el.find('#tablesPrice option:selected').text();
    const tablePrice_id     = el.find('#tablesPrice').val();

    // Verifica valor zerado
    if(value === "0,00"){
        alert('É preciso informar um valor maior que R$ 0,00');
        return false;
    }

    // Verifica tabela de preço já inserida
    $('.tablePriceList tbody').each(function(){
        if($(this).find('tr').attr('id-table-price') === tablePrice_id){
            alert('Tabela de preço já existente, caso queira altera-lo clique no ícone para alterar o valor.');
            addTablePreco = false;
            return false;
        }
    });

    // Verifica se ocorreu algum erro
    if(!addTablePreco) return false;

    // Verifica se a tabela está oculta para mostra-la
    if($('.tablePriceList tbody tr').length == 0) $('.tablePriceList').show();

    // Adiciona valor na tabela
    addRowTablePrice(tablePrice_id, tablePrice_name, value);

    // Zera valor de venda e foca no campo novamente / seleciona novamente o primeiro item no select de tabela de preço
    $('#price').val('0,00').focus();
    $("#tablesPrice").val($("#tablesPrice option:eq(0)").val()).select2();

    // Adicionar input da tabela
    $('.tablePriceInput').append(`
        <input type="hidden" name="tablesPrice[]" value="${tablePrice_id}" id-table-price="${tablePrice_id}">
        <input type="hidden" name="valuesPrice[]" value="${valueFormat}" id-table-price="${tablePrice_id}">
    `);

    functionsActionTablePrice();
});

// Limpa erros
$('#formRegister input:submit').click(function(){
    $('.form-validate-error').empty();
});

// Validando dados do formulário
$("#formRegister").validate({
    errorLabelContainer: ".form-validate-error",
    wrapper: "li",
    focusInvalid: false,
    highlight: element => {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: element =>{
        $(element).closest('.form-group').removeClass('has-error');
    },
    invalidHandler: () => $('html, body').animate({scrollTop:0}, 'slow'),
    rules: {
        description: {
            required: true,
            minlength: 3
        },
        bar_code: {
            number: true
        },
        unity: {
            required: true
        },
        ncm: {
            validateNcmCest: true
        },
        cest: {
            validateNcmCest: true
        }
    },
    messages:{
        description: {
            required: "É preciso informar uma descrição para o produto!",
            minlength: "A descrição do produto deve conter no mínimo 3 caracteres!"
        },
        bar_code: {
            number: "O código de barras deve conter apenas números!"
        },
        unity: {
            required: "Selecione um opção válida da unidade do produto!",
        },
        ncm: {
            validateNcmCest: "Informe um valor de NCM válido!"
        },
        cest: {
            validateNcmCest: "Informe um valor de CEST válido!"
        }
    },
    submitHandler: function(form) {
        $('form').append(`<input type="hidden" name="id" value="${window.location.href.split('/').pop()}">`);

        // Verifica se existe tabela de preço
        if($('.tablePriceList tbody tr').length == 0){
            $('.form-validate-error').show()
                .append('<li><label>É preciso conter pelo menos, uma tabela de preço para o produto!</label></li>');

            $('html, body').animate({scrollTop:0}, 'slow');
            return false;
        }

        form.submit();
    }
});

/**
 * Function callback validate NCM and CEST number
 *
 * @return {boolean}
 */
jQuery.validator.addMethod("validateNcmCest", function(value, element) {
    let lengthVerific = 0;
    if(element.id === "ncm") lengthVerific = 8;
    if(element.id === "cest") lengthVerific = 7;

    let valor = value.replace(/\./g, '');
    return valor.length === lengthVerific || value === "";
});

/**
 * Auxiliary function for triggering price list functions (anonymous function)
 *
 * @param   {null}
 * @return  {null}
 *
 */
const functionsActionTablePrice = () => {
    // Remover valor da tabela de preço
    $('.tablePriceList tbody i.fa-times').on('click', function(){
        const idTablePrice = $(this).parents('tr').attr('id-table-price');
        $(`input[id-table-price=${idTablePrice}]`).remove();
        $(this).parents('tr').remove();

        if($('.tablePriceList tbody tr').length == 0) $('.tablePriceList').hide();
    });

    // Alterar valor da tabela de preço
    $('.tablePriceList tbody i.fa-pencil-alt').on('click', function(){
        const el = $(this).parents('tr');
        const value = el.find("td:eq(1)").text().replace('R$ ', '');
        const tablePrice = parseInt(el.attr('id-table-price'));

        $('#price').val(value).focus();
        $('#tablesPrice').val(tablePrice).select2();

        el.remove();
        $(`input[id-table-price=${tablePrice}]`).remove();

        if($('.tablePriceList tbody tr').length == 0) $('.tablePriceList').hide();
    });
}
const addRowTablePrice = (id, name, value) => {
    $('.tablePriceList tbody').append(`
        <tr id-table-price="${id}">
            <td>${name}</td>
            <td>R$ ${value}</td>
            <td><i class="fa fa-times text-danger"></i><i class="fa fa-pencil-alt text-warning"></i></td>
        </tr>
    `);
}
