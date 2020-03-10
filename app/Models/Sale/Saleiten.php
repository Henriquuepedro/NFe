<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class Saleiten extends Model
{
    public $table = "saleitems";
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['cod_sale', 'cod_product', 'qnty_iten', 'value_iten', 'icms_st_iten', 'base_icms_st_iten', 'icms_iten', 'base_icms_iten', 'st_iten', 'icms_perc_iten', 'ipi_iten', 'ipi_perc_iten', 'discount_iten', 'have_st_iten', 'value_total_iten', 'complement_product_iten'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at'];
}
