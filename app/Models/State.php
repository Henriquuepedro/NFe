<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public function __construct(){}

    public function getCodeUf($uf)
    {
        return $this->where('uf', $uf)->get()[0]->codigo_uf;
    }
}
