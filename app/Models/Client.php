<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['razao_social', 'fantasia', 'cnpj_cpf', 'rg_ie', 'im', 'cod_locale', 'telefone', 'celular', 'email', 'tipo_consumidor', 'situacao_tributaria', 'tipo_cliente', 'cpf_consumidor_final'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Update information in the clients database
     *
     * @param array $dataClient
     * @param int $codClient
     * @return boolean
     */
    public function edit(array $dataClient, int $codClient)
    {
        return $this->where('id', $codClient)->update($dataClient);
    }

    /**
     * Get data client
     *
     * @param int $cod_client
     * @return array
     */
    public function getClient($cod_client)
    {
        return $this->where('id', $cod_client)->get()[0];
    }
}
