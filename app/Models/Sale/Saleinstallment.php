<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class Saleinstallment extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['cod_sale', 'installment_number', 'due_day', 'due_date', 'value', 'pay_day'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at'];
}
