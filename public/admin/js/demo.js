$(document).ready(function(){
    // Remover overflow do carregamento da página
    setTimeout(() => { $('.overflow-creen-load').remove() }, 1000);
    // Inicia datatable2
    if($('.dataTables').length > 0) {
        $('.dataTables').DataTable({
            "language": {
                "url": `${window.location.origin}/admin/file/json/dataTables.pt-BR.json`
            },
            "order": $('#tableSales').length ? [[ 0, "desc" ]] : [[ 0, "asc" ]]
        });
    }

    // Inicia select2
    if($('.select2').length > 0) $('.select2').select2();

    // Inicia check swith
    if($("input[data-bootstrap-switch]").length > 0)
        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'))
        });

    // Inicia tooltipe
    if($('[data-toggle="tooltip"]').length > 0) $('[data-toggle="tooltip"]').tooltip();
    if($('[dt-toggle="tooltip"]').length > 0) $('[dt-toggle="tooltip"]').tooltip();

});

    // Method Toast - Sweet Alert
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timerProgressBar: true,
        timer: 7000,
        onOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
//---------------------------------------------------------------------------------------------------------------------
// BUSCA ENDEÇO PELO CEP
$(document).on("click", ".cep-input-icon div", function(){
    const element = $(this).parents(".card-body");
    const cep = element.find('#cep').val().replace(/[^\d]+/g, '');

    if(cep.length == 8){
        $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, result => {
            if(!result.erro){
                const address   = result.logradouro;
                const district  = result.bairro;
                const state     = result.uf;
                const city      = result.ibge;
                const citys     = searchCitysOfState(state, city);

                element.find('#state').val(state).trigger('change');
                element.find('#address').val(address);
                element.find('#district').val(district);

                $('form #city').html(citys);
            }
            if(result.erro){
                alert( "CEP inválido ou inexistente!");
            }
        });
    }
    else alert( "CEP inválido ou inexistente!");

});
//---------------------------------------------------------------------------------------------------------------------
// Busca cidades do estado selecionado
$('#state').on('change', function(){

    const state = $(this).val();
    const citys = searchCitysOfState(state);

    $('form #city').html(citys);

});
//---------------------------------------------------------------------------------------------------------------------
// Consulta as cidades do estado passado
const searchCitysOfState = (state, city = "") => {

    let options = '<option value="">SELECIONE</option>';

    if(state === undefined || state === "") return options;

    $('form #city').prop('disabled', true);

    $.ajax({
        url: `${window.location.origin}/admin/search/citys/${state}`,
        method: "GET",
        async: false,
        dataType: 'json',
        success: result => {

            for (let countCity = 0; countCity < result.length; countCity++) {
                selected = city == result[countCity].codigo_ibge ? " selected" : "";
                options += `<option${selected} value="${result[countCity].codigo_ibge}">${result[countCity].nome}</option>`;
            }

            $('form #city').prop('disabled', false);

        }
    });

    return options;
}
//---------------------------------------------------------------------------------------------------------------------
/**
 *
 * Validate CNPJ and CPF
 * @param {string} val
 * @returns {boolean}
 *
 */
const validaCnpjCpf = val => {
    val = val.replace(/[^\d]+/g, '');

    if (val == '') return true;
    if (/^(\d)\1+$/.test(val)) return false;
    if (val.length != 14 && val.length != 11) return false;
    if (val.length == 14) return validCnpj(val);
    if (val.length == 11) return validCpf(val);
}
/**
 *
 * Validate CPF
 * @param {string} cpf
 * @returns {boolean}
 *
 */
const validCpf = cpf => {
    let soma = 0
    let resto;
    for (let i = 1; i <= 9; i++) soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i)

    resto = (soma * 10) % 11
    if ((resto == 10) || (resto == 11)) resto = 0
    if (resto != parseInt(cpf.substring(9, 10)) ) return false

    soma = 0
    for (let i = 1; i <= 10; i++) soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i)

    resto = (soma * 10) % 11
    if ((resto == 10) || (resto == 11))  resto = 0
    if (resto != parseInt(cpf.substring(10, 11) ) ) return false

    return true
}
/**
 *
 * Validate CNPJ
 * @param {string} cnpj
 * @returns {boolean}
 *
 */
const validCnpj  = cnpj => {
    let tamanho = cnpj.length - 2
    let numeros = cnpj.substring(0,tamanho)
    const digitos = cnpj.substring(tamanho)
    let soma = 0
    let pos = tamanho - 7
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--
        if (pos < 2) pos = 9
    }
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
    if (resultado != digitos.charAt(0)) return false;
    tamanho += 1
    numeros = cnpj.substring(0,tamanho)
    soma = 0
    pos = tamanho - 7
    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--
        if (pos < 2) pos = 9
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11
    if (resultado != digitos.charAt(1)) return false

    return true;
}
//---------------------------------------------------------------------------------------------------------------------
/**
 *
 *
 * Functions Jquery Validation with Select2
 *
 */

//If the change event fires we want to see if the form validates.
//But we don't want to check before the form has been submitted by the user
//initially.
$('.select2').on('change', function () {
    $(this).valid();
});
//---------------------------------------------------------------------------------------------------------------------
// Remove alerta
$(document).on('click', '[delete-alert]', function(){
    $(this).parents('.alert').slideUp('slow')
});

/**
 * Function replace number for currency br
 *
 * @param  {string}  currency
 * @return {string}
 *
 */
const formatCurrencyBr = currency => {
    currency = roundDecimal(currency, 2).toFixed(2).split('.');
    currency[0] = currency[0].split(/(?=(?:...)*$)/).join('.');
    return currency.join(',');
}

/**
 * Function replace number for currency en
 *
 * @param  {string}  currency
 * @return {string}
 *
 */
const formatCurrencyEn = currency => {
    currency = currency.replace('.', '');
    currency = currency.replace(',', '.');
    return parseFloat(currency);
}

/**
 * Add days to current date
 *
 * @param  {int}  days
 * @return {string}
 *
 */
const sumDaysDateNow = days => {
    let dataAtual = new Date();
    dataAtual.setDate(dataAtual.getDate() + days);

    const ano = dataAtual.getFullYear();
    const mes = dataAtual.getMonth() + 1 <= 9 ? "0" + (dataAtual.getMonth() + 1) : (dataAtual.getMonth() + 1);
    const dia = dataAtual.getDate() <= 9 ? "0" + dataAtual.getDate() : dataAtual.getDate();
    return `${ano}-${mes}-${dia}`;
}

/**
 * Return current date
 *
 * @param  {null}
 * @return {string}
 *
 */
const dateNow = () => {
    const dataAtual = new Date();

    const ano = dataAtual.getFullYear();
    const mes = dataAtual.getMonth() + 1 <= 9 ? "0" + (dataAtual.getMonth() + 1) : (dataAtual.getMonth() + 1);
    const dia = dataAtual.getDate() <= 9 ? "0" + dataAtual.getDate() : dataAtual.getDate();
    return `${ano}-${mes}-${dia}`;
}

/**
 * Find number of days between two dates
 *
 * @param  {string} date1
 * @param  {string} date2
 * @return {string}
 *
 */
const calculaDias = (date1, date2) => {
    moment.locale('pt-br');
    const data1 = moment(date1,'YYYY-MM-DD');
    const data2 = moment(date2,'YYYY-MM-DD');
    const diff  = data2.diff(data1, 'days');
    return diff;
}


/**
 * Round number to decimal places
 *
 * @param  {float}  num
 * @param  {int}    decimalPlace
 * @return {float}
 *
 */
const roundDecimal = (num, decimalPlace) => {
    numberDecimal = "1";
    for (let i = 0; i < decimalPlace; i++) numberDecimal += "0";

    return Math.round(num * numberDecimal) / numberDecimal
}
