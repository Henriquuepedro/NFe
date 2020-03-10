<?php

namespace App\Http\Controllers\Auth\Nfe;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TokenJWT;
use App\Models\Client;
use App\Models\Nature\Nature;
use App\Models\Product\Product;
use App\Models\Sale\Sale;
use App\Models\Sale\Saleinstallment;
use App\Models\Sale\Saleiten;
use App\Models\Sale\Salepayment;
use App\Models\Profile;
use App\Models\NFe\Fiscal_note;
use App\Models\NFe\Fiscal_notes_event;
use Illuminate\Http\Request;
use App\Services\NfeService;
use DB;
use Illuminate\Support\Facades\Response;
use NFePHP\DA\NFe\Danfe;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use Stichoza\GoogleTranslate\GoogleTranslate;

class NfeController extends Controller
{
    private $client;
    private $product;
    private $sale;
    private $saleiten;
    private $salepayment;
    private $saleinstallment;
    private $nature;
    private $profile;
    private $NFe;
    private $NFeEvent;

    public function __construct(Sale $sale, Saleiten $saleiten, Client $client, Product $product, Salepayment $salepayment, Saleinstallment $saleinstallment, Nature $nature, Profile $profile, Fiscal_note $NFe, Fiscal_notes_event $NFeEvent)
    {
        $this->sale             = $sale;
        $this->saleiten         = $saleiten;
        $this->client           = $client;
        $this->product          = $product;
        $this->salepayment      = $salepayment;
        $this->saleinstallment  = $saleinstallment;
        $this->nature           = $nature;
        $this->profile          = $profile;
        $this->NFe              = $NFe;
        $this->NFeEvent         = $NFeEvent;
    }

    /**
     * Returns a view of nfe sale.
     *
     * @param int $id
     * @return view
     */
    public function index(int $id)
    {
        // Verifica NF-e emitida
        $queryNFe = $this->NFe->where([['cod_sale', $id], ['status', 100]]);
        if($queryNFe->count() === 1)
            return redirect()->route('admin.sale.nfe.view', ['id' => $queryNFe->first()->cod_nf])->with('danger', "Venda {$id} já existe um NF-e emitida!");


        // Itens da venda
        $dataItems      = [];
        $dataNatures    = [];

        $queryItems = $this->sale
                            ->join('saleitems', 'sales.id', '=', 'saleitems.cod_sale')
                            ->join('products', 'saleitems.cod_product', '=', 'products.id')
                            ->where('sales.id', $id)
                            ->get();

        if($queryItems->count() === 0) return redirect()->route('admin.sale.list');

        // Forma de pagamento
        $dataPayment = $this->sale
                            ->join('salepayments', 'sales.id', '=', 'salepayments.cod_sale')
                            ->join('saleinstallments', 'salepayments.cod_sale', '=', 'saleinstallments.cod_sale')
                            ->join('clients', 'sales.cod_client', '=', 'clients.id')
                            ->where('salepayments.cod_sale', $id)
                            ->first();

        $validateSaleNfe = $this->getValidationNfe($id);

        $natureCustomer = $dataPayment->tipo_consumidor == "final" ? 0 : 1;
        $natures = $this->nature->where('customer_type', $natureCustomer)->get();
        foreach($natures as $nature) $dataNatures[$nature->id] = $nature->description;

        // Encriptografa o id do produto, para evitar burlar
        $token_sale = TokenJWT::encode([
            'userdata' => [
                'cod_sale' => $id
            ]
        ]);

        // Itens venda
        foreach($queryItems as $key => $iten){
            array_push($dataItems, [
                'description'   => $iten->description,
                'have_st_iten'  => $iten->have_st_iten === 0 ? "Não" : "Sim",
            ]);
        }

        return view('admin.sale.nfe', compact('dataPayment', 'token_sale', 'dataItems', 'validateSaleNfe', 'dataNatures'));
    }

    /**
     * Create array with data config NF-e
     *
     * @param string $razao_social
     * @param string $uf
     * @param string $cnpj
     * @return array
     */
    public function createConfigNFe(string $razao_social, string $uf, string $cnpj)
    {
        // Configuração NFe
        return [
            "atualizacao"   => "2020-02-12 15:40:36",
            "tpAmb"         => 2, // 1 - Produção / 2 - homologação
            "razaosocial"   => $razao_social,
            "siglaUF"       => $uf,
            "cnpj"          => $cnpj,
            "schemes"       => "PL_009_V4",
            "versao"        => "4.00",
            "tokenIBPT"     => "AAAAAAA",
            "CSC"           => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
            "CSCid"         => "000002"
        ];
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $arrSale = $this->createArrSale($request);

        // Configuração NFe
        $arrSale->config = $this->createConfigNFe($arrSale->emitente['razao_social'], $arrSale->emitente['uf'], $arrSale->emitente['cnpj']);

        $nfe_service = new NfeService($arrSale);
//        dd($nfe_service);
//        if(isset($nfe_service['error'])) return back()->withErrors($nfe_service['error'])->withInput($request->all());
        $xml = $nfe_service->gerarNfe();
//        dd($xml);
        if(isset($xml['error'])) return back()->withErrors($xml['error'])->withInput($request->all());

        // Assina NFe e verifica erros
        try {
            $signedXml = $nfe_service->sign($xml);
        } catch (\Exception $e) {
//            GoogleTranslate::trans($e->getMessage(), 'pt', 'en')
            return back()->withErrors(['error' => [$e->getMessage()]])->withInput($request->all());
        } catch (\Error $e) {
            return back()->withErrors(['error' => [$e->getMessage()]])->withInput($request->all());
        }

        // Transmitir
        $anwser = $nfe_service->transmit($signedXml);

        // Acrescenta uma unidade para tentar emitir a próxima NFe
        $userProfile = $this->profile->where('cnpj', auth()->user()->cnpj)->first();
        $number_nfe = $userProfile->number_start_nfe + 1;
        $this->profile->edit(['number_start_nfe' => $number_nfe], $userProfile->id, $userProfile->cnpj);

        $arrFiscalNote = [
            'key'               => isset($anwser['erro']) > 0 ? 0 : $anwser['chave'],
            'status'            => isset($anwser['erro']) > 0 ? 0 : $anwser['status'],
            'protocolo'         => isset($anwser['erro']) > 0 ? 0 : $anwser['protocolo'],
            'return_sefaz'      => isset($anwser['erro']) > 0 ? $anwser['erro'] : $anwser['motivo'],
            'date_emission'     => isset($anwser['erro']) > 0 ? null : date('Y-m-d H:i:s', strtotime($anwser['dataHora'])),

            "cod_nf"            => $userProfile->number_start_nfe,
            "cod_sale"          => $arrSale->nfe['cod_sale'],
            "cod_client"        => $arrSale->destinatario['cod'],
            "document_client"   => $arrSale->destinatario['cnpj_cpf'],
            "rg_ie_client"      => $arrSale->destinatario['rg_ie'],
            "uf_client"         => $arrSale->destinatario['uf'],
            "seq"               => $request->seq,
            "name_nature"       => $arrSale->nfe['nature']['natOp'],
            "cod_nature"        => $request->nature,
            "finality"          => $arrSale->nfe['finNFe'],
            "qnty"              => $arrSale->nfe['sendQnty'],
            "specie"            => $arrSale->nfe['sendSpecie'] === null ? "" : $arrSale->nfe['sendSpecie'],
            "gross_weight"      => $arrSale->nfe['Gweight'],
            "liquid_weight"     => $arrSale->nfe['Lweight'],
            "shipping"          => $arrSale->nfe['frete'],
            "gross_value"       => $arrSale->nfe['totalB'],
            "liquid_value"      => $arrSale->nfe['totalL'],
            "cod_user_reg"      => auth()->user()->id
        ];

        DB::beginTransaction();

        $insertNFe = $this->NFe->create($arrFiscalNote);
        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página da NFe, com uma mensagem de sucesso
        if($insertNFe){
            DB::commit();

            if(isset($anwser['erro']) > 0) return back()->withErrors($anwser['erro'])->withInput($request->all());

        }
        if($anwser['status'] == 100){
            // Criar página para visualizar NFe
            // Criar tabela para guardar informações da NFe emitada
            return redirect()->route('admin.sale.nfe.view', ['id' => $insertNFe->cod_nf]);

            // Caso a inserção for mal sucedida, volta a inserção na base e retorna a página de emissão, com uma mensagem de erro

//            return back()->with('Não foi possível realizar a exclusão, tente novamente!');
        }

        DB::rollBack();
        return redirect()->withErrors('Não foi possível realizar a emissão, reveja seus dados!')
            ->withInput($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewNfeEmit(int $cod)
    {
        $queryItems = $this ->NFe
                            ->select('*', 'sales.id as codSale')
                            ->join('sales', 'fiscal_notes.cod_sale', '=', 'sales.id')
                            ->join('saleitems', 'sales.id', '=', 'saleitems.cod_sale')
                            ->join('products', 'saleitems.cod_product', '=', 'products.id')
                            ->where('fiscal_notes.cod_nf', $cod)->get();

        if($queryItems->count() === 0) return redirect()->route('admin.sale.list');

        $queryItems[0]->key_format = $this->formatKeyNFe($queryItems[0]->key);

        // Forma de pagamento
        $dataPayment = $this->sale
                            ->join('salepayments', 'sales.id', '=', 'salepayments.cod_sale')
                            ->join('saleinstallments', 'salepayments.cod_sale', '=', 'saleinstallments.cod_sale')
                            ->join('clients', 'sales.cod_client', '=', 'clients.id')
                            ->where('salepayments.cod_sale', $queryItems[0]->codSale)
                            ->get();

        // Add link criptografado para download
        $link_xml = TokenJWT::encode([
            'userdata' => [
                'xml' => "xml/" . auth()->user()->cnpj . "/" . date('Y-m', strtotime($queryItems[0]->date_emission)) . "/{$queryItems[0]->key}.xml"
            ]
        ]);
        $cod_nfe = TokenJWT::encode([
            'userdata' => [
                    'cod_nfe' => $cod,
                'cancel'    => false
            ]
        ]);

        // Verifica nf-e cancelada
        $queryNFeCancel = $this->NFeEvent->where('cod_nf', $cod)->first();

        $nfeCancelXML = $queryNFeCancel === null ? null :
            TokenJWT::encode([
                'userdata' => [
                    'xml' => "xml/" . auth()->user()->cnpj . "/" . date('Y-m', strtotime($queryNFeCancel->data_cancela)) . "/cancel/{$queryNFeCancel->chave}-cancel.xml"
                ]
            ]);
        $nfeCancelPDF = $queryNFeCancel === null ? null :
            TokenJWT::encode([
                'userdata' => [
                    'cod_nfe' => $cod,
                    'cancel' => true
                ]
            ]);
        $nfeCancel = $queryNFeCancel === null ? false : true;

        $links_cript = (object)[
            'xml'               => $link_xml,
            'cod_nfe'           => $cod_nfe,
            'cod_nfe_cancel'    => $nfeCancelPDF,
            'xml_cancel'        => $nfeCancelXML,
        ];

        return view('admin.sale.viewnfe', compact('dataPayment', 'queryItems', 'links_cript', 'nfeCancel'));
    }

    /**
     * Checks whether the customer and products registration are complete
     *
     * @param int $cod_sale
     * @return null
     */
    public function getValidationNfe(int $cod_sale)
    {
        $error = [];

        $sale = $this->sale
            ->join('saleitems', 'sales.id', '=', 'saleitems.cod_sale')
            ->join('products', 'saleitems.cod_product', '=', 'products.id')
            ->join('clients', 'sales.cod_client', '=', 'clients.id')
            ->join('locales', 'clients.cod_locale', '=', 'locales.id')
            ->leftjoin('cities', 'locales.cod_ibge_city', '=', 'cities.codigo_ibge')
            ->where('sales.id', $cod_sale)
            ->get();

        foreach($sale as $product) {
            if ($product->ncm === null || strlen($product->ncm) !== 8)
                array_push($error, "O produto " . mb_strimwidth($product->description, 0, 50, '...') . " está com o NCM incorreto, complete o cadastro!");
            if($product->subst_trib === 1 && ($product->cest === null || strlen($product->cest) !== 7))
                array_push($error, "O produto " . mb_strimwidth($product->description, 0, 50, '...') . " contem substituição tributária, nesse caso é obrigatório informar o CEST corretamente!");
        }

        /**
         * Validando dados do destinatário
         */
        $rsClient = $sale[0];
        // Verificação dados cliente
        $rsClient->razao_social == "" || $rsClient->razao_social == null ? array_push($error, 'Não foi encontrada informação da razão social do cliente, complete o cadastro!') : false;
        $rsClient->cnpj_cpf == "" || $rsClient->cnpj_cpf == null ? array_push($error, 'Não foi encontrada informação do CNPJ/CPF do cliente, complete o cadastro!') : false;
        $rsClient->place == "" || $rsClient->place == null ? array_push($error, 'Não foi encontrada informação do nome de endereço do cliente, complete o cadastro!') : false;
        $rsClient->number == "" || $rsClient->number == null ? array_push($error, 'Não foi encontrada informação do número de endereço do cliente, complete o cadastro!') : false;
        $rsClient->district == "" || $rsClient->district == null ? array_push($error, 'Não foi encontrada informação do bairro do endereço do cliente, complete o cadastro!') : false;
        $rsClient->cep == "" || $rsClient->cep == null ? array_push($error, 'Não foi encontrada informação do CEP do endereço do cliente, complete o cadastro!') : false;
        $rsClient->nome == "" || $rsClient->nome == null ? array_push($error, 'Não foi encontrada informação do nome da cidade do cliente, complete o cadastro!') : false;

        /**
         * Validando dados do emissor
         */
        // Consulta ao dados da empresa
        $userProfile = $this->profile->where('cnpj', auth()->user()->cnpj)->first();
        // Erros de falta de informação
        $userProfile->razao_social == "" || $userProfile->razao_social == null ? array_push($error, 'Perfil da empresa não contem a razão social cadastrada!') : false;
        $userProfile->cnpj == "" || $userProfile->cnpj == null ? array_push($error, 'Sua conta de perfil não contem um cnpj cadastro, entre em contato com o suporte tecnico!') : false;
        $userProfile->number_start_nfe <= 0 || $userProfile->number_start_nfe == null ? array_push($error, 'Sua conta de perfil não contem o número de inicialização da NFe!') : false;
        $userProfile->regime_trib <= 0 || $userProfile->regime_trib == null ? array_push($error, 'Sua conta de perfil não contem o regime tributário!') : false;
        $userProfile->path_certificado == "" || $userProfile->path_certificado == null ? array_push($error, 'Sua conta de perfil não foi enviado um certificado!') : false;
        $userProfile->pass_certificado == "" || $userProfile->pass_certificado == null ? array_push($error, 'Sua conta de perfil não contem a senha do certificado!') : false;

        /**
         * Validando dados do endereço
         */
        $userProfile->cep == "" || $userProfile->cep == null ? array_push($error, 'Perfil da empresa não contem o CEP cadastrado!') : false;
        $userProfile->place == "" || $userProfile->place == null ? array_push($error, 'Perfil da empresa não contem o endereço cadastrado!') : false;
        $userProfile->number == "" || $userProfile->number == null ? array_push($error, 'Perfil da empresa não contem o número do endereço cadastrado! Caso não tenha número informe como S/N') : false;
        $userProfile->complement == "" || $userProfile->complement == null ? array_push($error, 'Perfil da empresa não contem o complemento cadastrado!') : false;
        $userProfile->district == "" || $userProfile->district == null ? array_push($error, 'Perfil da empresa não contem o bairro cadastrado!') : false;
        $userProfile->cod_ibge_city == "" || $userProfile->cod_ibge_city == null ? array_push($error, 'Perfil da empresa não contem a cidade cadastrada!') : false;

        return $error;
    }

    /**
     * Get data recipient
     *
     * @param $codSale
     * @return mixed
     */
    public function getDataRecipient($codSale)
    {
        return $this->sale
                    ->select(['*', 'cities.nome as name_city', 'states.nome as name_state'])
                    ->join('saleitems', 'sales.id', '=', 'saleitems.cod_sale')
                    ->join('products', 'saleitems.cod_product', '=', 'products.id')
                    ->join('clients', 'sales.cod_client', '=', 'clients.id')
                    ->join('locales', 'clients.cod_locale', '=', 'locales.id')
                    ->leftjoin('cities', 'locales.cod_ibge_city', '=', 'cities.codigo_ibge')
                    ->leftjoin('states', 'cities.codigo_uf', '=', 'states.codigo_uf')
                    ->where('sales.id', $codSale)
                    ->get();
    }

    /**
     * Get datas sale payment
     *
     * @param $codSale
     * @return mixed
     */
    public function getDataSalePayment($codSale)
    {
        return $this->salepayment->where('cod_sale', $codSale)->first();
    }

    /**
     * Get datas sale payment
     *
     * @param $codSale
     * @return mixed
     */
    public function getDataSaleInstallment($codSale)
    {
        return $this->saleinstallment->where('cod_sale', $codSale)->get();
    }

    /**
     * Get datas user authenticated
     *
     * @return mixed
     */
    public function getDataProfileUser()
    {
        return $this->profile
                    ->select(['*', 'cities.nome as name_city', 'states.nome as name_state'])
                    ->leftjoin('cities', 'profiles.cod_ibge_city', '=', 'cities.codigo_ibge')
                    ->leftjoin('states', 'cities.codigo_uf', '=', 'states.codigo_uf')
                    ->where('profiles.cnpj', auth()->user()->cnpj)->first();
    }

    /**
     * @param Request $request
     * @return object
     */
    public function createArrSale(Request $request)
    {
        $arrData = new \stdClass;

        // Recupera ID da venda
        $dataDecode = TokenJWT::decode($request['token_sale']);
        $codSale = $dataDecode->cod_sale;

        //Dados destinatário
        $sale = $this->getDataRecipient($codSale);
        //Dados de pagamento
        $salepayment = $this->getDataSalePayment($codSale);
        //Dados de pagamento
        $saleInstallment = $this->getDataSaleInstallment($codSale);
        // Descrição da natureza
        $infoNature = $this->getInfoNature((int)$request->nature, $sale[0]->tipo_consumidor);
        // Dados usuário autenticado
        $userProfile = $this->getDataProfileUser();

        $arrData->destinatario = [
            "cod"               => $sale[0]->id,
            "typeClient"        => $sale[0]->tipo_cliente,
            "tax_situation"     => $sale[0]->situacao_tributaria,
            "cpf_consumer_fin"  => $sale[0]->cpf_consumidor_final,
            "razao_social"      => $sale[0]->razao_social,
            "fantasia"          => $sale[0]->fantasia,
            "cnpj_cpf"          => $sale[0]->cnpj_cpf,
            "rg_ie"             => $sale[0]->rg_ie,
            "im"                => $sale[0]->im,
            "email"             => $sale[0]->email,
            "telephone"         => $sale[0]->telefone,
            "cellphone"         => $sale[0]->celular,
            "typeConsumer"      => $sale[0]->tipo_consumidor === "final" ? 0 : 1,
            "place"             => $sale[0]->place,
            "number"            => $sale[0]->number,
            "complement"        => $sale[0]->complement,
            "district"          => $sale[0]->district,
            "cod_ibge_city"     => $sale[0]->cod_ibge_city,
            "cep"               => $sale[0]->cep,
            "city"              => $sale[0]->name_city,
            "state"             => $sale[0]->name_state,
            "cod_uf"            => $sale[0]->codigo_uf,
            "uf"                => $sale[0]->uf
        ];
        $arrData->emitente = [
            "razao_social"      => $userProfile->razao_social,
            "fantasia"          => $userProfile->fantasia,
            "cnpj"              => $userProfile->cnpj,
            "telephone"         => $userProfile->telephone,
            "ie"                => $userProfile->ie,
            "im"                => $userProfile->im,
            "iest"              => $userProfile->iest,
            "cnae"              => $userProfile->cnae,
            "number_start_nfe"  => $userProfile->number_start_nfe,
            "regime_trib"       => $userProfile->regime_trib,
            "place"             => $userProfile->place,
            "number"            => $userProfile->number,
            "complement"        => $userProfile->complement,
            "district"          => $userProfile->district,
            "cod_ibge_city"     => $userProfile->cod_ibge_city,
            "cep"               => $userProfile->cep,
            "city"              => $userProfile->name_city,
            "state"             => $userProfile->name_state,
            "pass_certificado"  => $userProfile->pass_certificado,
            "codigo_uf"         => $userProfile->codigo_uf,
            "uf"                => $userProfile->uf,
        ];
        $arrData->items   = [];
        // Informações gerais da NFe
        $arrData->nfe = [
            'cod_sale'  => $codSale,
            'cUF'       => $arrData->emitente['codigo_uf'],
            'nature'    => ['natOp' => $infoNature->description, "cfop_state" => $infoNature->cfop_state, "cfop_state_st" => $infoNature->cfop_state_st, "cfop_no_state" => $infoNature->cfop_no_state, "cfop_no_state_st" => $infoNature->cfop_no_state_st],
            'mod'       => 55,
            'serie'     => 1,
            'nNF'       => $arrData->emitente['number_start_nfe'],
            'tpNF'      => 1, // 1 - Entrada / 2 - Saida
            'idDest'    => $arrData->emitente['codigo_uf'] === $arrData->destinatario['cod_uf'] ? 1 : 2, // 1 - Dentro do estado /  2- Fora do estado
            'cMunFG'    => (int)$arrData->emitente['cod_ibge_city'],
            'finNFe'    => (int)$request->finality, // 1-NF-e Normal / 2-NF-e Complementar / 3-NF-e de Ajuste
            'indFinal'  => $arrData->destinatario['typeConsumer'], // Tipo consumidor
            'frete'     => $request->shipping,
            'sendQnty'  => (int)$this->sanitizeNumberBr($request->qnty),
            'sendSpecie'=> $request->specie,
            'Gweight'   => $this->sanitizeNumberBr($request->gross_weight),
            'Lweight'   => $this->sanitizeNumberBr($request->liquid_weight),
            'parcelas'  => $salepayment->quantity_installment,
            'totalB'    => $salepayment->gross_value,
            'totalL'    => $salepayment->liquid_value,
            'discount'  => $salepayment->discount,
            'message_complement'    => $request->message_complement,
        ];


        $arrData->nfe['parcela'] = [];
        foreach ($saleInstallment as $installment){
            array_push($arrData->nfe['parcela'], [
                'nDup' => str_pad($installment->installment_number , 3 , '0' , STR_PAD_LEFT),
                'dVenc' => $installment->due_date,
                'vDup'  => $installment-> value
            ]);
        }

        foreach($sale as $iten){
            array_push($arrData->items, (object)[
                "cod_product"               => $iten->cod_product,
                "qnty_iten"                 => $iten->qnty_iten,
                "value_iten"                => $iten->value_iten,
                "icms_st_iten"              => $iten->icms_st_iten,
                "base_icms_st_iten"         => $iten->base_icms_st_iten,
                "icms_iten"                 => $iten->icms_iten,
                "base_icms_iten"            => $iten->base_icms_iten,
                "st_iten"                   => $iten->st_iten,
                "icms_perc_iten"            => $iten->icms_perc_iten,
                "ipi_iten"                  => $iten->ipi_iten,
                "ipi_perc_iten"             => $iten->ipi_perc_iten,
                "discount_iten"             => $iten->discount_iten,
                "have_st_iten"              => $iten->have_st_iten,
                "value_total_iten"          => $iten->value_total_iten,
                "complement_product_iten"   => $iten->complement_product_iten == "" ? 0 : $iten->complement_product_iten,
                "description"               => $iten->description,
                "bar_code"                  => $iten->bar_code === null ? "SEM GTIN" : $iten->bar_code,
                "unity"                     => $iten->unity,
                "ncm"                       => $iten->ncm,
                "cest"                      => $iten->cest,
                "incid_pis_cofins"          => $iten->incid_pis_cofins,
                "indic_produc"              => $iten->indic_produc,
                "isento"                    => $iten->isento,
                "imune"                     => $iten->imune,
                "suspensao_icms"            => $iten->suspensao_icms,
            ]);
        }

        return $arrData;
    }

    /**
     * @param int $cod
     * @param string $tipo_consumidor
     * @return mixed
     */
    public function getInfoNature(int $cod, string $tipo_consumidor)
    {
        return $this->nature->where([['id', $cod], ['customer_type', $tipo_consumidor === "final" ? 0 : 1]])->first();
    }

    /**
     * Download file XML NFe
     *
     * @param $file_name
     * @return mixed
     */
    public function downloadXML(string $file_name)
    {
        // Recupera url criptografado
        $dataDecode = TokenJWT::decode($file_name);
        $urlDownload = $dataDecode->xml;

        $path = storage_path()."/app/public/file/{$urlDownload}";
        if (file_exists($path)) {
            return Response::download($path);
        }
    }
    /**
     * Download file DANFE in PDF
     *
     * @param $cod
     */
    public function downloadPDF(string $cod)
    {
        // Recupera dados nfe
        $dataDecode = TokenJWT::decode($cod);
        $codNFe     = $dataDecode->cod_nfe;
        $cancelNFe  = $dataDecode->cancel;

        $queryNfe       = $this ->NFe->where('cod_nf', $codNFe)->first();
        $cnpj           = auth()->user()->cnpj;
        $key            = $queryNfe->key;
        $complementNFe  = $cancelNFe ? '-cancelPDF' : '';
        $complementUser = $cancelNFe ? '-CANCELADA' : '';
        $pathCancel     = $cancelNFe ? 'cancel/' : '';
        $datePath       = date('Y-m', strtotime($queryNfe->date_emission));

        $xml    = file_get_contents(storage_path() . "/app/public/file/xml/{$cnpj}/{$datePath}/{$pathCancel}{$key}{$complementNFe}.xml");
        $logo   = 'data://text/plain;base64,'. base64_encode(file_get_contents(storage_path() . "/app/public/file/logotipos/{$cnpj}/logotipo.jpg"));

        try {
            $danfe = new Danfe($xml);
            $danfe->debugMode(false);
            $danfe->creditsIntegratorFooter('WEBNFe Sistemas - http://www.webenf.com.br');
            $danfe->monta($logo);
            $pdf = $danfe->downloadDanfe("{$key}{$complementUser}-DANFE.pdf");

//            header('Content-Type: application/pdf');
//            echo $pdf;
        } catch (InvalidArgumentException $e) {
            echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
        }
    }

    public function cancelNFe(int $id)
    {
        $profile    = $this->getDataProfileUser(); // Dados usuário autenticado
        $configNFe  = $this->createConfigNFe($profile->razao_social, $profile->uf, $profile->cnpj); // Array configuração
        $NFe        = $this->NFe->where('cod_nf', $id)->first();
        $cnpj       = auth()->user()->cnpj;
        $datePath   = date('Y-m');
        $pathXML    = file_get_contents(storage_path() . "/app/public/file/xml/{$cnpj}/{$datePath}/{$NFe->key}.xml");

        $certificate = Certificate::readPfx(file_get_contents(storage_path("app/public/file/certificados/" . auth()->user()->cnpj . "/certificado.pfx")), $profile->pass_certificado);
        $tools = new Tools(json_encode($configNFe), $certificate);
        $tools->model('55');

        $xJust = 'Erro de digitação nos dados dos produtos';
        $response = $tools->sefazCancela($NFe->key, $xJust, $NFe->protocolo);

        //você pode padronizar os dados de retorno atraves da classe abaixo
        //de forma a facilitar a extração dos dados do XML
        //NOTA: mas lembre-se que esse XML muitas vezes será necessário,
        //quando houver a necessidade de protocolos

        $stdCl = new Standardize($response);
        //nesse caso $std irá conter uma representação em stdClass do XML
        $std = $stdCl->toStd();

        $nfeCancelXML = TokenJWT::encode([
                'userdata' => [
                    'xml' => "xml/" . auth()->user()->cnpj . "/" . date('Y-m', strtotime($std->retEvento->infEvento->dhRegEvento)) . "/cancel/{$std->retEvento->infEvento->chNFe}-cancel.xml"
                ]
            ]);
        $nfeCancelPDF = TokenJWT::encode([
                'userdata' => [
                    'cod_nfe' => $id,
                    'cancel' => true
                ]
            ]);

        //verifique se o evento foi processado
        if ($std->cStat == 128) {
            $cStat = $std->retEvento->infEvento->cStat;
            if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
                $xml                = Complements::toAuthorize($tools->lastRequest, $response);

                $xmlCancelMarcaAgua = Complements::cancelRegister($pathXML, $xml);

                //grave o XML protocolado
                $chave                  = $std->retEvento->infEvento->chNFe. "-cancel.xml";
                $chaveCancelMarcaAgua   = $std->retEvento->infEvento->chNFe. "-cancelPDF.xml";

                $this->createPasteNotExistStorage("app/public/file/xml/{$cnpj}");
                $this->createPasteNotExistStorage("app/public/file/xml/{$cnpj}/{$datePath}");
                $this->createPasteNotExistStorage("app/public/file/xml/{$cnpj}/{$datePath}/cancel");

                file_put_contents(storage_path("app/public/file/xml/{$cnpj}/{$datePath}/cancel/{$chave}"), $xml);
                file_put_contents(storage_path("app/public/file/xml/{$cnpj}/{$datePath}/cancel/{$chaveCancelMarcaAgua}"), $xmlCancelMarcaAgua);

                $arrEvent = [
                    'cod_nf'            => $id,
                    'data_cancela'      => date('Y-m-d H:i:s', strtotime($std->retEvento->infEvento->dhRegEvento)),
                    'motivo_cancela'    => $xJust,
                    'seq'               => $std->retEvento->infEvento->nSeqEvento,
                    'chave'             => $std->retEvento->infEvento->chNFe,
                    'protocolo'         => $std->retEvento->infEvento->nProt,
                    'tipo_evento'       => $std->retEvento->infEvento->tpEvento,
                    'cod_user_reg'      => auth()->user()->id
                ];

                $this->NFeEvent->create($arrEvent);
            }
        }

        //nesse caso o $json irá conter uma representação em JSON do XML
        echo json_encode([$stdCl->toArray(), [$nfeCancelXML, $nfeCancelPDF]]);
    }
}
