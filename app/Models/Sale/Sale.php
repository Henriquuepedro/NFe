<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['cod_client', 'note', 'cod_user_reg', 'cod_user_alt'];

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
    public function edit(array $sale, int $codSale)
    {
        return $this->where('id', $codSale)->update($sale);
    }
}
