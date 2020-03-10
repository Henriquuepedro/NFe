<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'razao_social'      => 'required|string|min:3',
            'fantasia'          => 'nullable|min:3',
            'im'                => 'nullable|numeric',
            'ie'                => 'nullable|numeric',
            'iest'              => 'nullable|numeric',
            'cnae'              => 'nullable|numeric',
            'regime_trib'       => 'max:3',
            'number_start_nfe'  => 'nullable|numeric',
            'cep'               => 'cep|required_with:address,addressNumber,complement,district,state,city',
            'address'           => 'nullable|required_with:cep,addressNumber,complement,district,state,city',
            'addressNumber'     => 'nullable|required_with:cep,address,complement,district,state,city',
            'district'          => 'nullable|required_with:cep,address,addressNumber,complement,state,city',
            'state'             => ['nullable', Rule::in(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']), 'required_with:cep,address,addressNumber,complement,district,city'],
            'city'              => 'nullable|numeric|required_with:cep,address,addressNumber,complement,district,state'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
            'razao_social.required'             => 'Informe a razão social corretamente!',
            'razao_social.string'               => 'Informe a razão social corretamente!',
            'razao_social.min'                  => 'a razão social deve conter no mínimo 3 caracteres!',
            'fantasia.min'                      => 'A fantasia deve conter no mínimo 3 caracteres!',
            'im.numeric'                        => 'A inscrição municipal deve conter apenas números!',
            'ie.numeric'                        => 'A inscrição estadual deve conter apenas números!',
            'iest.numeric'                      => 'A inscrição estadual do substituto tributário deve conter apenas números!',
            'cnae.numeric'                      => 'O CNAE deve conter apenas números!',
            'regime_trib.max'                   => 'Informe o regime tributário corretamente!',
            'number_start_nfe.numeric'          => 'O número de inicialização da NFe deve conter apenas números!',
            'cep.cep'                           => 'Informe o CEP corretamente!',
            'cep.required_with'                 => 'É preciso completar os dados do endereço, informe o CEP corretamente!',
            'address.required_with'             => 'É preciso completar os dados do endereço, informe o endereço corretamente!',
            'addressNumber.required_with'       => 'É preciso completar os dados do endereço, informe o número do endereço corretamente!',
            'district.required_with'            => 'É preciso completar os dados do endereço, informe o bairro corretamente!',
            'state.in'                          => 'Selecione um estado corretamente!',
            'state.required_with'               => 'É preciso completar os dados do endereço, informe o estado corretamente!',
            'city.required_with'                => 'É preciso completar os dados do endereço, informe a cidade corretamente!',
            'city.numeric'                      => 'Informe a cidade corretamente!'
        ];
    }
}
