<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
        $arrayReturn = [
            "client"            => "required|exists:clients,id",
            "shipping"          => "required|min:0|numeric",
            "insurance"         => "required|min:0|numeric",
            "others_expenses"   => "required|min:0|numeric",
            "daily_charges"     => "required|min:0|numeric",
            "value_icmsst"      => "required|min:0|numeric",
            "base_icmsst"       => "required|min:0|numeric",
            "value_icms"        => "required|min:0|numeric",
            "base_icms"         => "required|min:0|numeric",
            "value_ipi"         => "required|min:0|numeric",
            "discount_general"  => "required|min:0|numeric",
            "installment"       => "required|min:1|numeric",
            "qnt_items"         => "required|min:1|integer",
            "gross_value"       => "required|min:0.01|numeric",
            "liquid_value"      => "required|min:0.01|numeric"
        ];
        for ($count=1; $count <= $this->get('qnt_items'); $count++) { 
            $arrayReturn["cod_product_$count"]      = "decodeProduct";
            $arrayReturn["quantity_$count"]         = "required|min:0.01|numeric";
            $arrayReturn["value_sale_$count"]       = "required|min:0.01|numeric";
            $arrayReturn["icmsst_$count"]           = "required|min:0|numeric";
            $arrayReturn["icms_$count"]             = "required|min:0|numeric";
            $arrayReturn["ipi_$count"]              = "required|min:0|numeric";
            $arrayReturn["discount_$count"]         = "required|min:0|numeric";
            $arrayReturn["amount_$count"]           = "required|min:0.01|numeric";
            $arrayReturn["haveSt_$count"]           = "required|min:0|integer";
            $arrayReturn["valueSt_$count"]          = "required|min:0|numeric";
            $arrayReturn["valueBaseSt_$count"]      = "required|min:0|numeric";
            $arrayReturn["valueIcms_$count"]        = "required|min:0|numeric";
            $arrayReturn["valueBaseIcms_$count"]    = "required|min:0|numeric";
            $arrayReturn["valueStReal_$count"]      = "required|min:0|numeric";
        }
        for ($count=1; $count <= $this->get('installment'); $count++) { 
            $arrayReturn["days_p_$count"]  = "required|min:0|integer";
            $arrayReturn["date_p_$count"]  = "required|date";
            $arrayReturn["value_p_$count"] = "required|min:0.01|numeric";
        }

        return $arrayReturn;
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $arrayReturn = [
            "client.*"              => "Ocorreu um problema na identificação do cliente, informe-o novamente!",
            "shipping.*"            => "Valor de frete informado incorreto, informe-o novamente!",
            "insurance.*"           => "Valor de seguro informado incorreto, informe-o novamente!",
            "others_expenses.*"     => "Valor de outras sespesas informado incorreto, informe-o novamente!",
            "daily_charges.*"       => "Valor de encargos diários informado incorreto, informe-o novamente!",
            "value_icmsst.*"        => "Valor de ICMS substituição informado incorreto, faça o recalculo deles!",
            "base_icmsst.*"         => "Valor de base ICMS substituição informado incorreto, faça o recalculo deles!",
            "value_icms.*"          => "Valor de ICMS informado incorreto, faça o recalculo deles!",
            "base_icms.*"           => "Valor de base ICMS informado incorreto, faça o recalculo deles!",
            "value_ipi.*"           => "Valor de IPI informado incorreto, faça o recalculo deles!",
            "discount_general.*"    => "Valor de desconto informado incorreto, faça o recalculo deles!",
            "installment.*"         => "Não foi possível recuperar a quantidade de parcelas, informe-os novamente!",
            "qnt_items.*"           => "Não foi possível recuperar a quantidade de produtos, informe-os novamente!",
            "gross_value.*"         => "Não foi possível recuperar o valor bruto, informe novamente!",
            "liquid_value.*"        => "Não foi possível recuperar o valor liquído, informe novamente!"
        ];
        for ($count=1; $count <= $this->get('qnt_items'); $count++) { 
            $arrayReturn["cod_product_$count.*"]      = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["quantity_$count.*"]         = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["value_sale_$count.*"]       = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["icmsst_$count.*"]           = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["icms_$count.*"]             = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["ipi_$count.*"]              = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["discount_$count.*"]         = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["amount_$count.*"]           = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["haveSt_$count.*"]           = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["valueSt_$count.*"]          = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["valueBaseSt_$count.*"]      = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["valueIcms_$count.*"]        = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["valueBaseIcms_$count.*"]    = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
            $arrayReturn["valueStReal_$count.*"]      = "Não foi possível reconhecer o {$count}º produto, adicione-o novamente!";
        }
        for ($count=1; $count <= $this->get('installment'); $count++) { 
            $arrayReturn["days_p_$count.*"]  = "A {$count}ª parcela contem informações inválidas, informe-a novamente";
            $arrayReturn["date_p_$count.*"]  = "A {$count}ª parcela contem informações inválidas, informe-a novamente";
            $arrayReturn["value_p_$count.*"] = "A {$count}ª parcela contem informações inválidas, informe-a novamente";
        }

        return $arrayReturn;
    }
}
