<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Format phone number
     *
     * @param  string $value
     * @param  boolean|false  $viewEmpty
     * @return string
     */
    public function formatPhone($value, $viewEmpty = false)
    {
        if($value == "" && $viewEmpty) $tel = "Não Informado";
        if($value == "" && !$viewEmpty) $tel = "";
        elseif((strlen($value) < 10 || strlen($value) > 11) && strlen($value) != 0) return false;
        elseif(strlen($value) == 10) $tel = preg_replace("/([0-9]{2})([0-9]{4})([0-9]{4})/", "($1) $2-$3", $value);
        elseif(strlen($value) == 11) $tel = preg_replace("/([0-9]{2})([0-9]{5})([0-9]{4})/", "($1) $2-$3", $value);
        return $tel;
    }


    /**
     * Format CNPJ or CPF document
     *
     * @param  string $value
     * @param  boolean|false  $viewEmpty
     * @return string
     */
    public function formatDoc($value, $viewEmpty = false)
    {
        if($value == "" && $viewEmpty) $identidade = "Não Informado";
        if($value == "" && !$viewEmpty) $identidade = "";
        elseif(strlen($value) != 11 && strlen($value) != 14 && strlen($value) != 0) return false;
        elseif(strlen($value) == 11) $identidade = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})/", "$1.$2.$3-$4", $value);
        elseif(strlen($value) == 14) $identidade = preg_replace("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", "$1.$2.$3/$4-$5", $value);
        return $identidade;
    }


    /**
     * Format CEP address
     *
     * @param  string $value
     * @param  boolean|false  $viewEmpty
     * @return string
     */
    public function formatCep($value, $viewEmpty = false)
    {
        if($value == "" && $viewEmpty) $cep = "Não Informado";
        elseif(strlen($value) != 8) return false;
        elseif(strlen($value) == 8) $cep = preg_replace("/([0-9]{2})([0-9]{3})([0-9]{3})/", "$1.$2-$3", $value);
        return $cep;
    }


    /**
     * Format Date and DateTime
     *
     * @param  string $value
     * @param  boolean|false  $viewEmpty
     * @return string
     */
    public function formatDateTime($value, $viewEmpty = false)
    {
        if($value == "" && $viewEmpty) $dataTime = "Não Informado";
        elseif(strlen($value) != 10 && strlen($value) != 19) return false;

        if(strlen($value) == 10) $dataTime = date('d/m/Y', strtotime($value));
        if(strlen($value) == 19) $dataTime   = date('d/m/Y H:i', strtotime($value));

        return $dataTime;
    }


    /**
     * Format number in real currency
     *
     * @param  string $value
     * @param  boolean|false  $viewEmpty
     * @return string
     */
    public function formatCurrency($value, $viewEmpty = false)
    {
        $valor = "";
        if($value == "" && $viewEmpty) $valor = "Não Informado";
        if($value != "") $valor = number_format($value, 2, ',', '.');
        return $valor;
    }

    /**
     * Sanitize value for float
     *
     * @param string $value
     * @return float
     */
    public function sanitizeNumberBr(string $value)
    {
        if(!strstr($value, ',')) return (float)$value;

        $value = str_replace(".", "", $value);
        $value = number_format((float)str_replace(",", ".", $value), 2, '.', '');
        return $value;
    }

    /**
     * Format key NF-e - 0000 1111 2222 3333 ...
     *
     * @param $key
     * @return string|string[]|null
     */
    public function formatKeyNFe($key)
    {
        if(strlen($key) !== 44) return "Chave inválida!";

        return preg_replace("/([0-9]{4})([0-9]{4})([0-9]{4})([0-9]{4})([0-9]{4})([0-9]{4})([0-9]{4})([0-9]{4})([0-9]{4})([0-9]{4})([0-9]{4})/", "$1 $2 $3 $4 $5 $6 $7 $8 $9 $10 $11", $key);
    }

    /**
     * Create paste if not exist, local in storage
     *
     * @param string $path
     * @param string $permission
     */
    public static function createPasteNotExistStorage(string $path, string $permission = "777")
    {
        if(!file_exists(storage_path($path)))
            mkdir(storage_path($path), $permission);
    }
}
