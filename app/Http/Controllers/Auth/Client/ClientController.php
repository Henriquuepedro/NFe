<?php

namespace App\Http\Controllers\Auth\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Locale;
use App\Models\State;
use App\Models\Sale\Sale;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\UpdateClientRequest;
use TokenJWT;
use DB;

class ClientController extends Controller
{
    private $client;
    private $locale;
    private $state;
    private $sale;

    public function __construct(Client $client, State $state, Locale $locale, Sale $sale)
    {
        $this->client   = $client;
        $this->locale   = $locale;
        $this->state    = $state;
        $this->sale     = $sale;
    }

    /**
     * Returns a view with available clients.
     *
     * @return view
     */
    public function list()
    {
        // Clients recovered from database
        $clients    = $this->client->orderBy('razao_social')->get();
        // Array empty, datas clients
        $dataClient = [];

        // Loop to format customer data
        foreach ($clients as $client) {            
            $razaoSocial= $client->razao_social;
            $phone      = $client->telefone !== "" ? $this->formatPhone($client->celular, true) : $this->formatPhone($client->telefone, true);
            $document   = $this->formatDoc($client->cnpj_cpf, true);

            $token = TokenJWT::encode([
                'userdata' => [
                    'cod_client' => $client->id,
                    'cod_locale' => $client->cod_locale
                ]
            ]);

            array_push(
                $dataClient, 
                [
                    'id'            => $client->id, 
                    'razaoSocial'   => $razaoSocial, 
                    'document'      => $document, 
                    'phone'         => $phone,
                    'idToken'       => $token
                ]
            );
        }
        // Return view clients list with variable dataClient
        return view('admin.client.list', compact('dataClient'));
    }

    /**
     * Returns a view with available counties.
     *
     * @return view
     */
    public function register()
    {
        // Recupera a listagem de todos os estados
        $states = $this->state->orderBy('nome')->get();

        $dataStates = [];
        foreach($states as $state) $dataStates[$state->uf] = $state->nome;

        // Return view clients register
        return view('admin.client.register', compact('dataStates'));
    }

    /**
     * Returns a view with available counties.
     *
     * @return view
     */
    public function edit($id)
    {
        // Recupera os dados do cliente para alteração
        $client = $this->client ->where('clients.id', $id)
                                ->join('locales', 'clients.cod_locale', '=', 'locales.id')
                                ->leftJoin('cities', 'locales.cod_ibge_city', '=', 'cities.codigo_ibge')
                                ->leftJoin('states', 'cities.codigo_uf', '=', 'states.codigo_uf')
                                ->get();

        if($client->count() === 0) return redirect()->route('admin.client.list');

        $client = $client[0];

        // Recupera a listagem de todos os estados
        $states = $this->state->orderBy('nome')->get();

        $dataStates = [];
        foreach($states as $state) $dataStates[$state->uf] = $state->nome;


        $token = TokenJWT::encode([
            'userdata' => [
                'cod_client' => $client['id'],
                'cod_locale' => $client['cod_locale']
            ]
        ]);

        $client['token_update'] = $token;

        // Return view clients register
        return view('admin.client.register', compact('dataStates', 'client'));
    }

    /**
     * Get request data for database manipulation.
     *
     * @param ClientRequest $request
     * @return void
     */
    public function insert(ClientRequest $request)
    {
        // Validando dados do cliente
        $client = $this->sanitizeDataRequestClient($request);
        // Validando dados do endereço
        $locale = $this->sanitizeDataRequestLocale($request);

        // Inicia transação para inserção a base de dados
        DB::beginTransaction();

        // Insere o endereço na base
        $insertLocale           = $this->locale->create($locale);
        $client['cod_locale']   = $insertLocale->id; // Recupera ID inserido e adicionar ao array de clientes

        // Insere o cliente na base
        $insertCliente = $this->client->create($client);

        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página de listagem, com uma mensagem de sucesso
        if($insertLocale && $insertCliente){
            DB::commit();
            return redirect()->route('admin.client.list')
                             ->with('success', 'Cliente cadastrado com sucesso!');
        }

        // Caso a inserção for mal sucedida, volta a inserção na base e retorna a página de cadastra, com uma mensagem de erro
        DB::rollBack();
        return redirect()->withErrors('Não foi possível realizar o cadastro, reveja seus dados!')
				         ->withInput($request);

    }

    /**
     * Get request data for database manipulation.
     *
     * @param UpdateClientRequest $request
     * @return void
     */
    public function update(UpdateClientRequest $request)
    {
        $dataDecode = TokenJWT::decode($request['token_update']);
        
        $codClient = $dataDecode->cod_client;
        $codLocale = $dataDecode->cod_locale;

        
        // Validando dados do cliente
        $client = $this->sanitizeDataRequestClient($request);
        // Validando dados do endereço
        $locale = $this->sanitizeDataRequestLocale($request);

        // Inicia transação para inserção a base de dados
        DB::beginTransaction();

        // Insere o endereço na base
        $updateLocale   = $this->locale->edit($locale, $codLocale);
        // Insere o cliente na base
        $updateClient  = $this->client->edit($client, $codClient);

        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página de listagem, com uma mensagem de sucesso
        if($updateLocale && $updateClient){
            DB::commit();
            return redirect()->route('admin.client.list')
                             ->with('success', 'Cliente alterado com sucesso!');
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

        // Valida os códigos como inteiros
        $codClient = filter_var($idToken->cod_client, FILTER_VALIDATE_INT);
        $codLocale = filter_var($idToken->cod_locale, FILTER_VALIDATE_INT);

        $checkInUse = $this->sale->where('cod_client', $codClient)->get();

        if($checkInUse->count() > 0)
            return back()->with('danger', 'Não foi possível realizar a ação, o cliente está relacionado à um venda!');
        
        // Inicia a transação de dados
        DB::beginTransaction();

        // Insere o endereço na base
        $deleteClient = $this->client->where('id', $codClient)->delete();
        $deleteLocale = $this->locale->where('id', $codLocale)->delete();

        // Caso a exclusão for bem sucedida, finaliza a exclusão na base e retorna a página de listagem, com uma mensagem de sucesso
        if($deleteClient && $deleteLocale){
            DB::commit();
            return back()->with('success', 'Cliente excluído com sucesso!');
        }

        // Caso a exclusão for mal sucedida, volta a exclusão na base e retorna a página de cadastra, com uma mensagem de erro
        DB::rollBack();
        return back()->with('Não foi possível realizar a exclusão, tente novamente!');
    }

    /**
     * Sanitize all customer request data
     *
     * @param array $request
     * @return array
     */
    public function sanitizeDataRequestClient($request)
    {
        // Valida checkbox do cpf de consumidor final
        if($request["consumerFinalCpf"] === null) $request["consumerFinalCpf"] = 0;
        if($request["consumerFinalCpf"] === "on") $request["consumerFinalCpf"] = 1;

        // Validando dados do cliente
        $client = [
            "tipo_cliente"          => filter_var($request["tipoPessoa"],       FILTER_SANITIZE_STRING),
            "razao_social"          => filter_var($request["nameComplet"],      FILTER_SANITIZE_STRING),
            "fantasia"              => filter_var($request["fantasia"],         FILTER_SANITIZE_STRING),
            "tipo_consumidor"       => filter_var($request["consumerType"],     FILTER_SANITIZE_STRING),
            "situacao_tributaria"   => filter_var($request["taxSituation"],     FILTER_SANITIZE_STRING),
            "cpf_consumidor_final"  => filter_var($request["consumerFinalCpf"], FILTER_VALIDATE_INT),
            "email"                 => filter_var($request["email"],            FILTER_VALIDATE_EMAIL),
            "cnpj_cpf"              => filter_var(preg_replace("/[^0-9]/", '', $request["documentCpfCnpj"]), FILTER_SANITIZE_NUMBER_INT),
            "rg_ie"                 => filter_var(preg_replace("/[^0-9]/", '', $request["documentRgIe"]),    FILTER_SANITIZE_NUMBER_INT),
            "im"                    => filter_var(preg_replace("/[^0-9]/", '', $request["documentIm"]),      FILTER_SANITIZE_NUMBER_INT),
            "telefone"              => filter_var(preg_replace("/[^0-9]/", '', $request["telephone"]),       FILTER_SANITIZE_NUMBER_INT),
            "celular"               => filter_var(preg_replace("/[^0-9]/", '', $request["cellPhone"]),       FILTER_SANITIZE_NUMBER_INT),
        ];
        // Validando campos nulos
        foreach ($client as $key => $value) $client[$key] = $client[$key] === "" || $client[$key] === false ? null : $client[$key];

        return $client;
    }

    /**
     * Sanitize all locale request data
     *
     * @param array $request
     * @return array
     */
    public function sanitizeDataRequestLocale($request)
    {
         // Validando dados do endereço
         $locale = [
            "place"         => filter_var($request["address"],          FILTER_SANITIZE_STRING),
            "number"        => filter_var($request["addressNumber"],    FILTER_SANITIZE_STRING),
            "complement"    => filter_var($request["complement"],       FILTER_SANITIZE_STRING),
            "district"      => filter_var($request["district"],         FILTER_SANITIZE_STRING),
            "cod_ibge_city" => filter_var($request["city"],             FILTER_VALIDATE_INT),
            "cep"           => filter_var(preg_replace("/[^0-9]/", '', $request["cep"]), FILTER_SANITIZE_NUMBER_INT)
        ];

        // Validando campos nulos
        foreach ($locale as $key => $value) $locale[$key] = $locale[$key] === "" ? null : $locale[$key];

        return $locale;
    }
}
