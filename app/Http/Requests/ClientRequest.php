<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
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
            'tipoPessoa'        => ['size:2' , Rule::in(['pf', 'pj'])],
            'nameComplet'       => 'required|string|min:3|exists_razao_social',
            'fantasia'          => 'nullable|min:3',
            'documentCpfCnpj'   => 'nullable|unique_cpf_cnpj',
            'documentRgIe'      => 'nullable|numeric',
            'documentIm'        => 'nullable|numeric',
            'email'             => 'nullable|email',
            'telephone'         => 'nullable|telephone',
            'cellPhone'         => 'nullable|cellPhone',
            'consumerType'      => Rule::in(['final', 'nao_final']),
            'taxSituation'      => Rule::in(['nenhum', 'simples', 'lucro']),
            'consumerFinalCpf'  => ['nullable', Rule::in(['on'])],
            'cep'               => 'cep|required_with:address,addressNumber,complement,district,state,city',
            'address'           => 'nullable|string|required_with:cep,addressNumber,complement,district,state,city',
            'addressNumber'     => 'nullable|string|required_with:cep,address,complement,district,state,city',
            'complement'        => 'nullable|string',
            'district'          => 'nullable|string|required_with:cep,address,addressNumber,complement,state,city',
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
            'tipoPessoa.size'                   => 'Informe o tipo de pessoa corretamente!',
            'tipoPessoa.in'                     => 'Informe o tipo de pessoa corretamente!',
            'nameComplet.required'              => 'Informe o nome do cliente corretamente!',
            'nameComplet.string'                => 'Informe o nome do cliente corretamente!',
            'nameComplet.min'                   => 'O nome do cliente deve conter no mínimo 3 caracteres!',
            'nameComplet.exists_razao_social'   => 'O nome do cliente já está em uso!',
            'fantasia.min'                      => 'A fantasia do cliente deve conter no mínimo 3 caracteres!',
            'documentCpfCnpj.unique_cpf_cnpj'   => 'Documento de CNPJ/CPF já está em uso!',
            'documentRgIe.numeric'              => 'Documento de RG/IE deve conter apenas números!',
            'documentIm.numeric'                => 'Documento de IM deve conter apenas números!',
            'email.email'                       => 'Informe o e-mail corretamente!',
            'telephone.telephone'               => 'Informe o número de telefone corretamente!',
            'cellPhone.cellPhone'               => 'Informe o número de celular corretamente!',
            'consumerType.in'                   => 'Selecione o tipo de consumidor corretamente!',
            'taxSituation.in'                   => 'Selecione a situação tributária corretamente!',
            'consumerFinalCpf.in'               => 'Informe o CPF Consumidor Final corretamente!',
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
