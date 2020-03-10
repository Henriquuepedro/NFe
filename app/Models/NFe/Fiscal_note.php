<?php

namespace App\Models\NFe;

use Illuminate\Database\Eloquent\Model;

class Fiscal_note extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cod_nf', 'cod_sale', 'cod_client', 'document_client', 'rg_ie_client', 'uf_client', 'date_emission', 'seq', 'name_nature', 'cod_nature', 'finality', 'qnty', 'specie', 'gross_weight', 'liquid_weight', 'shipping', 'gross_value', 'liquid_value', 'return_sefaz', 'status', 'key', 'protocolo', 'cod_user_reg'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at'];
}
