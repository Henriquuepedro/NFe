<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NatureRequest extends FormRequest
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
        $complementUnique =  $this->get('token_update') !== null ? ", " . $this->get('id') : "";

        return [
            'description'       => "required|min:3|unique:natures,description{$complementUnique}",
            'consumerType'      => Rule::in([0, 1]),
            'cfop_state'        => 'nullable|min:4|max:4',
            'cfop_state_st'     => 'nullable|min:4|max:4',
            'cfop_no_state'     => 'nullable|min:4|max:4',
            'cfop_no_state_st'  => 'nullable|min:4|max:4'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
            'description.required'  => 'Informe o nome da natureza corretamente!',
            'description.min'       => 'O nome da natureza deve conter no mínimo 3 caracteres!',
            'description.unique'    => 'O nome da natureza já está em uso!',
            'consumerType.in'       => 'Selecione o tipo de consumidor corretamente!',
            'cfop_state.min'        => 'O CFOP dentro do estado deve conter 4 caracteres!',
            'cfop_state.max'        => 'O CFOP dentro do estado deve conter 4 caracteres!',
            'cfop_state_st.min'     => 'O CFOP dentro do estado com ST deve conter 4 caracteres!',
            'cfop_state_st.max'     => 'O CFOP dentro do estado com ST deve conter 4 caracteres!',
            'cfop_no_state.min'     => 'O CFOP fora do estado deve conter 4 caracteres!',
            'cfop_no_state.max'     => 'O CFOP fora do estado deve conter 4 caracteres!',
            'cfop_no_state_st.min'  => 'O CFOP fora do estado com ST deve conter 4 caracteres!',
            'cfop_no_state_st.max'  => 'O CFOP fora do estado com ST deve conter 4 caracteres!',
            
        ];
    }
}
