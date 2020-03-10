<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use NFePHP\NFe\Make;
use stdClass;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use App\Models\Product\Product;
use App\Http\Controllers\Controller;

class NfeService {

    // Configuração
    private $config;
    private $configJson;
    private $contentpfx;
    private $passwordpfx;
    private $tools;

    // Iidentificação
    private $cUF;
    private $natOp;
    private $mod;
    private $serie;
    private $nNF;
    private $tpNF;
    private $idDest;
    private $cMunFG;
    private $finNFe;
    private $indFinal;
    private $cNF;
    private $dhEmi;
    private $dhSaiEnt;
    private $tpImp;
    private $tpEmis;
    private $cDV;
    private $tpAmb;
    private $indPres;
    private $procEmi;
    private $verProc;
    private $dhCont;
    private $xJust;
    private $arrCFOP;

    // Info
    private $frete;
    private $vol_qnt;
    private $vol_especie;
    private $vol_pesoB;
    private $vol_pesoL;
    private $tPag;
    private $vPag;
    private $indPag;
    private $message_complement;
    private $discount;
    private $installment;
    private $vOrig;
    private $vLiq;

    // Emitente
    private $xNome_emit;
    private $xFant_emit;
    private $IE_emit;
    private $IEST_emit;
    private $IM_emit;
    private $CNAE_emit;
    private $CRT_emit;
    private $identidade_emit;
    private $xLgr_emit;
    private $nro_emit;
    private $xClp_emit;
    private $xBairro_emit;
    private $cMun_emit;
    private $xMun_emit;
    private $UF_emit;
    private $CEP_emit;
    private $cPais_emit;
    private $xPais_emit;
    private $fone_emit;

    // Destinatário
    private $typeConsumer;
    private $tax_situation;
    private $cpf_consumer_fin;
    private $tipo_cliente;
    private $xNome_dest;
    private $indIEDest_dest;
    private $IE_dest;
    private $IM_dest;
    private $email_dest;
    private $identidade_dest;
    private $ISUF_dest;
    private $idEstrangeiro_dest;
    private $xLgr_dest;
    private $nro_dest;
    private $xClp_dest;
    private $xBairro_dest;
    private $cMun_dest;
    private $xMun_dest;
    private $UF_dest;
    private $CEP_dest;
    private $cPais_dest;
    private $xPais_dest;
    private $fone_dest;

    private $products;

    /**
     * NfeService constructor.
     * @param $config
     */
    public function __construct($data)
    {
        $this->setInfo($data->nfe);
        $this->setEmit($data->emitente);
        $this->setDest($data->destinatario);

        $this->config       = $data->config;
        $this->configJson   = json_encode($data->config);
        $this->products     = $data->items;

        $configCerticado = Certificate::readPfx($this->contentpfx, $this->passwordpfx);
        if(preg_match('/error:23076071/', $configCerticado)){
            return ['error' => 'Erro de senha'];
        }

        $this->tools = new Tools($this->configJson, $configCerticado);
    }

    /**
     * @return string
     */
    public function gerarNfe()
    {
        // Cria uma nota vázia
        $nfe = new Make();

        /**
        |--------------------------------------------------------------------------
        | INF NFE
        |--------------------------------------------------------------------------
        */
        $stdInNfe = new stdClass();
        $stdInNfe->versao   = '4.00'; //versão do layout (string)
        $stdInNfe->pk_nItem = null; //deixe essa variavel sempre como NULL
        // Resultado obj
        $nfe->taginfNfe($stdInNfe);

        /**
        |--------------------------------------------------------------------------
        | Identificação NFe
        |--------------------------------------------------------------------------
        */
        $stdIde = new stdClass();
        $stdIde->cUF        = $this->cUF;
        $stdIde->cNF        = $this->cNF;
        $stdIde->natOp      = $this->natOp;
        $stdIde->mod        = $this->mod;
        $stdIde->serie      = $this->serie;
        $stdIde->nNF        = $this->nNF;
        $stdIde->dhEmi      = $this->dhEmi;
        $stdIde->dhSaiEnt   = $this->dhSaiEnt;
        $stdIde->tpNF       = $this->tpNF;
        $stdIde->idDest     = $this->idDest;
        $stdIde->cMunFG     = $this->cMunFG;
        $stdIde->tpImp      = $this->tpImp;
        $stdIde->tpEmis     = $this->tpEmis;
        $stdIde->cDV        = $this->cDV;
        $stdIde->tpAmb      = $this->tpAmb;
        $stdIde->finNFe     = $this->finNFe;
        $stdIde->indFinal   = $this->indFinal;
        $stdIde->indPres    = $this->indPres;
        $stdIde->procEmi    = $this->procEmi;
        $stdIde->verProc    = $this->verProc;
        $stdIde->dhCont     = $this->dhCont;
        $stdIde->xJust      = $this->xJust;
        // Resultado obj
        $nfe->tagide($stdIde);

        /**
        |--------------------------------------------------------------------------
        | EMITENTE
        |--------------------------------------------------------------------------
        */
        $stdEmit = new stdClass();
        $stdEmit->xNome = $this->xNome_emit;
        $stdEmit->xFant = $this->xFant_emit;
        $stdEmit->IE    = $this->IE_emit;
        $stdEmit->IEST  = $this->IEST_emit;
        $stdEmit->IM    = $this->IM_emit;
        $stdEmit->CNAE  = $this->CNAE_emit;
        $stdEmit->CRT   = $this->CRT_emit;
        $stdEmit->CPF   = strlen($this->identidade_emit) === 11 ? $this->identidade_emit : null;
        $stdEmit->CNPJ  = strlen($this->identidade_emit) === 14 ? $this->identidade_emit : null;

        // Resultado obj
        $nfe->tagemit($stdEmit);

        /**
        |--------------------------------------------------------------------------
        | ENDEREÇO EMITENTE
        |--------------------------------------------------------------------------
        */
        $stdEndEmit = new stdClass();
        $stdEndEmit->xLgr       = $this->xLgr_emit;
        $stdEndEmit->nro        = $this->nro_emit;
        $stdEndEmit->xCpl       = $this->xClp_emit;
        $stdEndEmit->xBairro    = $this->xBairro_emit;
        $stdEndEmit->cMun       = $this->cMun_emit;
        $stdEndEmit->xMun       = $this->xMun_emit;
        $stdEndEmit->UF         = $this->UF_emit;
        $stdEndEmit->CEP        = $this->CEP_emit;
        $stdEndEmit->cPais      = $this->cPais_emit;
        $stdEndEmit->xPais      = $this->xPais_emit;
        $stdEndEmit->fone       = $this->fone_emit;
        // Resultado obj
        $nfe->tagenderEmit($stdEndEmit);

        /**
        |--------------------------------------------------------------------------
        | DESTINATÁRIO
        |--------------------------------------------------------------------------
        */
        $stdDest = new stdClass();
        $stdDest->xNome         = $this->xNome_dest;
        $stdDest->indIEDest     = $this->indIEDest_dest; // 1 - Tem IE / 2 = Não tem IE
        $stdDest->IE            = $this->IE_dest;
        $stdDest->IM            = $this->IM_dest;
        $stdDest->email         = $this->email_dest;
        $stdDest->CPF           = $this->tipo_cliente == "pf" ? $this->identidade_dest : null;
        $stdDest->CNPJ          = $this->tipo_cliente == "pj" ? $this->identidade_dest : null;
        $stdDest->ISUF          = $this->ISUF_dest;
        $stdDest->idEstrangeiro = $this->idEstrangeiro_dest;

        // Resultado obj
        $nfe->tagdest($stdDest);

        /**
        |--------------------------------------------------------------------------
        | ENDEREÇO DESTINATÁRIO
        |--------------------------------------------------------------------------
        */
        $stdEndDest = new stdClass();
        $stdEndDest->xLgr       = $this->xLgr_dest;
        $stdEndDest->nro        = $this->nro_dest;
        $stdEndDest->xCpl       = $this->xClp_dest;
        $stdEndDest->xBairro    = $this->xBairro_dest;
        $stdEndDest->cMun       = $this->cMun_dest;
        $stdEndDest->xMun       = $this->xMun_dest;
        $stdEndDest->UF         = $this->UF_dest;
        $stdEndDest->CEP        = $this->CEP_dest;
        $stdEndDest->cPais      = $this->cPais_dest;
        $stdEndDest->xPais      = $this->xPais_dest;
        $stdEndDest->fone       = $this->fone_dest;
        // Resultado obj
        $nfe->tagenderDest($stdEndDest);

        /**
        |--------------------------------------------------------------------------
        | PRODUTOS
        |--------------------------------------------------------------------------
        */
        foreach ($this->products as $key => $product) {
            $CST_CSOSN      = $this->getCSTeCSOSN($product);
            $CFOP           = $this->getCFOPProduct($product->cod_product);

            $itenNfe = $key + 1;
            /**
            |--------------------------------------------------------------------------
            | INFO PRODUTO
            |--------------------------------------------------------------------------
            */
            $stdProd = new stdClass();
            $stdProd->item      = $itenNfe; //item da NFe
            $stdProd->cProd     = $product->cod_product;
            $stdProd->cEAN      = $product->bar_code;
            $stdProd->xProd     = $product->description;
            $stdProd->NCM       = $product->ncm;

            $stdProd->cBenef    = ""; //incluido no layout 4.00

            $stdProd->EXTIPI    = ""; // Preencher de acordo com o código EX da TIPI
            $stdProd->CFOP      = $CFOP;
            $stdProd->uCom      = $product->unity;
            $stdProd->qCom      = number_format($product->qnty_iten, 2 , '.', '');
            $stdProd->vUnCom    = number_format($product->value_iten, 2 , '.', '');
            $stdProd->vProd     = number_format($product->value_iten * $product->qnty_iten, 2 , '.', '');
            $stdProd->cEANTrib  = $product->bar_code;
            $stdProd->uTrib     = $product->unity;
            $stdProd->qTrib     = number_format($product->qnty_iten, 2 , '.', '');
            $stdProd->vUnTrib   = number_format($product->value_iten, 2 , '.', '');
            $stdProd->vFrete    = ""; // Valor Total do Frete
            $stdProd->vSeg      = ""; // Valor Total do Seguro
            $stdProd->vDesc     = $product->discount_iten == 0 ? "" : number_format($product->discount_iten, 2 , '.', '');
            $stdProd->vOutro    = ""; // Outras Despesas acessórias
            $stdProd->indTot    = 1; // Indica se valor do Item (vProd) compõe o valor total da NF-e (vProd) - 0 compõe / 1- não compõe
            $stdProd->xPed      = ""; // Número do Pedido de Compra
            $stdProd->nItemPed  = ""; // Item do Pedido de Compra
            $stdProd->nFCI      = "";
            $nfe->tagprod($stdProd);
            /**
            * |------------------------------
            * | CEST
            * |------------------------------
            */
            $stdCEST = new stdClass();
            $stdCEST->item = $itenNfe; //item da NFe
            $stdCEST->CEST = $product->cest; //item da NFe
            $nfe->tagCEST($stdCEST);
            /**
            |--------------------------------------------------------------------------
            | INFO COMPLEMENTAR PRODUTO
            |--------------------------------------------------------------------------
            */
            $stdProdAdc = new stdClass();
            $stdProdAdc->item = $itenNfe; //item da NFe
            $stdProdAdc->infAdProd = $product->complement_product_iten;
            $nfe->taginfAdProd($stdProdAdc);

            /**
            |--------------------------------------------------------------------------
            | IMPOSTO
            |--------------------------------------------------------------------------
            */
            $stdImp = new stdClass();
            $stdImp->item = $itenNfe; //item da NFe
            $stdImp->vTotTrib = $product->icms_st_iten;
            $nfe->tagimposto($stdImp);

            /**
            |--------------------------------------------------------------------------
            | ICMS
            |--------------------------------------------------------------------------
            */
            $stdICMS = new stdClass();
            $stdICMS->item          = $itenNfe; //item da NFe
            $stdICMS->orig          = 0; // Origem mercadoria -- 0 - Nacional / 1 – Estrangeira importação direta / 2 – Estrangeira adquirida no mercado interno.
            $this->CRT_emit === 1 ? $stdICMS->CSOSN = $CST_CSOSN : $stdICMS->CST = $CST_CSOSN;
            $stdICMS->modBC         = "0"; // Modalidade de determinação da BC do ICMS
            $stdICMS->vBC           = number_format($product->base_icms_iten, 2 , '.', '');
            $stdICMS->pICMS         = number_format($product->icms_perc_iten, 2 , '.', '');
            $stdICMS->vICMS         = number_format($product->icms_iten, 2 , '.', '');
            if ($this->CRT_emit === 1) $stdICMS->pCredSN = "0"; // icmssn
            if ($this->CRT_emit === 1) $stdICMS->vCredICMSSN = "0"; // icmssn
            $stdICMS->modBCST       = "0"; // Modalidade de determinação / 0 - Preço tabelado
            $stdICMS->pMVAST        = number_format($product->st_iten, 2 , '.', ''); // Percentual da margem de valor Adicionado do ICMS ST
            $stdICMS->vBCST         = number_format($product->base_icms_st_iten, 2 , '.', ''); // Valor da BC do ICMS ST
            $stdICMS->pICMSST       = number_format($product->st_iten, 2 , '.', '');
            $stdICMS->vICMSST       = number_format($product->icms_st_iten, 2 , '.', '');
            $stdICMS->pRedBCST      = null; // Percentual da Redução de BC do ICMS ST
            $stdICMS->pFCP          = null;
            $stdICMS->vFCP          = null;
            $stdICMS->vBCFCP        = null;
            $stdICMS->vBCFCPST      = null;
            $stdICMS->pFCPST        = null;
            $stdICMS->vFCPST        = null;
            $stdICMS->vICMSDeson    = null;
            $stdICMS->motDesICMS    = null;
            $stdICMS->pRedBC        = null;
            $stdICMS->vICMSOp       = null;
            $stdICMS->pDif          = null;
            $stdICMS->vICMSDif      = null;
            $stdICMS->vBCSTRet      = null;
            $stdICMS->pST           = null;
            $stdICMS->vICMSSTRet    = null;
            $stdICMS->vBCFCPSTRet   = null;
            $stdICMS->pFCPSTRet     = null;
            $stdICMS->vFCPSTRet     = null;
            $stdICMS->pRedBCEfet    = null;
            $stdICMS->vBCEfet       = null;
            $stdICMS->pICMSEfet     = null;
            $stdICMS->vICMSEfet     = null;
            $stdICMS->vICMSSubstituto = null; //NT2018.005_1.10_Fevereiro de 2019

            $this->CRT_emit === 1 ? $nfe->tagICMSSN($stdICMS) : $nfe->tagICMS($stdICMS);

            /**
            |--------------------------------------------------------------------------
            | PIS
            |--------------------------------------------------------------------------
            */
            $stdPIS = new stdClass();
            $stdPIS->item = $itenNfe; //item da NFe
            $stdPIS->CST = "05";
//            $stdPIS->vBC = $this->CRT_emit === 1 ? null : $stdProd->vProd;
//            $stdPIS->pPIS = $this->CRT_emit === 1 ? null : "0.65";
//            $stdPIS->vPIS = $this->CRT_emit === 1 ? null : number_format($stdPIS->vBC * ($stdPIS->pPIS / 100), 2, '.', '');
//            $stdPIS->qBCProd = null;
//            $stdPIS->vAliqProd = null;
            $nfe->tagPIS($stdPIS);

            /**
            |--------------------------------------------------------------------------
            | COFINS
            |--------------------------------------------------------------------------
            */
            $stdCOFINS = new stdClass();
            $stdCOFINS->item = $itenNfe; //item da NFe
            $stdCOFINS->CST = "05";
//            $stdCOFINS->vBC = $this->CRT_emit === 1 ? null : $stdProd->vProd;
//            $stdCOFINS->pCOFINS = $this->CRT_emit === 1 ? null : "3.00";
//            $stdCOFINS->vCOFINS = $this->CRT_emit === 1 ? null : number_format($stdCOFINS->vBC * ($stdCOFINS->pCOFINS / 100), 2, '.', '');
//            $stdCOFINS->qBCProd = null;
//            $stdCOFINS->vAliqProd = null;
            $nfe->tagCOFINS($stdCOFINS);
        }
        /**
        |--------------------------------------------------------------------------
        | TOTAIS
        |--------------------------------------------------------------------------
        */
        $stdTotais = new stdClass();
//        $stdTotais->vBC = "0";
//        $stdTotais->vICMS = "0";
//        $stdTotais->vICMSDeson = "0";
//        $stdTotais->vFCP = "0"; //incluso no layout 4.00
//        $stdTotais->vBCST = "0";
//        $stdTotais->vST = "0";
//        $stdTotais->vFCPST = "0"; //incluso no layout 4.00
//        $stdTotais->vFCPSTRet = "0"; //incluso no layout 4.00
//        $stdTotais->vProd = "0";
        $stdTotais->vFrete = $this->frete;
//        $stdTotais->vSeg = "0";
//        $stdTotais->vDesc = $this->discount;
//        $stdTotais->vII = "0";
//        $stdTotais->vIPI = "0";
//        $stdTotais->vIPIDevol = "0"; //incluso no layout 4.00
//        $stdTotais->vPIS = "0";
//        $stdTotais->vCOFINS = "0";
//        $stdTotais->vOutro = "0";
//        $stdTotais->vNF = "0";
//        $stdTotais->vTotTrib = "0";
        $nfe->tagICMSTot($stdTotais);

        /**
        |--------------------------------------------------------------------------
        | TRANSPORTADORA
        |--------------------------------------------------------------------------
        */
        $stdTransp = new stdClass();
        $stdTransp->modFrete = $this->frete;
        $nfe->tagtransp($stdTransp);

        /**
        |--------------------------------------------------------------------------
        | VOLUMES
        |--------------------------------------------------------------------------
        */
        $stdVol = new stdClass();
        $stdVol->item = 1; //indicativo do numero do volume
        $stdVol->qVol = $this->vol_qnt;
        $stdVol->esp = $this->vol_especie;
        // $stdVol->marca = 'OLX';
        $stdVol->nVol = $this->vol_qnt;
        $stdVol->pesoL = $this->vol_pesoL;
        $stdVol->pesoB = $this->vol_pesoB;
        $nfe->tagvol($stdVol);

        /**
        |--------------------------------------------------------------------------
        | FORMAS DE PAGAMENTO
        |--------------------------------------------------------------------------
        */
        $stdPag = new stdClass();
        $stdPag->vTroco = ""; //incluso no layout 4.00, obrigatório informar para NFCe (65)
        $nfe->tagpag($stdPag);

        /**
        |--------------------------------------------------------------------------
        | DATALHE DO PAGAMENTO
        |--------------------------------------------------------------------------
         */
        $stdPgto = new stdClass();
        $stdPgto->tPag = $this->tPag;
        $stdPgto->vPag = number_format($this->vPag, 2, '.', ''); //Obs: deve ser informado o valor pago pelo cliente
        $stdPgto->CNPJ = null;
        $stdPgto->tBand = null;
        $stdPgto->cAut = null;
        $stdPgto->tpIntegra = null; //incluso na NT 2015/002
        $stdPgto->indPag = $this->indPag; //0= Pagamento à Vista 1= Pagamento à Prazo
        $nfe->tagdetPag($stdPgto);

        /**
        |--------------------------------------------------------------------------
        | DUPLICATA
        |--------------------------------------------------------------------------
         */

        /**
        |--------------------------------------------------------------------------
        | DUPLICATA
        |--------------------------------------------------------------------------
         */
        $stdFat = new stdClass();
        $stdFat->nFat   = "001";
        $stdFat->vLiq   = $this->vLiq;
        $stdFat->vOrig  = $this->vOrig;
        $stdFat->vDesc  = "0.00";
        $nfe->tagfat($stdFat);

        foreach ($this->installment as $parcela){
            $stdDup = new stdClass();
            $stdDup->nDup   = $parcela['nDup'];
            $stdDup->dVenc  = $parcela['dVenc'];
            $stdDup->vDup   = $parcela['vDup'];
            $nfe->tagdup($stdDup);
        }

        /**
        |--------------------------------------------------------------------------
        | INFORMAÇÃO ADICIONAL
        |--------------------------------------------------------------------------
        */
        $stdInfAdc = new stdClass();
//        $stdInfAdc->infAdFisco = 'informacoes para o fisco';
        $stdInfAdc->infCpl = $this->message_complement;
        $nfe->taginfAdic($stdInfAdc);

        /**
        |--------------------------------------------------------------------------
        | MONTA A NOTA
        |--------------------------------------------------------------------------
        */
        if ($nfe->montaNFe()){
            if(count($nfe->getErrors()) > 0) return ['error' => $nfe->getErrors()];
            return $nfe->getXML();
        }
        else throw Exception("Erro ao gerar NFe");
    }

    /**
     * Sign NFe
     * @param $xml
     * @return string signed NFe xml
     */
    public function sign($xml)
    {
        return $this->tools->signNFe($xml);
    }

    /**
     * @param $signed_xml
     * @return string
     */
    public function transmit($signed_xml)
    {
        $resp = $this->tools->sefazEnviaLote([$signed_xml], 1, 1);

        $st = new Standardize();
        $stdAnswer = $st->toStd($resp);

        try {
            $chave = $stdAnswer->protNFe->infProt->chNFe. ".xml";
            $cnpj = auth()->user()->cnpj;
            $datePath = date('Y-m');
            $xml = Complements::toAuthorize($signed_xml, $resp);

            Controller::createPasteNotExistStorage("app/public/file/xml/{$cnpj}");
            Controller::createPasteNotExistStorage("app/public/file/xml/{$cnpj}/{$datePath}");

            file_put_contents(storage_path("app/public/file/xml/{$cnpj}/{$datePath}/{$chave}"), $xml);

            return [
                'chave' => $stdAnswer->protNFe->infProt->chNFe,
                'status' => $stdAnswer->protNFe->infProt->cStat,
                'motivo' => $stdAnswer->protNFe->infProt->xMotivo,
                'dataHora' => $stdAnswer->protNFe->infProt->dhRecbto,
                'protocolo' => $stdAnswer->protNFe->infProt->nProt
            ];
        } catch (\Exception $e) {
            return [
                'erro' =>  $e->getMessage()
            ];
        }
    }

    /**
     * @param $cUF
     * @param $natOp
     * @param $mod
     * @param $serie
     * @param $nNF
     * @param $tpNF
     * @param $idDest
     * @param $cMunFG
     * @param $indFinal
     */
    public function setInfo($info)
    {
        // IDE
        $this->cUF      = $info['cUF'];
        $this->natOp    = $info['nature']['natOp'];
        $this->mod      = $info['mod'];
        $this->serie    = $info['serie'];
        $this->nNF      = $info['nNF'];
        $this->tpNF     = $info['tpNF'];
        $this->idDest   = $info['idDest'];
        $this->cMunFG   = $info['cMunFG'];
        $this->finNFe   = $info['finNFe'];
        $this->indFinal = $info['indFinal'];
        // Valores fixos
        $this->cNF      = $info['cod_sale'];
        $this->dhEmi    = date('Y-m-d\TH:i:sP');
        $this->dhSaiEnt = date('Y-m-d\TH:i:sP');
        $this->tpImp    = 1; // 1-Retrato / 2-Paisagem
        $this->tpEmis   = 1; // 1 - Normal / 2- Contingência, etc....
        $this->cDV      = 0; // Dígito verificador
        $this->tpAmb    = 2; // 1-Produção / 2-Homologação
        $this->indPres  = 0; // Se o cliente estava presente
        $this->procEmi  = 0; // Processo de emissão da NF-e
        $this->verProc  = '1.0.0'; // Versão sistema emisor
        $this->dhCont   = null; // Data/hora contingência
        $this->xJust    = null; // Justificativa contingência

        // Info NFe
        $this->frete        = $info['frete'];
        $this->vol_qnt      = $info['sendQnty'];
        $this->vol_especie  = $info['sendSpecie'];
        $this->vol_pesoB    = $info['Gweight'];
        $this->vol_pesoL    = $info['Lweight'];
        $this->discount     = $info['discount'];
        $this->installment  = $info['parcela'];

        $this->message_complement   = $info['message_complement'];

        $this->indPag       =  $info['parcelas'] == 1 ? 0 : 1;
        $this->tPag         = '14';
        $this->vPag         = $info['totalB']; //Obs: deve ser informado o valor pago pelo cliente
        $this->vLiq         = $info['totalL'];
        $this->vOrig        = $info['totalL'];

        $this->arrCFOP      = $info['nature'];
        unset($this->arrCFOP['natOp']);
    }

    /**
     * @param $emit
     */
    public function setEmit($emit)
    {
        $this->xNome_emit       = $emit['razao_social'];
        $this->xFant_emit       = $emit['fantasia'];
        $this->IE_emit          = $emit['ie'];
        $this->IEST_emit        = $emit['iest'];
        $this->IM_emit          = $emit['im'];
        $this->CNAE_emit        = $emit['cnae'];
        $this->CRT_emit         = $emit['regime_trib'];
        $this->identidade_emit  = $emit['cnpj'];
        $this->xLgr_emit        = $emit['place'];
        $this->nro_emit         = $emit['number'];
        $this->xClp_emit        = $emit['complement'];
        $this->xBairro_emit     = $emit['district'];
        $this->cMun_emit        = $emit['cod_ibge_city'];
        $this->xMun_emit        = $emit['city'];
        $this->UF_emit          = $emit['uf'];
        $this->CEP_emit         = $emit['cep'];
        $this->cPais_emit       = "1058";
        $this->xPais_emit       = "Brasil";
        $this->passwordpfx      = $emit['pass_certificado'];
        $this->contentpfx       = file_get_contents(storage_path("app/public/file/certificados/". auth()->user()->cnpj ."/certificado.pfx"));
    }

    /**
     * @param $dest
     */
    public function setDest($dest)
    {
        $this->tipo_cliente     = $dest['typeClient'];
        $this->tax_situation    = $dest['tax_situation'];
        $this->cpf_consumer_fin = $dest['cpf_consumer_fin'];
        $this->typeConsumer     = $dest['typeConsumer'];
        $this->xNome_dest       = $dest['razao_social'];
        $this->IE_dest          = $dest['typeClient'] == "pj" ? $dest['rg_ie'] : null;
        $this->indIEDest_dest   = $dest['rg_ie'] !== null ? 1 : 0;
        $this->IM_dest          = $dest['im'];
        $this->email_dest       = $dest['email'];
        $this->identidade_dest  = $dest['cnpj_cpf'];
        $this->ISUF_dest        = null;
        $this->idEstrangeiro_dest = null;
        $this->xLgr_dest        = $dest['place'];
        $this->nro_dest         = $dest['number'];
        $this->xClp_dest        = $dest['complement'];
        $this->xBairro_dest     = $dest['district'];
        $this->cMun_dest        = $dest['cod_ibge_city'];
        $this->xMun_dest        = $dest['city'];
        $this->UF_dest          = $dest['uf'];
        $this->CEP_dest         = $dest['cep'];
        $this->cPais_dest       = "1058";
        $this->xPais_dest       = "Brasil";
        $this->fone_dest        = $dest['telephone'] !== null ? $dest['telephone'] : ($dest['cellphone'] !== null ? $dest['cellphone'] : "");
    }

    public function getCSTeCSOSN($product)
    {
        $CST        = null; // Inicializando variável de CST
        $CSOSN      = null; // Inicializando variável de CSOSN
        $productDb  = $this->getDataProduct($product->cod_product); // Consulta produto

        /**
         * Cálculo CST
         */
        if ($productDb->suspensao_icms === 1 && $this->typeConsumer === 0) $CST = 50; // Se tiver suspensão do ICMS e for consumidor final
        elseif ($this->typeConsumer === 0) $CST = 51; // Se for consumidor final
        elseif ($product->st_iten === 0) { // Se não existir MVA
            if ($productDb->subst_trib === 1 && $productDb->isento === 1) $CST = 30; // Se existir ST e for isento
            elseif ($productDb->subst_trib === 1) $CST = 60; // Se existir ST
            elseif ($productDb->isento === 1) $CST = 40; // Se for isento
            elseif ($productDb->subst_trib === 0 && $productDb->isento === 0) { // Se naõ existe ST e não for isento
                // Importada adquirida no exterior CST(40)
                // Não importada e não adquirida no exterior CST(41)
                $CST = 41; // Por padrão CST(40), em breve será feito validação para exterior
            }
        }
        elseif ($product->st_iten !== 0) { // Se existir MVA
            if ($productDb->subst_trib === 1 && $productDb->isento === 1) $CST = 30; // Se existir ST e for isento
            elseif ($productDb->subst_trib === 1) { // Se existir ST
                if ($this->IE_dest === null) $CST = "00"; // Se não existir IE
                elseif ($this->IE_dest !== null) $CST = 10; // Se existir IE
            }
            elseif ($productDb->subst_trib === 0 || $productDb->isento === 0) $CST = "00"; // Se não existir ST ou não for isento
        }
        else $CST = 90; // Outras

        /**
         * Emissor optante pelo simples nacional
         */
        if ($this->CRT_emit === 1) {
            /**
             * Cálculo CSOSN
             */
            if ($CST === 60) $CSOSN = 500; // CST(60), transforma para CSOSN(50)
            elseif ($CST === 40 || $CST === 41 || $CST === 50 || $CST === 51) $CSOSN = 300; // CST(40) ou CST(41) ou CST(50) ou CST(51), transforma para CSOSN(300)
            elseif ($this->tipo_cliente === "pf" || $this->tax_situation === "simples") { // Se cliente for pessoa física ou optante pelo simples
                if ($CST === 10 || $CST === 30 || $CST === 70) { // CST(10) ou CST(30) ou CST(70)
                    $CSOSN = 202; // Transforma para CSOSN(202)
                    if ($this->cpf_consumer_fin === 1) $CSOSN = 201; // Se existir consumidor final no CPF
                }
                elseif ($CST !== 10 && $CST !== 30 && $CST !== 70) { // !CST(10) e !CST(30) e CST(70)
                    $CSOSN = 102; // Transforma para CSOSN(102)
                    if ($this->cpf_consumer_fin === 1) $CSOSN = 101; // Se existir consumidor final no CPF
                }
            }
            elseif ($this->tipo_cliente === "pj" && $this->tax_situation !== "simples") { // Se cliente for pessoa jurídica e não for optante pelo simples
                if ($CST === 10 || $CST === 70) $CSOSN = 201; // CST(10) ou CST(70), transforma para CSOSN(201)
                if ($CST !== 10 && $CST !== 70) $CSOSN = 101; // !CST(10) e !CST(70), transforma para CSOSN(101)
            }
            else $CSOSN = 900; // Outros
        }
        // Se o emissor for optante pelo simples nacional irá retornar CSOSN, caso contrário CST
        return $this->CRT_emit === 1 ? $CSOSN : $CST;
    }

    /**
     * Get CFOP of product
     *
     * @param $codProduct
     * @return mixed
     */
    public function getCFOPProduct($codProduct)
    {
        $productDb  = $this->getDataProduct($codProduct); // Consulta produto

        if ($this->UF_emit === $this->UF_dest){
            if ($productDb->subst_trib === 1) return $this->arrCFOP['cfop_state_st']; // Dentro do estado Com ST
            return $this->arrCFOP['cfop_state']; // Dentro do estado Sem ST
        }

        if ($productDb->subst_trib === 1)  return $this->arrCFOP['cfop_no_state_st']; // Fora do estado Com ST
        return $this->arrCFOP['cfop_no_state']; // Fora do estado Sem ST
    }

    /**
     * Get data product
     *
     * @param $cod
     * @return mixed
     */
    public function getDataProduct($cod)
    {
        return Product::where('id', $cod)->first();
    }
}
