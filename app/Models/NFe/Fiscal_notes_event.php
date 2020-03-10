<?php

namespace App\Models\NFe;

use Illuminate\Database\Eloquent\Model;

class Fiscal_notes_event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cod_nf', 'data_cancela', 'motivo_cancela', 'seq', 'chave', 'protocolo', 'tipo_evento', 'cod_user_reg'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at'];
}
