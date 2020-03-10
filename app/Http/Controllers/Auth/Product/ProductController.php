<?php

namespace App\Http\Controllers\Auth\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Product\Tableprice;
use App\Models\Product\Tablepriceproduct;
use App\Models\Sale\Saleiten;
use TokenJWT;
use DB;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    private $product;
    private $tablePrice;
    private $tablePriceProduct;
    private $saleiten;

    public function __construct(Product $product, Tableprice $tablePrice, Tablepriceproduct $tablePriceProduct, Saleiten $saleiten)
    {
        $this->product              = $product;
        $this->tablePrice           = $tablePrice;
        $this->tablePriceProduct    = $tablePriceProduct;
        $this->saleiten             = $saleiten;
    }

    /**
     * Returns a view with available products.
     *
     * @return view
     */
    public function list()
    {
        $products = $this->product->get();
        $dataProducts = [];

        foreach($products as $product){
            $description    = $product->description;
            $reference      = $product->reference === null ? "Não Informado" : $product->reference;
            $amount         = number_format($product->amount, 2, ',', '.');

            $token = TokenJWT::encode([
                'userdata' => [
                    'cod_product' => $product->id
                ]
            ]);

            array_push($dataProducts, (object)[
                'id'            => $product['id'],
                'description'   => $description,
                'reference'     => $reference,
                'amount'        => $amount,
                'idToken'       => $token
            ]);
        }

        return view('admin.product.list', compact('dataProducts'));
    }

    /**
     * Returns a view with available counties.
     *
     * @return view
     */
    public function register()
    {
        // Consulta tabela de preço
        $tablesPrice = $this->tablePrice->get();

        // Criar array para usar na tag select
        $dataTablesPrice = [];
        foreach($tablesPrice as $tablePrice) $dataTablesPrice[$tablePrice->id] = $tablePrice->description;

        // Return view products register
        return view('admin.product.register', compact('dataTablesPrice'));
    }

    /**
     * Returns a view with available counties.
     *
     * @return view
     */
    public function edit($id)
    {
        // Recupera os dados do produto para alteração
        $product = $this->product
                                ->select(
                                    '*',
                                    'products.id as codProduct',
                                    'products.description as descProduct',
                                    'tableprices.description as descTablePrice'
                                )
                                ->where('products.id', $id)
                                ->join('tablepriceproducts', 'products.id', '=', 'tablepriceproducts.cod_product')
                                ->join('tableprices', 'tablepriceproducts.cod_table_price', '=', 'tableprices.id')
                                ->get();

        if($product->count() === 0) return redirect()->route('admin.product.list');

        // Encriptografa o id do produto, para evitar burlar
        $token = TokenJWT::encode([
            'userdata' => [
                'cod_product' => $product[0]['codProduct']
            ]
        ]);
        $product[0]['token_update'] = $token;

        // Formatando quantidade em estoque
        $product[0]['amount']       = number_format($product[0]['amount'], 2, ',', '.');
        $product[0]['icms_1']       = number_format($product[0]['icms_1'], 2, ',', '.');
        $product[0]['icms_2']       = number_format($product[0]['icms_2'], 2, ',', '.');
        $product[0]['icms_3']       = number_format($product[0]['icms_3'], 2, ',', '.');
        $product[0]['ipi_saida']    = number_format($product[0]['ipi_saida'], 2, ',', '.');
        $product[0]['fcp']          = number_format($product[0]['fcp'], 2, ',', '.');
        $product[0]['lucro_pres']   = number_format($product[0]['lucro_pres'], 2, ',', '.');
        $product[0]['inci_imposto'] = number_format($product[0]['inci_imposto'], 2, ',', '.');
        $product[0]['imposto_impor']= number_format($product[0]['imposto_impor'], 2, ',', '.');

        // Consulta tabela de preço
        $tablesPrice = $this->tablePrice->get();

        // Criar array para usar na tag select
        $dataTablesPrice = [];
        foreach($tablesPrice as $tablePrice) $dataTablesPrice[$tablePrice->id] = $tablePrice->description;
        // dd($product);
        // Return view product register
        return view('admin.product.register', compact('product', 'dataTablesPrice'));
    }

    /**
     * Get request data for database manipulation.
     *
     * @param ProductRequest $request
     * @return void
     */
    public function insert(ProductRequest $request)
    {
        // Validando dados do produto
        $product = $this->sanitizeDataRequestProduct($request);
        // Validando dados da tabela de preço
        $tablePrice = $this->sanitizeDataRequestTablePrice($request);

        // Inicia transação para inserção a base de dados
        DB::beginTransaction();

        // Insere o produto na base
        $insertProduct = $this->product->create($product);
        $codProduct = $insertProduct->id; // Recupera ID inserido e adicionar ao array de tabela de preço

        $insertTablesPrice = true; // Verificação de ocorreu algum problema no envio das tabelas
        // Loop para enviar todas as tabelas de preços
        foreach($tablePrice as $table){
            $table['cod_product'] = $codProduct;
            if($this->tablePriceProduct->create($table) != true) $insertTablesPrice = false;
        }

        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página de listagem, com uma mensagem de sucesso
        if($insertProduct && $insertTablesPrice){
            DB::commit();
            return redirect()->route('admin.product.list')
                             ->with('success', 'Produto cadastrado com sucesso!');
        }

        // Caso a inserção for mal sucedida, volta a inserção na base e retorna a página de cadastra, com uma mensagem de erro
        DB::rollBack();
        return redirect()->withErrors('Não foi possível realizar o cadastro, reveja seus dados!')
				         ->withInput($request);

    }

    /**
     * Get request data for database manipulation.
     *
     * @param UpdateProductRequest $request
     * @return void
     */
    public function update(UpdateProductRequest $request)
    {
        $dataDecode = TokenJWT::decode($request['token_update']);
        $codProduct = $dataDecode->cod_product;

        // Validando dados do produto
        $product = $this->sanitizeDataRequestProduct($request);
        // Validando dados da tabela de preço
        $tablePrice = $this->sanitizeDataRequestTablePrice($request);

        // Inicia transação para inserção a base de dados
        DB::beginTransaction();

        // Altera o produto na base
        $updateProduct   = $this->product->edit($product, $codProduct);

        // Remove todas as tabelas de preço do produto
        $deleteTablePrice = $this->tablePriceProduct->where('cod_product', $codProduct)->delete();

        $insertTablesPrice = true; // Verificação de ocorreu algum problema no envio das tabelas
        // Loop para enviar todas as tabelas de preços
        foreach($tablePrice as $table){
            $table['cod_product'] = $codProduct;
            if($this->tablePriceProduct->create($table) != true) $insertTablesPrice = false;
        }

        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página de listagem, com uma mensagem de sucesso
        if($insertTablesPrice && $updateProduct && $deleteTablePrice){
            DB::commit();
            return redirect()->route('admin.product.list')
                             ->with('success', 'Produto alterado com sucesso!');
        }

        // Caso a inserção for mal sucedida, volta a inserção na base e retorna a página de cadastra, com uma mensagem de erro
        DB::rollBack();
        return redirect()->withErrors('Não foi possível realizar a alteração, reveja seus dados!')
				         ->withInput($request);
    }

    /**
     * Get request data for database manipulation.
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $idToken = TokenJWT::decode($request->idToken);

        // Valida código como inteiro
        $codProduct = filter_var($idToken->cod_product, FILTER_VALIDATE_INT);

        $checkInUse = $this->saleiten->select('cod_sale')->where('cod_product', $codProduct)->groupBy('cod_sale')->get();

        if($checkInUse->count() > 0)
            return back()->with('danger', 'Não foi possível realizar a ação, o produto está relacionado à um venda!');

        // Inicia a transação de dados
        DB::beginTransaction();

        // Remove os dados
        $deleteProduct      = $this->product->where('id', $codProduct)->delete();
        $deleteTablePrice   = $this->tablePriceProduct->where('cod_product', $codProduct)->delete();

        // Caso a exclusão for bem sucedida, finaliza a exclusão na base e retorna a página de listagem, com uma mensagem de sucesso
        if($deleteProduct && $deleteTablePrice){
            DB::commit();
            return back()->with('success', 'Produto excluído com sucesso!');
        }

        // Caso a exclusão for mal sucedida, volta a exclusão na base e retorna a página de cadastra, com uma mensagem de erro
        DB::rollBack();
        return back()->with('Não foi possível realizar a exclusão, tente novamente!');
    }

    /**
     * Sanitize all product request data
     *
     * @param array $request
     * @return array
     */
    public function sanitizeDataRequestProduct($request)
    {
        $request["active"]          = $request["active"] === null ? 0 : 1;
        $request["subst_trib"]      = $request["subst_trib"] === null ? 0 : 1;
        $request["incid_pis_cofins"]= $request["incid_pis_cofins"] === null ? 0 : 1;
        $request["indic_produc"]    = $request["indic_produc"] === null ? 0 : 1;
        $request["isento"]          = $request["isento"] === null ? 0 : 1;
        $request["imune"]           = $request["imune"] === null ? 0 : 1;
        $request["suspensao_icms"] = $request["suspensao_icms"] === null ? 0 : 1;

        $request["amount"]          = str_replace(",", ".", str_replace(".", "", $request["amount"]));
        $request["icms_1"]          = str_replace(",", ".", str_replace(".", "", $request["icms_1"]));
        $request["icms_2"]          = str_replace(",", ".", str_replace(".", "", $request["icms_2"]));
        $request["icms_3"]          = str_replace(",", ".", str_replace(".", "", $request["icms_3"]));
        $request["ipi_saida"]       = str_replace(",", ".", str_replace(".", "", $request["ipi_saida"]));
        $request["fcp"]             = str_replace(",", ".", str_replace(".", "", $request["fcp"]));
        $request["lucro_pres"]      = str_replace(",", ".", str_replace(".", "", $request["lucro_pres"]));
        $request["inci_imposto"]    = str_replace(",", ".", str_replace(".", "", $request["inci_imposto"]));
        $request["imposto_impor"]   = str_replace(",", ".", str_replace(".", "", $request["imposto_impor"]));

        $request["ncm"]             = str_replace(".", "", $request["ncm"]);
        $request["cest"]            = str_replace(".", "", $request["cest"]);
        // Validando dados do produto
        $product = [
            "description"       => filter_var($request["description"],      FILTER_SANITIZE_STRING),
            "reference"         => filter_var($request["reference"],        FILTER_SANITIZE_STRING),
            "bar_code"          => filter_var($request["bar_code"],         FILTER_SANITIZE_NUMBER_INT),
            "unity"             => filter_var($request["unity"],            FILTER_SANITIZE_STRING),
            "amount"            => filter_var($request["amount"],           FILTER_VALIDATE_FLOAT),
            "active"            => filter_var($request["active"],           FILTER_VALIDATE_INT),
            "subst_trib"        => filter_var($request["subst_trib"],       FILTER_VALIDATE_INT),
            "incid_pis_cofins"  => filter_var($request["incid_pis_cofins"], FILTER_VALIDATE_INT),
            "indic_produc"      => filter_var($request["indic_produc"],     FILTER_VALIDATE_INT),
            "isento"            => filter_var($request["isento"],           FILTER_VALIDATE_INT),
            "imune"             => filter_var($request["imune"],            FILTER_VALIDATE_INT),
            "suspensao_icms"    => filter_var($request["suspensao_icms"],   FILTER_VALIDATE_INT),
            "icms_1"            => filter_var($request["icms_1"],           FILTER_VALIDATE_FLOAT),
            "icms_2"            => filter_var($request["icms_2"],           FILTER_VALIDATE_FLOAT),
            "icms_3"            => filter_var($request["icms_3"],           FILTER_VALIDATE_FLOAT),
            "ipi_saida"         => filter_var($request["ipi_saida"],        FILTER_VALIDATE_FLOAT),
            "fcp"               => filter_var($request["fcp"],              FILTER_VALIDATE_FLOAT),
            "lucro_pres"        => filter_var($request["lucro_pres"],       FILTER_VALIDATE_FLOAT),
            "inci_imposto"      => filter_var($request["inci_imposto"],     FILTER_VALIDATE_FLOAT),
            "imposto_impor"     => filter_var($request["imposto_impor"],    FILTER_VALIDATE_FLOAT),
            "ncm"               => filter_var($request["ncm"],              FILTER_SANITIZE_NUMBER_INT),
            "cest"              => filter_var($request["cest"],             FILTER_SANITIZE_NUMBER_INT)
        ];
        $product["description"] = str_replace("&#34;", '"', $product["description"]);
        $product["description"] = str_replace("&#39;", "'", $product["description"]);

        // Validando campos nulos
        foreach ($product as $key => $value) $product[$key] = $product[$key] === "" || $product[$key] === false ? null : $product[$key];

        return $product;
    }

    /**
     * Sanitize all table price request data
     *
     * @param array $request
     * @return array
     */
    public function sanitizeDataRequestTablePrice($request)
    {
        // Validando dados do endereço
        $tablePrice = [];
        for ($countTable=0; $countTable < count($request->tablesPrice); $countTable++) {
            array_push($tablePrice, [
                'cod_table_price'   => filter_var($request->tablesPrice[$countTable], FILTER_VALIDATE_INT),
                'price'             => filter_var($request->valuesPrice[$countTable], FILTER_VALIDATE_FLOAT)
            ]);
        }

        return $tablePrice;
    }
}
