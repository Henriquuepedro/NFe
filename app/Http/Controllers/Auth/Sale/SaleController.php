<?php

namespace App\Http\Controllers\Auth\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale\Sale;
use App\Models\Sale\Saleiten;
use App\Models\Sale\Salepayment;
use App\Models\Sale\Saleinstallment;
use App\Models\Nature\Nature;
use App\Models\Client;
use App\Models\Product\Product;
use App\Models\Product\Tableprice;
use App\Models\NFe\Fiscal_note;
use App\Http\Requests\SaleRequest;
use TokenJWT;
use DB;

class SaleController extends Controller
{

    private $sale;
    private $saleiten;
    private $tablePrice;
    private $client;
    private $product;
    private $salepayment;
    private $saleinstallment;
    private $arrayProducts = [];
    private $arrayInstallments = [];
    private $dataSale;
    private $dataPayment;
    private $nature;
    private $fiscal_note;

    public function __construct(Sale $sale, Saleiten $saleiten, Tableprice $tablePrice, Client $client, Product $product, Salepayment $salepayment, Saleinstallment $saleinstallment, Nature $nature, Fiscal_note $fiscal_note)
    {
        $this->sale             = $sale;
        $this->saleiten         = $saleiten;
        $this->tablePrice       = $tablePrice;
        $this->client           = $client;
        $this->product          = $product;
        $this->salepayment      = $salepayment;
        $this->saleinstallment  = $saleinstallment;
        $this->nature           = $nature;
        $this->fiscal_note      = $fiscal_note;
    }

    /**
     * Returns a view with available sales.
     *
     * @return view
     */
    public function list()
    {
        $sales = $this  ->sale
                        ->select('*', 'sales.created_at as date_create', 'sales.id as cod_sale')
                        ->join('clients', 'sales.cod_client', '=', 'clients.id')
                        ->join('salepayments', 'sales.id', '=', 'salepayments.cod_sale')
                        ->orderBy('sales.id', 'desc')
                        ->get();
        $dataSales = [];

        foreach($sales as $sale){
            $client         = $sale->razao_social;
            $id             = $sale->cod_sale;
            $liquid_value_f = number_format($sale->liquid_value, 2, ',', '.');
            $liquid_value   = $sale->liquid_value;
            $date_create    = date('d/m/Y H:i', strtotime($sale->date_create));
            $time_create    = strtotime($sale->date_create);

            $token = TokenJWT::encode([
                'userdata' => [
                    'cod_sale' => $id
                ]
            ]);

            $nfe = $this->fiscal_note->where([['cod_sale', $id], ['status', 100]])->first();

            array_push($dataSales, (object)[
                'id'            => $id,
                'id_table'      => str_pad($id, 5, 0, STR_PAD_LEFT),
                'client'        => $client,
                'liquid_value_f'=> $liquid_value_f,
                'liquid_value'  => $liquid_value,
                'date_create'   => $date_create,
                'time_create'   => $time_create,
                'idToken'       => $token,
                'status'        => $nfe === null ? null : $nfe->status,
                'cod_nf'        => $nfe === null ? "Não Emitida" : $nfe->cod_nf
            ]);
        }

        return view('admin.sale.list', compact('dataSales'));
    }

    /**
     * Returns a view with available counties.
     *
     * @return view
     */
    public function register()
    {
        // Table price/client/product recovered from database
        $tablesPrice    = $this->tablePrice->get();
        $clients        = $this->client->orderBy('razao_social')->get();
        $products       = $this->product->where('active', 1)->orderBy('description')->get();
        // Array empty, datas table price/cliens/product
        $dataTablesPrice    = [];
        $dataClients        = [];
        $dataProducts       = [];

        // Loop to create option of select tables price/client/product
        foreach($tablesPrice as $tablePrice) $dataTablesPrice[$tablePrice->id] = $tablePrice->description;
        foreach($clients as $client) $dataClient[$client->id] = $client->razao_social;


        foreach($products as $product){

            $id_cryp = TokenJWT::encode([
                'userdata' => [
                    'cod_product' => $product->id
                ]
            ]);

            $dataProducts[$id_cryp] = $product->description;
        }

        // Return view products register
        return view('admin.sale.register', compact('dataTablesPrice', 'dataClient', 'dataProducts'));
    }

    /**
     * Returns a view of edit sale.
     *
     * @return view
     */
    public function edit(int $id)
    {
        // Table price/client/product recovered from database
        $tablesPrice    = $this->tablePrice->get();
        $clients        = $this->client->orderBy('razao_social')->get();
        $products       = $this->product->where('active', 1)->orderBy('description')->get();
        $nfe            = $this->fiscal_note->where([['cod_sale', $id], ['status', 100]])->first();
        $codNFe         = $nfe === null ? null : $nfe->cod_nf;
        // Itens da venda
        $queryItems = $this->sale
                        ->join('saleitems', 'sales.id', '=', 'saleitems.cod_sale')
                        ->where('sales.id', $id)
                        ->get();

        if($queryItems->count() === 0) return redirect()->route('admin.sale.list');

        // Forma de pagamento
        $dataPayment = $this->salepayment
                        ->join('saleinstallments', 'salepayments.cod_sale', '=', 'saleinstallments.cod_sale')
                        ->where('salepayments.cod_sale', $id)
                        ->get();

        // Array empty, datas table price/cliens/product
        $dataTablesPrice    = [];
        $dataClients        = [];
        $dataProducts       = [];
        $dataItems          = [];

        // Loop to create option of select tables price/client/product
        foreach($tablesPrice as $tablePrice) $dataTablesPrice[$tablePrice->id] = $tablePrice->description;
        foreach($clients as $client) $dataClient[$client->id] = $client->razao_social;
        foreach($products as $product){
            $id_cryp = TokenJWT::encode([
                'userdata' => [
                    'cod_product' => $product->id
                ]
            ]);

            $dataProducts[$id_cryp] = $product->description;
        }

        // Itens venda
        $dataItems['items'] = [];
        foreach($queryItems as $key => $iten){
            $id_cryp = TokenJWT::encode([
                'userdata' => [
                    'cod_product' => $iten->cod_product
                ]
            ]);
            array_push($dataItems['items'], [
                'cod_product'       => $id_cryp,
                'description'       => $dataProducts[$id_cryp],
                'qnty_iten'         => number_format($iten->qnty_iten, 2, ',', '.'),
                'value_iten'        => number_format($iten->value_iten, 2, ',', '.'),
                'icms_st_iten_grid' => number_format($iten->icms_st_iten, 2, ',', '.'),
                'icms_st_iten'      => $iten->icms_st_iten,
                'base_icms_st_iten' => number_format($iten->base_icms_st_iten, 2, '.', ''),
                'icms_iten'         => number_format($iten->icms_iten, 2, '.', ''),
                'base_icms_iten'    => number_format($iten->base_icms_iten, 2, '.', ''),
                'st_iten'           => number_format($iten->st_iten, 2, '.', ''),
                'icms_perc_iten'    => number_format($iten->icms_perc_iten, 2, ',', '.'),
                'ipi_iten'          => number_format($iten->ipi_iten, 2, ',', '.'),
                'ipi_perc_iten'     => number_format($iten->ipi_perc_iten, 2, '.', ''),
                'discount_iten'     => number_format($iten->discount_iten, 2, ',', '.'),
                'have_st_iten'      => $iten->have_st_iten,
                'value_total_iten'  => number_format($iten->value_total_iten, 2, ',', '.')
            ]);
        }

        // Encriptografa o id do produto, para evitar burlar
        $token_update = TokenJWT::encode([
            'userdata' => [
                'cod_sale' => $queryItems[0]->cod_sale
            ]
        ]);
        $dataItems['sale'] = [
            'cod_client'    => $iten->cod_client,
            'created_at'    => date('Y-m-d', strtotime($iten->created_at)),
            'updated_at'    => date('Y-m-d', strtotime($iten->updated_at)),
            'cod_sale_token'=> $token_update,
            'cod_sale'      => $queryItems[0]->cod_sale,
            'count_items'   => count($dataItems['items'])
        ];

        return view('admin.sale.edit', compact('dataItems', 'dataPayment', 'dataTablesPrice', 'dataClient', 'dataProducts', 'codNFe'));
    }

    /**
     * Get request data for database manipulation.
     *
     * @param SaleRequest $request
     * @return void
     */
    public function insert(SaleRequest $request)
    {
        $validateInstallments = $this->validateInstallments($request);
        if(count($validateInstallments) > 0) return back()->withErrors($validateInstallments)->withInput($request->all());

        $products           = [];
        $installments       = [];
        $insertItems        = true; // Verificação se ocorreu algum problema no envio dos itens
        $insertInstallments = true; // Verificação se ocorreu algum problema no envio dos pagamentos

        // Valida impostos com erro
        $validateTaxUser = $this->setArrayProducts($request);
        if(count($validateTaxUser) > 0) return back()->withErrors($validateTaxUser)->withInput($request->all());

        $this->setArrayInstallments($request);
        $this->setDataSale($request);
        $this->setDataPayment($request);

        // Inicia transação para inserção a base de dados
        DB::beginTransaction();

        // Insere a venda na base
        $insertSale = $this->sale->create($this->dataSale);
        $codSale = $insertSale->id; // Recupera ID inserido da venda

        // Insere o financeiro da venda
        $this->dataPayment['cod_sale'] = $codSale; // Add código no arrray
        $insertSalepayment = $this->salepayment->create($this->dataPayment);

        // Loop para enviar todos os itens
        foreach($this->arrayProducts as $product){
            $product['cod_sale'] = $codSale; // Add código no arrray
            if($this->saleiten->create($product) != true) $insertItems = false; // Verifica se ocorreu algum erro
        }

        // Loop para enviar todos os pagamentos
        foreach($this->arrayInstallments as $installment){
            $installment['cod_sale'] = $codSale; // Add código no arrray
            if($this->saleinstallment->create($installment) != true) $insertInstallments = false; // Verifica se ocorreu algum erro
        }

        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página da nova venda, com uma mensagem de sucesso
        if($insertSale && $insertSalepayment && $insertItems && $insertInstallments){
            DB::commit();
            return redirect()->route('admin.sale.edit', ['id' => $codSale])
                             ->with('success', 'Venda cadastrada com sucesso!');
        }

        DB::rollBack();
        return 'view error';
    }

    /**
     * Get request data for database manipulation.
     *
     * @param SaleRequest $request
     * @return void
     */
    public function update(SaleRequest $request)
    {
        $dataDecode = TokenJWT::decode($request['token_update']);
        $codSale = $dataDecode->cod_sale;

        if($codSale != $request->id_update) return back()->withErrors('Autenticação da venda inválida, recarregue a página e tente novamente!')->withInput($request->all());

        $validateInstallments = $this->validateInstallments($request);
        if(count($validateInstallments) > 0) return back()->withErrors($validateInstallments)->withInput($request->all());

        $products           = [];
        $installments       = [];
        $updateItems        = true; // Verificação se ocorreu algum problema no envio dos itens
        $updateInstallments = true; // Verificação se ocorreu algum problema no envio dos pagamentos


        // Valida impostos com erro
        $validateTaxUser = $this->setArrayProducts($request);
        if(count($validateTaxUser) > 0) return back()->withErrors($validateTaxUser)->withInput($request->all());

        $this->setArrayInstallments($request);
        $this->setDataSale($request);
        $this->setDataPayment($request);

        // Inicia transação para inserção a base de dados
        DB::beginTransaction();

        $deleteInstallments = $this->saleinstallment->where('cod_sale', $codSale)->delete();
        $deleteIntems = $this->saleiten->where('cod_sale', $codSale)->delete();

        // Loop para atualizar todos os itens
        foreach($this->arrayProducts as $product){
            $product['cod_sale'] = $codSale; // Add código no arrray
            if($this->saleiten->create($product) != true) $updateItems = false; // Verifica se ocorreu algum erro
        }
        // Loop para atualizar todos os pagamentos
        foreach($this->arrayInstallments as $installment){
            $installment['cod_sale'] = $codSale; // Add código no arrray
            if($this->saleinstallment->create($installment) != true) $updateInstallments = false; // Verifica se ocorreu algum erro
        }

        // Atualiza a venda na base
        $updateSale = $this->sale->edit($this->dataSale, $codSale);

        // Atualiza o financeiro da venda
        $this->dataPayment['cod_sale'] = $codSale; // Add código no arrray
        $updateSalepayment = $this->salepayment->edit($this->dataPayment, $codSale);

        // Caso a atualização for bem sucedida, finaliza a atualização na base e retorna a página da venda, com uma mensagem de sucesso
        if($updateSale && $updateSalepayment && $updateItems && $updateInstallments){
            DB::commit();
            return redirect()->route('admin.sale.edit', ['id' => $codSale])
                             ->with('success', 'Venda alterada com sucesso!');
        }

        DB::rollBack();
        return 'view error';
    }

    /**
     * Get request data for database manipulation.
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $dataDecode = TokenJWT::decode($request['idToken']);
        $codSale = $dataDecode->cod_sale;

        DB::beginTransaction();

        $deleteInstallments = $this->saleinstallment->where('cod_sale', $codSale)->delete();
        $deleteItems = $this->saleiten->where('cod_sale', $codSale)->delete();
        $deletePayment = $this->salepayment->where('cod_sale', $codSale)->delete();
        $deleteSale = $this->sale->where('id', $codSale)->delete();

        // Caso a atualização for bem sucedida, finaliza a atualização na base e retorna a página da venda, com uma mensagem de sucesso
        if($deleteSale && $deletePayment && $deleteItems && $deleteInstallments){
            DB::commit();
            return redirect()->route('admin.sale.list')
                             ->with('success', 'Venda excluída com sucesso!');
        }

        DB::rollBack();
        return 'view error';
    }

    /**
     * Create array of products and validate, attr value for arrayProducts
     *
     * @param array $request
     * @return null
     */
    public function setArrayProducts(Request $request)
    {
        $error              = [];
        $icmsSt_total       = 0;
        $baseIcmsSt_total   = 0;
        $icms_total         = 0;
        $baseIcms_total     = 0;
        $ipi_total          = 0;
        // Cria array com dados dos produtos, validado
        for ($count_product=1; $count_product <= $request->qnt_items; $count_product++) {

            $decodeCodProduct   = TokenJWT::decode($request["cod_product_$count_product"]);
            $cod_product        = $decodeCodProduct->cod_product;

            // Variáveis usuário
            $dataClient         = $this->client->getClient($request->client);
            $dataProduct        = $this->product->getProduct($cod_product);
            $finalCostumer      = $dataClient->tipo_consumidor == "nao_final" ? 1 : 0;
            $icmsOrigem         = 17; // ICMS usuário
            $mva                = $dataProduct->lucro_pres;
            $haveSt             = $dataProduct->subst_trib;
            $valueIten          = $this->sanitizeNumberBr($request["value_sale_$count_product"]);
            $quantityIten       = $this->sanitizeNumberBr($request["quantity_$count_product"]);
            $discountIten       = $this->sanitizeNumberBr($request["discount_$count_product"]);
            $icms_perc_iten     = $this->sanitizeNumberBr($request["icms_$count_product"]);
            $ipi_iten           = $this->sanitizeNumberBr($request["ipi_$count_product"]);
            $ipi_perc_iten      = $this->sanitizeNumberBr($request["ipi_perc_$count_product"]);
            $icms_st_iten       = $this->sanitizeNumberBr($request["valueSt_$count_product"]);
            $base_icms_st_iten  = $this->sanitizeNumberBr($request["valueBaseSt_$count_product"]);
            $icms_iten          = $this->sanitizeNumberBr($request["valueIcms_$count_product"]);
            $base_icms_iten     = $this->sanitizeNumberBr($request["valueBaseIcms_$count_product"]);
            $valueStReal        = $this->sanitizeNumberBr($request["valueStReal_$count_product"]);

            // Cálculo impostos
            $amount         = ($valueIten * $quantityIten) - $discountIten;
            $icmsSt         = 0;
            $valueBaseSt    = 0;
            $vlrIcmsDestino = 0;
            $baseIcmsDestino= 0;
            $valueIpi       = 0;

            if($haveSt == 1 && $finalCostumer == 1 && $icms_perc_iten != 0){
                $baseIcmsDestino = $amount;
                $vlrIcmsDestino  = $amount * ($icms_perc_iten/100);
                $valueIpi        = $amount * ($ipi_perc_iten/100);
                $amountLiquid    = $amount + $valueIpi;

                $valueBaseSt    = $amountLiquid * (1+($valueStReal/100));
                $icmsSt        = ($valueBaseSt * ($icmsOrigem/100)) - $vlrIcmsDestino;
            }

            // Verifica cálculo de impostos
            number_format($baseIcmsDestino,2, '.', '')  != $base_icms_iten      ? array_push($error, "O valor da base do ICMS do {$count_product}º produto está diferente da somatória dos itens!") : false;
            number_format($vlrIcmsDestino,2, '.', '')   != $icms_iten           ? array_push($error, "O valor do ICMS do {$count_product}º produto está diferente da somatória dos itens!") : false;
            number_format($valueBaseSt,2, '.', '')      != $base_icms_st_iten   ? array_push($error, "O valor da base do ICMS ST do {$count_product}º produto está diferente da somatória dos itens!") : false;
            number_format($icmsSt ,2, '.', '')          != $icms_st_iten        ? array_push($error, "O valor do ICMS ST do {$count_product}º produto está diferente da somatória dos itens!") : false;
            number_format($valueIpi ,2, '.', '')        != $ipi_iten            ? array_push($error, "O valor do IPI do {$count_product}º produto está diferente da somatória dos itens!") : false;

            $icmsSt_total       += $icms_st_iten;
            $baseIcmsSt_total   += $base_icms_st_iten;
            $icms_total         += $icms_iten;
            $baseIcms_total     += $base_icms_iten;
            $ipi_total          += $ipi_iten;

            array_push($this->arrayProducts, [
                "cod_product"               => $cod_product,
                "qnty_iten"                 => $quantityIten,
                "value_iten"                => $valueIten,
                "icms_perc_iten"            => $icms_perc_iten,
                "ipi_iten"                  => $ipi_iten,
                "ipi_perc_iten"             => $ipi_perc_iten,
                "discount_iten"             => $discountIten,
                "value_total_iten"          => $this->sanitizeNumberBr($request["amount_$count_product"]),
                "icms_st_iten"              => $icms_st_iten,
                "base_icms_st_iten"         => $base_icms_st_iten,
                "icms_iten"                 => $icms_iten,
                "base_icms_iten"            => $base_icms_iten,
                "st_iten"                   => $this->sanitizeNumberBr($request["valueStReal_$count_product"]),
                "have_st_iten"              => filter_var($request["haveSt_$count_product"], FILTER_VALIDATE_INT),
                "complement_product_iten"   => ""
            ]);
        }

        number_format($icmsSt_total, 2, '.', '')       != (float)$request->value_icmsst ? array_push($error, "O valor do ICMS ST total está diferente da somatória dos itens!") : false;
        number_format($baseIcmsSt_total, 2, '.', '')   != (float)$request->base_icmsst ? array_push($error, "O valor da base do ICMS ST total está diferente da somatória dos itens!") : false;
        number_format($icms_total, 2, '.', '')         != (float)$request->value_icms ? array_push($error, "O valor do ICMS total está diferente da somatória dos itens!") : false;
        number_format($baseIcms_total, 2, '.', '')     != (float)$request->base_icms ? array_push($error, "O valor da base do ICMS total está diferente da somatória dos itens!") : false;
        number_format($ipi_total, 2, '.', '')          != (float)$request->value_ipi ? array_push($error, "O valor do IPI total está diferente da somatória dos itens!") : false;

        return $error;
    }

    /**
     * Create array of installments and validate, attr value for arrayInstallments
     *
     * @param array $request
     * @return null
     */
    public function setArrayInstallments(Request $request)
    {
        // Cria array com dados das parcelas, validado
        for ($count_installment=1; $count_installment <= $request->installment; $count_installment++) {
            array_push($this->arrayInstallments, [
                "installment_number"    => $count_installment,
                "due_day"               => filter_var($request["days_p_" . $count_installment], FILTER_VALIDATE_INT),
                "due_date"              => $request["date_p_" . $count_installment],
                "value"                 => $this->sanitizeNumberBr($request["value_p_" . $count_installment])
            ]);
        }
    }

    /**
     * Validate data sale and attr value for dataSale
     *
     * @param array $request
     * @return null
     */
    public function setDataSale(Request $request)
    {
        // Dados da venda, validado
        $this->dataSale = [
            "cod_client" => filter_var($request->client, FILTER_VALIDATE_INT),
            "note" => "",
            "cod_user_reg" => auth()->user()->id
        ];
    }

    /**
     * Validate data payment sale and attr value for dataPayment
     *
     * @param array $request
     * @return null
     */
    public function setDataPayment(Request $request)
    {
        // Dados financeiro da venda, validado
        $this->dataPayment = [
            "shipping"              => $this->sanitizeNumberBr($request->shipping),
            "insurance"             => $this->sanitizeNumberBr($request->insurance),
            "other_expense"         => $this->sanitizeNumberBr($request->others_expenses),
            "daily_charges"         => $this->sanitizeNumberBr($request->daily_charges),
            "icms_st"               => $this->sanitizeNumberBr($request->value_icmsst),
            "base_icms_st"          => $this->sanitizeNumberBr($request->base_icmsst),
            "icms"                  => $this->sanitizeNumberBr($request->value_icms),
            "base_icms"             => $this->sanitizeNumberBr($request->base_icms),
            "ipi"                   => $this->sanitizeNumberBr($request->value_ipi),
            "discount"              => $this->sanitizeNumberBr($request->discount_general),
            "gross_value"           => $this->sanitizeNumberBr($request->gross_value),
            "liquid_value"          => $this->sanitizeNumberBr($request->liquid_value),
            "quantity_installment"  => filter_var($request->installment, FILTER_VALIDATE_INT),
            "calculate_automatic"   => isset($request->calculate_automatic) ? 1 : 0
        ];
    }

    /**
     * Validate data installments sale and verified total value and sum installments
     *
     * @param array $request
     * @return null
     */
    public function validateInstallments(Request $request)
    {
        $msgsError = [];
        $sumTotal = 0;
        $orderDays = -9999999;
        for ($countInstallment = 1; $countInstallment <= $request->installment; $countInstallment++) {
            $days   = filter_var($request["days_p_$countInstallment"], FILTER_VALIDATE_INT);
            $value  = filter_var($request["value_p_$countInstallment"], FILTER_VALIDATE_FLOAT);

            if($days <= $orderDays) break;

            $orderDays = $days;
            $sumTotal += $value;
        }

        $liquidValue = (float)number_format($request->liquid_value, 3, '.', '');
        $sumTotal = (float)number_format($sumTotal, 3, '.', '');

        if($liquidValue != $sumTotal && ($countInstallment - 1) == $request->installment) array_push($msgsError,"Os valores das parcelas estão diferente do valor total!");
        if(($countInstallment - 1) != $request->installment) array_push($msgsError,"As datas de vencimento precisam ser informadas em ordem crescente!");

        return $msgsError;
    }
}
