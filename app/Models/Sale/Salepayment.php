<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class Salepayment extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['cod_sale', 'shipping', 'insurance', 'other_expense', 'daily_charges', 'icms_st', 'base_icms_st', 'icms', 'base_icms', 'ipi', 'discount', 'gross_value', 'liquid_value', 'quantity_installment', 'calculate_automatic'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at'];

    /**
     * Update information in the clients database
     *
     * @param array $product
     * @param int $codProduct
     * @return boolean
     */
    public function edit(array $salePayment, int $codSale)
    {
        return $this->where('cod_sale', $codSale)->update($salePayment);
    }
}
