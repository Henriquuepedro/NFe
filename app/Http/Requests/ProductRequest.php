<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'description'       => 'required|string|min:3|unique:products,description',
            'reference'         => 'nullable|string',
            'bar_code'          => 'nullable|numeric',
            'unity'             => ['required', 'string', Rule::in(['UN', 'G', 'JOGO', 'LT', 'MWHORA', 'METRO', 'M3', 'M2', '1000UN', 'PARES', 'QUILAT', 'KG'])],
            'amount'            => 'string',
            'icms_1'            => 'required|string|min:4|max:6',
            'icms_2'            => 'required|string|min:4|max:6',
            'icms_3'            => 'required|string|min:4|max:6',
            'ipi_saida'         => 'required|string|min:4|max:6',
            'fcp'               => 'required|string|min:4|max:6',
            'lucro_pres'        => 'required|string|min:4|max:6',
            'inci_imposto'      => 'required|string|min:4|max:6',
            'imposto_impor'     => 'required|string|min:4|max:6',
            'ncm'               => 'nullable|string|min:10|max:10',
            'cest'              => 'nullable|string|min:9|max:9',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
            'description.required'  => 'Informe a descrição do produto corretamente!',
            'description.string'    => 'Informe a descrição do produto corretamente!',
            'description.min'       => 'A descrição do produto deve conter no mínimo 3 caracteres!',
            'description.unique'    => 'A descrição do produto já está em uso!',
            'reference.string'      => 'Informe a referência do produto corretamente!',
            'bar_code.numeric'      => 'O código de barras deve conter apenas números!',
            'unity.required'        => 'Selecione a unidade do produto corretamente!',
            'unity.string'          => 'Selecione a unidade do produto corretamente!',
            'unity.in'              => 'Selecione a unidade do produto corretamente!',
            'amount.string'         => 'Informe a quantidade do produto corretamente!',
            'icms_1.required'       => "Informe o ICMS de saída de MG / RJ / PR / SP / RS corretamente, caso não existir, informe 0,00%",
            'icms_1.string'         => "Informe o ICMS de saída de MG / RJ / PR / SP / RS corretamente, caso não existir, informe 0,00%",
            'icms_1.min'            => "Informe o ICMS de saída de MG / RJ / PR / SP / RS corretamente, informe um valor entre 0,00% e 100,00%",
            'icms_1.max'            => "Informe o ICMS de saída de MG / RJ / PR / SP / RS corretamente, informe um valor entre 0,00% e 100,00%",
            'icms_2.required'       => "Informe o ICMS de saída de dentro do estado corretamente, caso não existir, informe 0,00%",
            'icms_2.string'         => "Informe o ICMS de saída de dentro do estado corretamente, caso não existir, informe 0,00%",
            'icms_2.min'            => "Informe o ICMS de saída de dentro do estado corretamente, informe um valor entre 0,00% e 100,00%",
            'icms_2.max'            => "Informe o ICMS de saída de dentro do estado corretamente, informe um valor entre 0,00% e 100,00%",
            'icms_3.required'       => "Informe o ICMS de saída de outros estados corretamente, caso não existir, informe 0,00%",
            'icms_3.string'         => "Informe o ICMS de saída de outros estados corretamente, caso não existir, informe 0,00%",
            'icms_3.min'            => "Informe o ICMS de saída de outros estados corretamente, informe um valor entre 0,00% e 100,00%",
            'icms_3.max'            => "Informe o ICMS de saída de outros estados corretamente, informe um valor entre 0,00% e 100,00%",
            'ipi_saida.required'    => "Informe o IPI de saída corretamente, caso não existir, informe 0,00%",
            'ipi_saida.string'      => "Informe o IPI de saída corretamente, caso não existir, informe 0,00%",
            'ipi_saida.min'         => "Informe o IPI de saída corretamente, informe um valor entre 0,00% e 100,00%",
            'ipi_saida.max'         => "Informe o IPI de saída corretamente, informe um valor entre 0,00% e 100,00%",
            'fcp.required'          => "Informe o FCP corretamente, caso não existir, informe 0,00%",
            'fcp.string'            => "Informe o FCP corretamente, caso não existir, informe 0,00%",
            'fcp.min'               => "Informe o FCP corretamente, informe um valor entre 0,00% e 100,00%",
            'fcp.max'               => "Informe o FCP corretamente, informe um valor entre 0,00% e 100,00%",
            'lucro_pres.required'   => "Informe o Lucro Presumido corretamente, caso não existir, informe 0,00%",
            'lucro_pres.string'     => "Informe o Lucro Presumido corretamente, caso não existir, informe 0,00%",
            'lucro_pres.min'        => "Informe o Lucro Presumido corretamente, informe um valor entre 0,00% e 100,00%",
            'lucro_pres.max'        => "Informe o Lucro Presumido corretamente, informe um valor entre 0,00% e 100,00%",
            'inci_imposto.required' => "Informe o Incidência dos Impostos corretamente, caso não existir, informe 0,00%",
            'inci_imposto.string'   => "Informe o Incidência dos Impostos corretamente, caso não existir, informe 0,00%",
            'inci_imposto.min'      => "Informe o Incidência dos Impostos corretamente, informe um valor entre 0,00% e 100,00%",
            'inci_imposto.max'      => "Informe o Incidência dos Impostos corretamente, informe um valor entre 0,00% e 100,00%",
            'imposto_impor.required'=> "Informe o Imposto de Importação corretamente, caso não existir, informe 0,00%",
            'imposto_impor.string'  => "Informe o Imposto de Importação corretamente, caso não existir, informe 0,00%",
            'imposto_impor.min'     => "Informe o Imposto de Importação corretamente, informe um valor entre 0,00% e 100,00%",
            'imposto_impor.max'     => "Informe o Imposto de Importação corretamente, informe um valor entre 0,00% e 100,00%",
            'ncm.string'            => "Informe a Classificação Fiscal(NCM) corretamente",
            'ncm.min'               => "Informe a Classificação Fiscal(NCM) corretamente, deve conter 8 caracteres!",
            'ncm.max'               => "Informe a Classificação Fiscal(NCM) corretamente, deve conter 8 caracteres!",
            'cest.string'           => "Informe a CEST corretamente",
            'cest.min'              => "Informe a CEST corretamente, deve conter 8 caracteres!",
            'cest.max'              => "Informe a CEST corretamente, deve conter 8 caracteres!"
        ];
    }
}
