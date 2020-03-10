<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Tablepriceproduct extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['cod_table_price', 'cod_product', 'price'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
