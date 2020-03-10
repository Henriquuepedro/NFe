var init = true; // Variável de controle de inicialização da DOM

$(document).ready(function(){
    if($('[name="tipoPessoa"]:checked').length > 0){
        $('[name="tipoPessoa"]:checked').trigger('change');
    }
    if($('[name="cityOld"]').val() !== ""){
        const state = $('[name="state"]').val();
        const city  = $('[name="cityOld"]').val();

        const citys = searchCitysOfState(state, city);
        $('form #city').html(citys);
    }
});


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
    errorPlacement: function (error, element) {
        if (element.hasClass('select2-hidden-accessible')) {
            error.insertAfter(element.closest('.has-error').find('.select2'));
        } else if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    },
    rules: {
        tipoPessoa: {
            required: true
        },
        nameComplet: {
            required: true,
            minlength: 3
        },
        fantasia: {
            minlength: 3
        },
        documentCpfCnpj:{
            cpfCnpj: true
        },
        emial:{
            email: true
        },
        telephone:{
            rangelength: [14, 14]
        },
        cellPhone:{
            rangelength: [15, 15]
        },
        cep: {
            required: () => verifyAddress(),
            rangelength: [10, 10]
        },
        address: {
            minlength: 3,
            required: () => verifyAddress()
        },
        addressNumber: {
            required: () => verifyAddress()
        },
        complement: {
            minlength: 3
        },
        district: {
            minlength: 3,
            required: () => verifyAddress()
        },
        state: {
            required: () => verifyAddress()
        },
        city: {
            required: () => verifyAddress()
        }
    },
    messages:{
        tipoPessoa: {
            required: "Selecione um tipo de pessoa para esse cliente!"
        },
        nameComplet: {
            required: "O nome do cliente precisa ser preenchido!",
            minlength: "O nome do cliente deve conter no mínimo 3 caracteres!"
        },
        fantasia: {
            minlength: "A fantasia deve conter no mínimo 3 caracteres!"
        },
        documentCpfCnpj:{
            cpfCnpj: () => {
                const tipoPessoa = $('[name="tipoPessoa"]:checked').val();
                if(tipoPessoa === "pf") return "Informe um CPF válido!";
                if(tipoPessoa === "pj") return "Informe um CNPJ válido!";
            }
        },
        emial:{
            email: "Informe um e-mail válido!"
        },
        telephone:{
            rangelength: "Informe um número de telefone válido!"
        },
        cellPhone:{
            rangelength: "Informe um número de telefone válido!"
        },
        cep: {
            range: "Informe um CEP válido!",
            required: "Informe o CEP do endereço corretamente!"
        },
        address: {
            minlength: "O nome de endereço deve conter no mínimo 3 caracteres!",
            required: "Informe o nome do endereço corretamente!"
        },
        complement: {
            minlength: "O complemento deve conter no mínimo 3 caracteres!"
        },
        district: {
            minlength:  "O bairro do endereço deve conter no mínimo 3 caracteres!",
            required: "Informe o bairro do endereço corretamente!"
        },
        addressNumber: {
            required: "Informe o número do endereço corretamente!"
        },
        city: {
            required: "Informe a cidade do endereço corretamente!"
        },
        state: {
            required: "Informe o estado do endereço corretamente!"
        }
    },
    submitHandler: function(form) {
        $('form').append(`<input type="hidden" name="id" value="${window.location.href.split('/').pop()}">`);
        form.submit();
    }
});

// Validação de cnpj e cpf
jQuery.validator.addMethod('cpfCnpj', function (value) {
    return validaCnpjCpf(value);
}, 'Informe um número de documento válido!');

const verifyAddress = () => {
    const verify = ($('#cep').val() != "" || $('#address').val() != "" || $('#addressNumber').val() != "" || $('#district').val() != "" || $('#city').val() != "" || $('#state').val() != "");

    if(!verify) $('#city, #state, #cep, #address, #addressNumber, #district').closest('.form-group').removeClass('has-error');

    return verify;
}

// Alterar tipo de pessoa
$('[name="tipoPessoa"]').on('change', function(){
    const tipoPessoa = $(this).val();

    $('.formHidden').slideDown('slow');
    $('.select2').select2();

    if(tipoPessoa === "pj"){
        $('[for="nameComplet"]').text('Razão Social');
        $('[name="nameComplet"]').attr('placeholder', 'Insira a razão social');
        $('[name="fantasia"]').parents('.form-group').parent().show();
        $('[name="documentIm"]').parents('.form-group').parent().show();
        $('[name="documentCpfCnpj"]').parents('.form-group').parent().removeClass('col-md-3').addClass('col-md-4');
        $('[name="documentRgIe"]').parents('.form-group').parent().removeClass('col-md-3').addClass('col-md-4');
        $('[for="documentCpfCnpj"]').text('CNPJ');
        $('[name="documentCpfCnpj"]').mask('00.000.000/0000-00');
        $('[name="documentCpfCnpj"]').attr('placeholder', 'Insira o CNPJ');
        $('[for="documentRgIe"]').text('Inscrição Estadual (IE)');
        $('[name="documentRgIe"]').attr('placeholder', 'Insira a inscrição estadual');
        $('[name="consumerFinalCpf"]').parents('.form-group').parent().hide();

        if(!init) $('[name="documentCpfCnpj"], [name="documentRgIe"]').val('').trigger('blur');
    }
    if(tipoPessoa === "pf"){
        $('[for="nameComplet"]').text('Nome Completo');
        $('[name="nameComplet"]').attr('placeholder', 'Insira o nome completo');
        $('[name="fantasia"]').parents('.form-group').parent().hide();
        $('[name="documentIm"]').parents('.form-group').parent().hide();
        $('[name="documentCpfCnpj"]').parents('.form-group').parent().removeClass('col-md-4').addClass('col-md-3');
        $('[name="documentRgIe"]').parents('.form-group').parent().removeClass('col-md-4').addClass('col-md-3');
        $('[for="documentCpfCnpj"]').text('CPF');
        $('[name="documentCpfCnpj"]').mask('000.000.000-00');
        $('[name="documentCpfCnpj"]').attr('placeholder', 'Insira o CPF');
        $('[for="documentRgIe"]').text('Registro Geral (RG)');
        $('[name="documentRgIe"]').attr('placeholder', 'Insira o registro geral');
        $('[name="consumerFinalCpf"]').parents('.form-group').parent().show();

        if(!init) $('[name="documentCpfCnpj"], [name="documentRgIe"], [name="fantasia"], [name="documentIm"]').val('').trigger('blur');
    }
    setTimeout(() => { init = false }, 1000);

})
