// Variáveis global
var finalCostumer   = 0;
var clientState     = 0;

$(document).ready(function(){
    $('#quantity, #value_sale, #discount, #shipping, #insurance, #others_expenses, #daily_charges, #ipi, #icms, #discount_general, input[id*="value_p_"]').maskMoney({thousands:'.', decimal:',', allowZero:true});
    
    if($('.table-products tbody tr').length !== 0){
        $('#shipping').val(parseFloat($('#shipping').val()).toFixed(2).replace('.', ','));
        $('#insurance').val(parseFloat($('#insurance').val()).toFixed(2).replace('.', ','));
        $('#others_expenses').val(parseFloat($('#others_expenses').val()).toFixed(2).replace('.', ','));
        $('#daily_charges').val(parseFloat($('#daily_charges').val()).toFixed(2).replace('.', ','));

        $('#value_icmsst').val(parseFloat($('#value_icmsst').val()).toFixed(2).replace('.', ','));
        $('#base_icmsst').val(parseFloat($('#base_icmsst').val()).toFixed(2).replace('.', ','));
        $('#value_icms').val(parseFloat($('#value_icms').val()).toFixed(2).replace('.', ','));
        $('#base_icms').val(parseFloat($('#base_icms').val()).toFixed(2).replace('.', ','));
        $('#value_ipi').val(parseFloat($('#value_ipi').val()).toFixed(2).replace('.', ','));

        $('#discount_general').val(parseFloat($('#discount_general').val()).toFixed(2).replace('.', ','));

        if($('#client').val() != "") $('#client').trigger('change');
        $('button.stepNext-1').attr('disabled', false);

        if($('#automatic_distribution').is(':checked')) $('input[name^="value_p_"]').attr('disabled', 'true');

        setTimeout(() => {
            updateInstallments();
            updateValuesLiquidAndGross(false);
            $('[data-toggle-click="tooltip"]').tooltip({trigger: 'click', placement: 'right'});
        }, 1000);
    }
});

/**
 * Update state of button, disabled/enable
 */
$('#client').change(function(){
    const cod_client = $(this).val();
    $('button[data-target="#products"]').attr('disabled', cod_client == "");
    $('button.stepNext-0').attr('disabled', cod_client == ""  );
    $('button[data-target="#formPayment"]').attr('disabled', ($('.table-products tbody tr').length === 0 || cod_client == ""));

    if(cod_client === "") return false;

    $.getJSON(`${window.location.origin}/admin/search/client/${cod_client}`, function(data) {
        finalCostumer = data.tipo_consumidor === "final" ? 0 : 1;
        clientState = data.uf;
        setTimeout(() => { recalculateProducts() }, 250);
    });
});

/**
 * Remove tooltip when clicking outside the element
 */
$(document).click(function(e){
    if(e.target.className !== "description") $('.tooltip').tooltip('hide');
    if($('.tooltip.show').length > 1){
        const idTooltipTarget = $(event.target).attr('aria-describedby');
        $('.tooltip.show').each(function(){
            if(idTooltipTarget != $(this).attr('id')) $(`#${$(this).attr('id')}`).tooltip('hide');
        })
    }
});

/**
 * Change product
 */
$('#product').on('select2:select select2:unselecting', function(){
    const value = $(this).val();

    // Verifica se existe valor na opção
    if(value == ""){
        clearInputProduct();
        return false;
    }

    searchProduct(value);

});

/**
 * Alter instalment of form payment
 */
$('#installment').change(function(){
    const installment = $(this).val();
    let installments = '';

    const value_disabled = $('#automatic_distribution').is(':checked') ? 'disabled' : '';

    for (let count = 1; count <= installment; count++) {
        days = (count - 1) * 30;
        installments += 
        `<div class="row installment_${count} col-md-12 no-padding">
            <div class="offset-md-2 col-md-2 title-installment">
                <div class="form-group">
                    <span>${count}ª Parcela</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    ${count === 1 ? '<label class="col-md-12 text-center" for="days_p_${count}">Dias</label>' : ''}
                    <input class="form-control" id="days_p_${count}" min="-999999" name="days_p_${count}" type="number" value="${days}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    ${count === 1 ? '<label class="col-md-12 text-center" for="date_p_${count}">Vencimento</label>' : ''}
                    <input class="form-control" id="date_p_${count}" name="date_p_${count}" type="date" value="${sumDaysDateNow(days)}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    ${count === 1 ? '<label class="col-md-12 text-center" for="value_p_${count}">Valor</label>' : ''}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><strong>R$</strong></span>
                        </div>
                        <input class="form-control" id="value_p_${count}" name="value_p_${count}" type="text" value="0,00" ${value_disabled}>
                    </div>
                </div>
            </div>
        </div>`;
    }

    $('.installments div[class*="installment_"]').remove();
    $('.installments').append(installments);

    $('input[id*="value_p_"]').maskMoney({thousands:'.', decimal:',', allowZero:true});

    recalculateInstallments();
});

/**
 * Change date by number of days
 */
$(document).on('keyup blur change', 'input[id*="days_p_"]', function(){
    let days = $(this).val();
    days == "" ? $(this).val(0) : $(this).val(parseInt(days));
    days = days == "" ? 0 : parseInt(days);

    const dataVctoFormatada = sumDaysDateNow(days);
    $(this).closest('.row').find('input[type="date"]').val(dataVctoFormatada);
});

/**
 * Add days by a date
 */
$(document).on('keyup blur change', 'input[id*="date_p_"]', function(){
    const dataVctoInput = $(this).val();
    if(dataVctoInput.length == 10){
        const dataVctoFormatada = dateNow();

        diasVcto = calculaDias(dataVctoFormatada, dataVctoInput);
        if(diasVcto < 0){
            diasVcto = 0;
            // $(this).val(dataVctoFormatada);
        }
        $(this).closest('.row').find('input[type="number"]').val(diasVcto);
    }
});

/**
 * Action of clear product insertion data entries
 */
$('.clear-input-product').click(function(){
    clearInputProduct();
});

/**
 * Action to add product to table
 */
$('.add-product-table').click(function(){

    let msgError = "";

    if($('#quantity').val() == "0,00") msgError = "Informe uma quantidade para adicionar.";
    if($('#value_sale').val() == "0,00") msgError = "Informe um valor para adicionar.";
    if($('#product').val() == "") msgError = "Selecione um produto para adicionar.";    

    if(msgError !== ""){
        Toast.fire({
            icon: 'error',
            title: msgError
        });
        return false;
    }

    addProductTable();
    
});

/**
 * Action to remove product to table
 */
$(document).on('click', '.table-products tbody tr i.fa-times', function(){
    $(this).closest('tr').remove();
    if($('.table-products tbody tr').length === 0){
        $('.products-list, .total-product-list').hide();
        $('.table-products #selectAll').prop('checked', false);
        $('button.stepNext-1').attr('disabled', true);
        $('button[data-target="#formPayment"]').attr('disabled', true);
    }
    updateValuesLiquidAndGross();
});

/**
 * Action to alter product to table
 */
$(document).on('click', '.table-products tbody tr i.fa-pencil-alt', function(){

    const el = $(this).closest('tr');

    const cod_product   = el.find('.custom-control-input').attr('id');
    const quantity      = el.find('.quantity').text();
    const value_sale    = el.find('.value_sale').text().replace('R$ ', '');
    const icms          = el.find('.icms').text().replace(' %', '');
    const ipi           = el.find('.valueIpiReal').text();
    const discount      = el.find('.discount').text().replace('R$ ', '');
    const haveSt        = el.find('.haveSt').text();
    const valueSt       = el.find('.valueStReal').text();
    
    searchProduct(cod_product, false);
    $('#product').val(cod_product).select2();
    $('#quantity').val(quantity);
    $('#value_sale').val(value_sale);
    $('#discount').val(discount);
    $('#icms').val(icms);
    $('#ipi').val(formatCurrencyBr(ipi));
    $('#haveSt').val(haveSt);
    $('#valueSt').val(valueSt);

    $(this).closest('tr').remove();
    if($('.table-products tbody tr').length === 0){
        $('.products-list, .total-product-list').hide();
        $('.table-products #selectAll').prop('checked', false);
        $('button.stepNext-1').attr('disabled', true);
        $('button[data-target="#formPayment"]').attr('disabled', true);
    }
    updateValuesLiquidAndGross();
});

/**
 * Go for next step of sale of product
 */
$('button[class*="stepNext"').click(function(){
    $(this).closest('.card').next().find('.collapse').collapse('show');
});

/**
 * Check and uncheck all products listed in table
 */
$('.table-products #selectAll').change(function(){
    if($(this).is(':checked')) $('.table-products .custom-control-input').prop('checked', true);
    if($(this).is(':not(:checked)')) $('.table-products .custom-control-input').prop('checked', false);
});

/**
 * Remove products marked
 */
$('.remove-checkeds').click(function(){
    if($('.table-products tbody tr .custom-control-input:checked').length === 0){
        Toast.fire({
            icon: 'error',
            title: 'Nenhum produto selecionado.'
        });
        return false;
    }
    $('.table-products tbody tr').each(function(){
        if($(this).find('input[type="checkbox"]').is(':checked')) $(this).remove();
    });
    if($('.table-products tbody tr').length === 0){
        $('.products-list, .total-product-list').hide();
        $('.table-products #selectAll').prop('checked', false);
    }
    updateValuesLiquidAndGross();
});

/**
 * Check and uncheck input with all checkboxs
 */
$(document).on('change', '.table-products .custom-control-input', function(){
    let checked = false;
    $('.table-products tbody tr').each(function(){
        if($(this).find('input[type="checkbox"]').is(':not(:checked)')) checked = true;
    });
    if(checked) $('.table-products #selectAll').prop('checked', false);
    if(!checked) $('.table-products #selectAll').prop('checked', true);
});

/**
 * Update values liquid and gross in keyup
 */
$('#shipping, #insurance, #others_expenses, #daily_charges, #discount_general').blur(function(){
    updateValuesLiquidAndGross();
});

/**
 * Check and uncheck automatic distribution, disabled and enable values installment
 */
$('#automatic_distribution').change(function(){
    recalculateInstallments();
    $('input[id*="value_p_"]').attr('disabled', $(this).is(':checked'));
});

/**
 * Update values sale
 */
$('.refreshValues').click(function(){
    updateInstallments();
    updateValuesLiquidAndGross(false);
});

/**
 * Block enter key not to submit form
 */
$(document).on('keypress', 'input', function(e) {
    if(e.which == 13) return false;
});

$('#client').change(function(){
});

/**
 * Update form payment with confirm
 * 
 * @return {null}
 */
const updateInstallments = () => {
    const liquid_value      = roundDecimal(getLiquidValueSale(), 2);
    const value_installment = roundDecimal(sumValuesTotalInstallment(), 2);

    if($('#automatic_distribution').is(':not(:checked)') && $("#installment").val() != 1 && liquid_value !== value_installment){
        const updateFormPayment = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });
        
        updateFormPayment.fire({
            title: 'Valores alterados, deseja recalcular as parcelas?',
            text: "Os valores das parcelas serão distribuídos proporcionalmente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Atualizar Parcelas',
            cancelButtonText: 'Não Atualizar Parcelas',
            reverseButtons: true
        }).then((result) => {
            if(result.value) recalculateInstallments();
        });
    }
    if($('#automatic_distribution').is(':checked') || $("#installment").val() == 1 || liquid_value === value_installment) recalculateInstallments();
}

/**
 * Sum values total installment
 * 
 * @return {float}
 */
const sumValuesTotalInstallment = () => {
    let count = 0;

    $('input[id*="value_p_"]').each(function(){
        count += formatCurrencyEn($(this).val());
    });

    return count;
}


/**
 * Verify quantity max available
 * 
 * @return {boolean}
 */
const verifyValueQuantity = () => {
    const maxStock   = $('#quantity').attr('max-stock');
    const valueTyped = formatCurrencyEn($('#quantity').val());

    if(valueTyped > maxStock){
        Toast.fire({
            icon: 'error',
            title: 'Quantidade informada é maior que a disponível em estoque.'
        });

        $('#quantity').focus();
        return true;
    }
    return false;
}

/**
 * Clear product insertion data entries
 * 
 * @return {null}
 */
const clearInputProduct = () =>{
    $('#haveSt, #valueSt').val(0);
    $('#quantity, #value_sale, #discount, #icms, #ipi').val('0,00');
    $('#qnt_stock, #table_price').html("&nbsp;");
    $('.unity_quantity').html("&nbsp;&nbsp;&nbsp;");
    $('#product').val($('#product option:first').val()).select2()
}

/**
 * Search quantity available in stock
 * 
 * @param   {string}    product
 * @return  {int}
 */
const searchStockAvailable = product => {
    let quantity = 0;
    $('.table-products tbody tr').each(function(){
        if($(this).find('input[type="checkbox"]').attr('id') == product)
            quantity += formatCurrencyEn($(this).find('.quantity').text().replace('R$ ', ''));
    });
    return quantity;
}

/**
 * Add products to table
 * 
 * @return {null}
 */
const addProductTable = () =>{
    const nameProduct   = $('#product option:selected').text();
    const idProduct     = $('#product').val();
    const quantity      = $('#quantity').val();
    const value_sale    = $('#value_sale').val();
    const discount      = $('#discount').val();
    const icms          = $('#icms').val();
    const ipi           = $('#ipi').val();
    const haveSt        = $('#haveSt').val();
    const valueSt       = $('#valueSt').val();
    const icmsOrigem    = 17;

    if(verifyValueQuantity()) return false;

    // Adiciona na tabela
    if($(".products-list").is(':not(:visible)')) $(".products-list, .total-product-list").show(); // Mostra tabela caso esteja oculta
    createRowTable(idProduct, nameProduct, quantity, discount, value_sale, haveSt, icms, icmsOrigem, valueSt, ipi);

    setTimeout(() => {
        $('button.stepNext-1').attr('disabled', false);
        $('button[data-target="#formPayment"]').attr('disabled', false);
        $('[data-toggle-click="tooltip"]').tooltip({trigger: 'click', placement: 'right'});
        clearInputProduct();
        updateValuesLiquidAndGross();
    }, 150);
}

/**
 * Create row table product
 * 
 * @param   {string}    idProduct
 * @param   {string}    nameProduct
 * @param   {float}     quantity
 * @param   {float}     discount
 * @param   {float}     value_sale
 * @param   {int}       haveSt
 * @param   {float}     icms
 * @param   {float}     valueSt
 * @param   {float}     ipi
 * @return  {string}
 */
const createRowTable = async (idProduct, nameProduct, quantity, discount, value_sale, haveSt, icmsDestino, icmsOrigem, valueSt, ipi) => {

    value_sale  = parseFloat(formatCurrencyEn(value_sale));
    quantity    = parseFloat(formatCurrencyEn(quantity));
    icmsDestino = parseFloat(formatCurrencyEn(icmsDestino));
    ipi         = parseFloat(formatCurrencyEn(ipi));
    discount    = parseFloat(formatCurrencyEn(discount));
    valueSt     = parseFloat(valueSt);
    icmsOrigem  = parseFloat(icmsOrigem);

    let icmsSt = valueBaseSt = vlrIcmsDestino = baseIcmsDestino = valueIpi = 0;
    let amount  = (value_sale * quantity) - discount;

    
    if(haveSt == 1 && finalCostumer == 1 && icmsDestino != 0){

        returnCalc = calculateStItem(amount, icmsDestino, icmsOrigem, valueSt, ipi);

        baseIcmsDestino = returnCalc[0];
        vlrIcmsDestino  = returnCalc[1];
        valueIpi        = returnCalc[2];
        valueBaseSt     = returnCalc[3];
        icmsSt          = returnCalc[4];

    }

    amount += icmsSt; // Value liquid - product + icms

    const row = 
        `<tr>
            <td class="text-center">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="${idProduct}">
                    <label for="${idProduct}" class="custom-control-label"></label>
                </div>
            </td>
            <td class="description" data-toggle-click="tooltip" title='${nameProduct}'>${nameProduct}</td>
            <td class="quantity">${formatCurrencyBr(quantity)}</td>
            <td class="text-right value_sale">R$ ${formatCurrencyBr(value_sale)}</td>
            <td class="icmsst">R$ ${formatCurrencyBr(icmsSt)}</td>
            <td class="icms">${formatCurrencyBr(icmsDestino)} %</td>
            <td class="ipi">R$ ${formatCurrencyBr(valueIpi)}</td>
            <td class="text-right discount">R$ ${formatCurrencyBr(discount)}</td>
            <td class="text-right amount">R$ ${formatCurrencyBr(amount)}</td>
            <td class="d-flex justify-content-around pt-1 action no-padding mt-1"><i class="text-red fa fa-times"></i><i class="text-orange fa fa-pencil-alt"></i></td>
            
            
            <td class="d-none haveSt">${haveSt}</td>
            <td class="d-none valueSt">${roundDecimal(icmsSt, 2)}</td>
            <td class="d-none valueBaseSt">${roundDecimal(valueBaseSt, 2)}</td>
            <td class="d-none valueIcms">${roundDecimal(vlrIcmsDestino, 2)}</td>
            <td class="d-none valueBaseIcms">${roundDecimal(baseIcmsDestino, 2)}</td>
            <td class="d-none valueStReal">${valueSt}</td>
            <td class="d-none valueIpiReal">${ipi}</td>
        </tr>`;
        
    $(".table-products tbody").append(row);
}
const calculateStItem = (amount, icmsDestino, icmsOrigem, valueSt, ipi) => {
    
    const baseIcmsDestino = amount;
    const vlrIcmsDestino  = amount * (icmsDestino/100);
    const valueIpi        = amount * (ipi/100);
    const amountLiquid    = amount + valueIpi;

    const valueBaseSt   = amountLiquid * (1+(valueSt/100));
    const icmsSt        = (valueBaseSt * (icmsOrigem/100)) - vlrIcmsDestino;

    return [baseIcmsDestino, vlrIcmsDestino, valueIpi, valueBaseSt, icmsSt];
}

/**
 * Seach data of product and add inputs
 * 
 * @param   {string}    value
 * @param   {boolean}   fillAllFields
 * @return  {null}
 */
const searchProduct = (value, fillAllFields = true) => {

    $('#quantity, #value_sale, #discount, #icms, #ipi').closest('.form-group').append('<div class="overlay dark"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
    $('.clear-input-product, .add-product-table, #product').attr('disabled', true);

    $.getJSON(`${window.location.origin}/admin/search/product/${value}/${clientState}`, function(data) {
        
        data.amount -= searchStockAvailable(value);
        if(fillAllFields){
            $('#quantity').val(data.amount == 0 ? '0,00' : data.amount > 0 && data.amount < 1 ? formatCurrencyBr(data.amount) : '1,00');
            $('#value_sale').val(formatCurrencyBr(data.price));
            $('#discount').val('0,00');
            $('#icms').val(formatCurrencyBr(data.icms));
            $('#ipi').val(formatCurrencyBr(data.ipi_saida));
            $('#haveSt').val(data.subst_trib);
            $('#valueSt').val(data.lucro_pres);
        }
        $('#qnt_stock').text(`ESTOQUE: ${formatCurrencyBr(data.amount)} ${data.unity}`);
        $('.unity_quantity').text(data.unity);
        $('#table_price').text(`TABELA: ${data.description}`);
        $('#quantity').attr('max-stock', data.amount);
        $('#quantity, #value_sale, #discount, #icms, #ipi').closest('.form-group').find('.overlay').remove();
        $('.clear-input-product, .add-product-table, #product').attr('disabled', false);
    });
}

/**
 * Sum total value products
 * 
 * @return {float}
 */
const sumTotalValueProducts = () => {
    let value       = 0;
    let value_sale  = 0;
    let quantity    = 0;
    let discount    = 0;

    $('.table-products tbody tr').each(function(){
        value_sale = formatCurrencyEn($(this).find('.value_sale').text().replace('R$ ', ''));
        quantity = formatCurrencyEn($(this).find('.quantity').text());
        discount = formatCurrencyEn($(this).find('.discount').text().replace('R$ ', ''));
        value += (value_sale - discount) * quantity;
    });

    return value;
}

/**
 * Sum total value expenses
 * 
 * @return {float}
 */
const sumTotalValueExpenses = () => {
    const shipping          = formatCurrencyEn($('#shipping').val());
    const insurance         = formatCurrencyEn($('#insurance').val());
    const others_expenses   = formatCurrencyEn($('#others_expenses').val());
    const daily_charges     = formatCurrencyEn($('#daily_charges').val());

    return shipping + insurance + others_expenses + daily_charges;
}

/**
 * Sum total value tax
 * 
 * @return {float}
 */
const sumTotalValueTax = () => {
    const value_icmsst    = formatCurrencyEn($('#value_icmsst').val());
    // const base_icms     = formatCurrencyEn($('#base_icms').val());
    // const value_ipi     = formatCurrencyEn($('#value_ipi').val());

    return value_icmsst
}

/**
 * Sum total value discount
 * 
 * @return {float}
 */
const sumTotalValueDiscount = () => {
    const discount_general = formatCurrencyEn($('#discount_general').val());

    return discount_general;
}

/**
 * Update values liquid and gross
 * 
 * @param   {boolean}   update
 * @return  {null}
 */
const updateValuesLiquidAndGross = (update = true) => {
    updateValuesTaxTotal();
    if(update) updateInstallments();

    const gross_value       = sumTotalValueProducts();
    const liquid_value      = getLiquidValueSale();

    $('.gross_value span').text(formatCurrencyBr(gross_value));
    $('.liquid_value span').text(formatCurrencyBr(liquid_value));
}

/**
 * Recalculate installments
 * 
 * @return {null}
 */
const recalculateInstallments = () => {
    
    let sumInstallmentValue = 0;
    const installment       = parseInt($("#installment").val());
    const liquid_value      = getLiquidValueSale();

    let installmentValue    = liquid_value / installment;

    for (count = 1; count <= installment; count++) {
        if(count == installment) installmentValue = liquid_value - sumInstallmentValue;

        sumInstallmentValue += roundDecimal((liquid_value / installment), 2);
        $(`#value_p_${count}`).val(formatCurrencyBr(installmentValue));
    }
}

/**
 * Get liquid value total sale
 * 
 * @return  {null}
 */
const getLiquidValueSale = () => {

    const gross_value       = sumTotalValueProducts();
    const discount_general  = sumTotalValueDiscount();
    const expenses          = sumTotalValueExpenses();
    const tax_value         = sumTotalValueTax();
   
    return gross_value - discount_general + expenses + tax_value;

}


/**
 * Update values taxin form payment
 * 
 * @return  {null}
 */
const updateValuesTaxTotal = () => {

    let baseIcmsSt  = 0;
    let valueIcmsSt = 0;
    let baseIcms    = 0;
    let valueIcms   = 0;
    let valueIpi    = 0;

    $('.table-products .valueBaseSt').each(function(){
        baseIcmsSt += parseFloat($(this).text());
    });
    $('.table-products .icmsst').each(function(){
        valueIcmsSt += parseFloat(formatCurrencyEn($(this).text().replace('R$ ', '')));
    });
    $('.table-products .ipi').each(function(){
        valueIpi += parseFloat(formatCurrencyEn($(this).text().replace('R$ ', '')));
    });
    $('.table-products .valueBaseIcms').each(function(){
        baseIcms += parseFloat($(this).text());
    });
    $('.table-products .valueIcms').each(function(){
        valueIcms += parseFloat($(this).text());
    });

    $('#base_icmsst').val(formatCurrencyBr(baseIcmsSt));
    $('#value_icmsst').val(formatCurrencyBr(valueIcmsSt));
    $('#value_ipi').val(formatCurrencyBr(valueIpi));
    $('#base_icms').val(formatCurrencyBr(baseIcms));
    $('#value_icms').val(formatCurrencyBr(valueIcms));
}

const recalculateProducts = async () => {
    $('.table-products tbody tr').each(function(){
        quantity        = formatCurrencyEn($('.quantity', this).text().replace("R$ ", ""));
        value_sale      = formatCurrencyEn($('.value_sale', this).text().replace("R$ ", ""));
        icmsDestino     = formatCurrencyEn($('.icms', this).text().replace(" %", ""));
        ipi             = parseFloat($('.valueIpiReal', this).text());
        discount        = formatCurrencyEn($('.discount', this).text().replace("R$ ", ""));
        haveSt          = $('.haveSt', this).text();
        mva             = $('.valueStReal', this).text();
        icmsOrigem      = 17;

        amount          = value_sale * quantity - discount;

        icmsSt = valueBaseSt = vlrIcmsDestino = baseIcmsDestino = valueIpi = 0;

        if(haveSt == 1 && finalCostumer == 1 && mva != 0){
            returnCalc      = calculateStItem(amount, icmsDestino, icmsOrigem, mva, ipi);
            baseIcmsDestino = returnCalc[0];
            vlrIcmsDestino  = returnCalc[1];
            valueIpi        = returnCalc[2];
            valueBaseSt     = returnCalc[3];
            icmsSt          = returnCalc[4];
        }

        amount += icmsSt; // Value liquid - product + icms

        $('.icmsst', this).text('R$ ' + formatCurrencyBr(icmsSt));
        $('.amount', this).text('R$ ' + formatCurrencyBr(amount));
        $('.ipi', this).text('R$ ' + formatCurrencyBr(valueIpi));
        $('.valueIpiReal', this).text(ipi);

        $('.valueSt', this).text(roundDecimal(icmsSt, 2));
        $('.valueBaseSt', this).text(roundDecimal(valueBaseSt, 2));
        $('.valueIcms', this).text(roundDecimal(vlrIcmsDestino, 2));
        $('.valueBaseIcms', this).text(roundDecimal(baseIcmsDestino, 2));
    });
    updateValuesLiquidAndGross();
}

/**
 * Validate form for send
 */
$('#formRegister').submit(function(){
    const client            = $('#client').val();
    const products          = $('.table-products tbody tr').length;
    const installment       = parseInt($('#installment').val());
    const inputsVcto        = $(".installments input[id^='days_p_']");
    
    let msgError            = ""; // Mensagem de erro
    let vlrSomaParcela      = 0;
    let valorTotalLocacao   = getLiquidValueSale();
    let count_product       = 0;
    let controlerData       = -9999999;
    let vlrAtualDia         = 0;

    if(installment < 1 || installment > 12) msgError = "Selecione uma parcela válida"; // Verifica as parcelas
    if(products == 0) msgError = "Informe pelo menos um produto na venda"; // Verifica os produtos
    if(client == "") msgError = "Selecione o cliente corretamente"; // Verifica o cliente
    // Verifica se existe erro
    if(msgError != ""){
        Toast.fire({ icon: 'error', title: msgError });
        return false;
    }

    /**
     * Verifica se as datas foram informadas em ordem crescente
     */
    if(inputsVcto.length > 0){
        for (countVctoPgto = 0; countVctoPgto < inputsVcto.length; countVctoPgto++) {
            vlrAtualDia = parseInt(inputsVcto.eq(countVctoPgto).val());
            if(vlrAtualDia <= controlerData || isNaN(vlrAtualDia)){
                Toast.fire({ icon: 'error', title: "As datas de vencimento precisam ser informadas em ordem crescente!" });
                return false;
            }
            controlerData = vlrAtualDia;
        }
    }

    /**
     * Verifica se o valor total da venda corresponde ao valor total das parcelas
     */
    for (countParc = 1; countParc <= $(".installments input[id^='value_p_']").length; countParc++)
        vlrSomaParcela += formatCurrencyEn($(`#value_p_${countParc}`).val());

    vlrSomaParcela      = roundDecimal(vlrSomaParcela, 2); // arredonda para duas casas decimais
    valorTotalLocacao   = roundDecimal(valorTotalLocacao, 2); // arredonda para duas casas decimais
    // Verifica se existe diferença de valores entre o valor total da venda e a soma das parcelas
    if(valorTotalLocacao != vlrSomaParcela){
        Toast.fire({ icon: 'error', title: "Os valores das parcelas estão diferente do valor total!" });
        return false;
    }

    // Criando input desconhecidos
    $('form').append(`<input type="hidden" name="gross_value" value="${sumTotalValueProducts()}">`);
    $('form').append(`<input type="hidden" name="liquid_value" value="${getLiquidValueSale()}">`);

    $('input').attr('disabled', false);
    // Transforma valores de parcela em float
    $('input[name^="value_p_"]').each(function(){
        $(this).val(formatCurrencyEn($(this).val()));
    });
    $('input[name="discount_general"]').val(formatCurrencyEn($('input[name="discount_general"]').val()));
    $('input[name="value_ipi"]').val(formatCurrencyEn($('input[name="value_ipi"]').val()));
    $('input[name="base_icms"]').val(formatCurrencyEn($('input[name="base_icms"]').val()));
    $('input[name="value_icms"]').val(formatCurrencyEn($('input[name="value_icms"]').val()));
    $('input[name="base_icmsst"]').val(formatCurrencyEn($('input[name="base_icmsst"]').val()));
    $('input[name="value_icmsst"]').val(formatCurrencyEn($('input[name="value_icmsst"]').val()));
    $('input[name="daily_charges"]').val(formatCurrencyEn($('input[name="daily_charges"]').val()));
    $('input[name="others_expenses"]').val(formatCurrencyEn($('input[name="others_expenses"]').val()));
    $('input[name="insurance"]').val(formatCurrencyEn($('input[name="insurance"]').val()));
    $('input[name="shipping"]').val(formatCurrencyEn($('input[name="shipping"]').val()));

    $('.table-products tbody tr').each(function(){
        count_product++; // auto_increment

        cod_product     = $(".custom-control-input", this).attr("id");
        description     = $('.description', this).text().replace('"', "");
        quantity        = formatCurrencyEn($('.quantity', this).text().replace("R$ ", ""));
        value_sale      = formatCurrencyEn($('.value_sale', this).text().replace("R$ ", ""));
        icmsst          = formatCurrencyEn($('.icmsst', this).text().replace("R$ ", ""));
        icms            = formatCurrencyEn($('.icms', this).text().replace(" %", ""));
        ipi             = formatCurrencyEn($('.ipi', this).text().replace("R$ ", ""));
        ipi_perc        = parseFloat($('.valueIpiReal', this).text());
        discount        = formatCurrencyEn($('.discount', this).text().replace("R$ ", ""));
        amount          = formatCurrencyEn($('.amount', this).text().replace("R$ ", ""));
        haveSt          = $('.haveSt', this).text();
        valueSt         = $('.valueSt', this).text();
        valueBaseSt     = $('.valueBaseSt', this).text();
        valueIcms       = $('.valueIcms', this).text();
        valueBaseIcms   = $('.valueBaseIcms', this).text();
        valueStReal     = $('.valueStReal', this).text();

        $('form').append(`
            <input type="hidden" name="cod_product_${count_product}" value="${cod_product}">
            <input type="hidden" name="name_product_${count_product}" value="${description}">
            <input type="hidden" name="quantity_${count_product}" value="${quantity}">
            <input type="hidden" name="value_sale_${count_product}" value="${value_sale}">
            <input type="hidden" name="icmsst_${count_product}" value="${icmsst}">
            <input type="hidden" name="icms_${count_product}" value="${icms}">
            <input type="hidden" name="ipi_${count_product}" value="${ipi}">
            <input type="hidden" name="ipi_perc_${count_product}" value="${ipi_perc}">
            <input type="hidden" name="discount_${count_product}" value="${discount}">
            <input type="hidden" name="amount_${count_product}" value="${amount}">
            <input type="hidden" name="haveSt_${count_product}" value="${haveSt}">
            <input type="hidden" name="valueSt_${count_product}" value="${valueSt}">
            <input type="hidden" name="valueBaseSt_${count_product}" value="${valueBaseSt}">
            <input type="hidden" name="valueIcms_${count_product}" value="${valueIcms}">
            <input type="hidden" name="valueBaseIcms_${count_product}" value="${valueBaseIcms}">
            <input type="hidden" name="valueStReal_${count_product}" value="${valueStReal}">
        `);
    });
    $('form').append(`<input type="hidden" name="qnt_items" value="${count_product}">`);
    $('form').append(`<input type="hidden" name="id_update" value="${window.location.href.split('/').pop()}">`);
});