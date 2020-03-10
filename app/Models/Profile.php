<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['razao_social', 'fantasia', 'cnpj', 'ie', 'im', 'cnae', 'regime_trib', 'logotipo', 'path_certificado', 'pass_certificado', 'cep', 'place', 'number', 'complement', 'district', 'cod_ibge_city', 'cod_user_reg', 'cod_user_alt', 'created_at', 'updated_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Update information in the profile database
     *
     * @param array $dataProfile
     * @param int $codProfile
     * @param string $cnpjProfile
     * @return boolean
     */
    public function edit(array $dataProfile, int $codProfile, string $cnpjProfile)
    {
        return $this->where([['id', '=', $codProfile], ['cnpj', '=', $cnpjProfile]])->update($dataProfile);
    }
}
