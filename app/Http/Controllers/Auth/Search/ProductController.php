<?php

namespace App\Http\Controllers\Auth\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Product\Tableprice;
use App\Models\Product\Tablepriceproduct;
use TokenJWT;

class ProductController extends Controller
{
    private $tablePriceDefault = 1;
    private $ufUser = 'SC';
    private $product;
    private $tablePrice;
    private $tablePriceProduct;

    public function __construct(Product $product, Tableprice $tablePrice, Tablepriceproduct $tablePriceProduct)
    {
        $this->product = $product;
        $this->tablePrice = $tablePrice;
        $this->tablePriceProduct = $tablePriceProduct;
    }

    public function searchProduct($id, $uf)
    {
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        $uf = filter_var(strtoupper($uf), FILTER_SANITIZE_STRING);

        $decode_id      = TokenJWT::decode($id);
        $cod_product    = $decode_id->cod_product;

        $column_icms = '';

        switch ($uf) {
            case 'MG':
            case 'RJ':
            case 'PR':
            case 'SP':
            case 'RS':
                $column_icms = 'products.icms_1';
                break;
            case $this->ufUser:
                $column_icms = 'products.icms_2';
                break;
            default:
                $column_icms = 'products.icms_3';
                break;
        }

        $queryProduct = $this->product
                            ->select(['tableprices.description', 'products.unity', 'products.amount', 'tablepriceproducts.price', "$column_icms as icms", 'products.ipi_saida', 'products.subst_trib', 'products.lucro_pres'])
                            ->join('tablepriceproducts', 'products.id', '=', 'tablepriceproducts.cod_product')
                            ->join('tableprices', 'tablepriceproducts.cod_table_price', '=', 'tableprices.id')
                            ->where([['tablepriceproducts.cod_table_price', $this->tablePriceDefault], ['products.id', $cod_product]])
                            ->get();

        echo json_encode($queryProduct[0]);
    }
}
