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
        razao_social: {
            required: true,
            minlength: 3
        },
        fantasia: {
            minlength: 3
        },
        im: {
            number: true
        },
        ie: {
            number: true
        },
        iest: {
            number: true
        },
        regime_trib: {
            required: true,
            number: true,
            max: 3
        },
        number_start_nfe: {
            number: true
        },
        logotipo: {
            extension: "jpg|jpeg|png|bmp"
        },
        certificado: {
            extension: "pfx"
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
        razao_social: {
            required: "O nome do cliente precisa ser preenchido!",
            minlength: "O nome do cliente deve conter no mínimo 3 caracteres!"
        },
        fantasia: {
            minlength: "A fantasia deve conter no mínimo 3 caracteres!"
        },
        im: {
            number: "A inscrição municipal deve conter apenas números!"
        },
        ie: {
            number: "A inscrição estadual deve conter apenas números!"
        },
        iest: {
            number: "A inscrição estadual do substituto tributário deve conter apenas números!"
        },
        regime_trib: {
            required: "Informe o regime tributário corretamente!",
            number: "Informe o regime tributário corretamente!",
            max: "Informe o regime tributário corretamente!"
        },
        number_start_nfe:{
            number: "O número de inicialização da NFe deve conter apenas números!"
        },
        logotipo: {
            extension: "As extensões permitidas do logotipo deve ser jpg, jpeg, png, bmp"
        },
        certificado: {
            extension: "A extensão permitida do certificado deve ser pfx"
        },
        cep: {
            rangelength: "Informe um CEP válido!",
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
        form.submit();
    }
});

const verifyAddress = () => {
    const verify = ($('#cep').val() != "" || $('#address').val() != "" || $('#addressNumber').val() != "" || $('#district').val() != "" || $('#city').val() != "" || $('#state').val() != "");

    if(!verify) $('#city, #state, #cep, #address, #addressNumber, #district').closest('.form-group').removeClass('has-error');

    return verify;
}
