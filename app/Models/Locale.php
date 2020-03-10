<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{ 
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['place', 'number', 'complement', 'district', 'cod_ibge_city', 'cep'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    
    /**
     * Update information in the locales database
     *
     * @param array $dataLocale
     * @param int $codLocale
     * @return boolean
     */
    public function edit(array $dataLocale, int $codLocale)
    {
        return $this->where('id', $codLocale)->update($dataLocale);
    }
    
}