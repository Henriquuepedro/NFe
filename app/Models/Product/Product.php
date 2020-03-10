<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['description', 'reference', 'bar_code', 'unity', 'amount', 'active', 'icms_1', 'icms_2', 'icms_3', 'ipi_saida', 'fcp', 'lucro_pres', 'inci_imposto', 'imposto_impor', 'ncm', 'cest', "subst_trib", "incid_pis_cofins", "indic_produc", "isento", "imune", "suspensao_icms"];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Update information in the clients database
     *
     * @param array $product
     * @param int $codProduct
     * @return boolean
     */
    public function edit(array $product, int $codProduct)
    {
        return $this->where('id', $codProduct)->update($product);
    }

    /**
     * Get data product
     *
     * @param int $cod_product
     * @return array
     */
    public function getProduct($cod_product)
    {
        return $this->where('id', $cod_product)->first();
    }
}
