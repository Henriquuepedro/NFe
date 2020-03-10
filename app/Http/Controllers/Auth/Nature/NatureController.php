<?php

namespace App\Http\Controllers\Auth\Nature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nature\Nature;
use App\Http\Requests\NatureRequest;
use TokenJWT;
use DB;

class NatureController extends Controller
{
    private $nature;

    public function __construct(Nature $nature)
    {
        $this->nature = $nature;
    }

    /**
     * Returns a view with available natures.
     *
     * @return view
     */
    public function list()
    {
        $natures = $this->nature->get();
        $dataNatures = [];

        foreach($natures as $nature){
            $id             = $nature->id;
            $description    = $nature->description;
            $customer_type  = $nature->customer_type == 0 ? "Consumidor Final" : "Consumidor Não Final";

            $token = TokenJWT::encode([
                'userdata' => [
                    'cod_nature' => $nature->id
                ]
            ]);

            array_push($dataNatures, (object)[
                'id'            => $id,
                'description'   => $description,
                'customer_type' => $customer_type,
                'idToken'       => $token
            ]);
        }

        return view('admin.nature.list', compact('dataNatures'));
    }
    
    /**
     * Returns a view.
     *
     * @return view
     */
    public function register()
    {
        // Return view nature register
        return view('admin.nature.register');
    }
    

    /**
     * Returns a view with available counties.
     *
     * @return view
     */
    public function edit($id)
    {
        // Recupera os dados da natureza para alteração
        $nature = $this->nature->where('id', $id)->get();

        // Verifica se existe a natureza no banco de dados
        if($nature->count() === 0) return redirect()->route('admin.nature.list');

        $nature = $nature[0];

        // Encriptografa o id da natureza, para evitar burlar
        $token = TokenJWT::encode([
            'userdata' => [
                'cod_nature' => $nature->id
            ]
        ]);

        $nature['token_update'] = $token;

        // Return view nature register
        return view('admin.nature.register', compact('nature'));
    }
    
    /**
     * Get request data for database manipulation.
     *
     * @param NatureRequest $request
     * @return void
     */
    public function insert(NatureRequest $request)
    {
        $data = $request->all();

        $nature = [
            "description"       => $data['description'],
            "customer_type"     => $data['consumerType'],
            "cfop_state"        => $data['cfop_state'],
            "cfop_state_st"     => $data['cfop_state_st'],
            "cfop_no_state"     => $data['cfop_no_state'],
            "cfop_no_state_st"  => $data['cfop_no_state_st'],
            "cod_user_reg"      => auth()->user()->id
        ];

        // Inicia transação para inserção a base de dados
        DB::beginTransaction();
        
        // Insere a natureza na base
        $insertNature = $this->nature->create($nature);

        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página de listagem, com uma mensagem de sucesso
        if($insertNature){
            DB::commit();
            return redirect()->route('admin.nature.list')
                             ->with('success', 'Natureza cadastrada com sucesso!');
        }

        // Caso a inserção for mal sucedida, volta a inserção na base e retorna a página de cadastra, com uma mensagem de erro
        DB::rollBack();
        return redirect()->withErrors('Não foi possível realizar o cadastro, reveja seus dados!')
				         ->withInput($request);

    }
    
    /**
     * Get request data for database manipulation.
     *
     * @param NatureRequest $request
     * @return void
     */
    public function update(NatureRequest $request)
    {
        $dataDecode = TokenJWT::decode($request['token_update']);
        $codNature = $dataDecode->cod_nature;

        $data = $request->all();

        $nature = [
            "description"       => $data['description'],
            "customer_type"     => $data['consumerType'],
            "cfop_state"        => $data['cfop_state'],
            "cfop_state_st"     => $data['cfop_state_st'],
            "cfop_no_state"     => $data['cfop_no_state'],
            "cfop_no_state_st"  => $data['cfop_no_state_st'],
            "cod_user_alt"      => auth()->user()->id
        ];
        
        // Inicia transação para inserção a base de dados
        DB::beginTransaction();

        // Altera a natureza na base
        $updateNature = $this->nature->edit($nature, $codNature);

        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página de listagem, com uma mensagem de sucesso
        if($updateNature){
            DB::commit();
            return redirect()->route('admin.nature.list')
                             ->with('success', 'Natureza alterada com sucesso!');
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
        $codNature = filter_var($idToken->cod_nature, FILTER_VALIDATE_INT);

        // $checkInUse = "query";

        // if($checkInUse->count() > 0)
        //     return back()->with('danger', 'Não foi possível realizar a ação, a natureza está relacionado à uma NFE!');
        
        // Inicia a transação de dados
        DB::beginTransaction();

        // Remove a natureza
        $deleteNature = $this->nature->where('id', $codNature)->delete();

        // Caso a exclusão for bem sucedida, finaliza a exclusão na base e retorna a página de listagem, com uma mensagem de sucesso
        if($deleteNature){
            DB::commit();
            return back()->with('success', 'Natureza excluída com sucesso!');
        }

        // Caso a exclusão for mal sucedida, volta a exclusão na base e retorna a página de cadastra, com uma mensagem de erro
        DB::rollBack();
        return back()->with('Não foi possível realizar a exclusão, tente novamente!');
    }
}
