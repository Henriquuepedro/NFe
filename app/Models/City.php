<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    private $city;

    public function __construct(){}
    
    /**
     * Seach city for code ibge
     *
     * @param  int $ibge
     * @return array
     */
    public function searchCodeSateForIbge($ibge)
    {
        return $this->where('codigo_ibge', $ibge)->get()[0];
    }
    

    /**
     * Search citys for state(code UF)
     *
     * @param  int $uf
     * @return array
     */
    public function searchCitysForState($uf)
    {
        return $this->where('codigo_uf', $uf)->get();
    }
}
