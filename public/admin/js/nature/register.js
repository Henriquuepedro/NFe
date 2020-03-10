$(document).ready(function(){
    $('#cfop_state, #cfop_state_st, #cfop_no_state, #cfop_no_state_st').mask('0000');
});

// Validando dados do formulário
$("#formRegister").validate({
    errorLabelContainer: ".form-validate-error",
    wrapper: "li",
    focusInvalid: false,
    highlight: element => {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: element => {
        $(element).closest('.form-group').removeClass('has-error');
    },
    invalidHandler: () => $('html, body').animate({scrollTop:0}, 'slow'),
    rules: {
        description: {
            required: true,
            minlength: 3
        },
        cfop_state: {
            validadeCfop: true
        },
        cfop_state_st: {
            validadeCfop: true
        },
        cfop_no_state: {
            validadeCfop: true
        },
        cfop_no_state_st: {
            validadeCfop: true
        }
    },
    messages:{
        description: {
            required: "É preciso informar uma descrição para o produto!",
            minlength: "A descrição do produto deve conter no mínimo 3 caracteres!"
        },
        cfop_state: {
            validadeCfop: "Informe um valor de CFOP dentro do estado válido!"
        },
        cfop_state_st: {
            validadeCfop: "Informe um valor de CFOP dentro do estado com ST válido!"
        },
        cfop_no_state: {
            validadeCfop: "Informe um valor de CFOP fora do estado válido!"
        },
        cfop_no_state_st: {
            validadeCfop: "Informe um valor de CFOP fora do estado com ST válido!"
        }
    },
    submitHandler: form => {
        $('form').append(`<input type="hidden" name="id" value="${window.location.href.split('/').pop()}">`);
        
        form.submit();
    }
});

/**
 * Function callback validate CFOP number
 * 
 * @return {boolean}
 */
jQuery.validator.addMethod("validadeCfop", value => {
    return (value.length === 0 || value.length === 4) && (value === "" ? true : typeof parseInt(value) === "number");
});
