<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use TokenJWT;
use DB;
use Image;

class ProfileController extends Controller
{
    private $profile;
    private $state;
    private $city;

    public function __construct(Profile $profile, State $state, City $city)
    {
        $this->profile  = $profile;
        $this->state    = $state;
        $this->city     = $city;
    }

    /**
     * Returns a view with available company and states.
     *
     * @return view
     */
    public function edit()
    {
        $dataStates = [];

        // Recupera dados da empresa para a listagem
        $profile    = $this->profile
                        ->join('cities', 'profiles.cod_ibge_city', '=', 'cities.codigo_ibge')
                        ->join('states', 'cities.codigo_uf', '=', 'states.codigo_uf')
                        ->where('cnpj', auth()->user()->cnpj)
                        ->get()[0];
        // Cria token para atualização da empresa
        $profile['token_update'] = TokenJWT::encode([
            'userdata' => [
                'profile_id'    => $profile->id,
                'profile_cnpj'  => $profile->cnpj
            ]
        ]);
        // Recupera a listagem de todos os estados
        $states = $this->state->orderBy('nome')->get();
        foreach($states as $state) $dataStates[$state->uf] = $state->nome;

        // Return view clients list with variable dataClient
        return view('admin.user.index', compact('profile', 'dataStates'));
    }

    /**
     * Get request data for database manipulation.
     *
     * @param ProfileRequest $request
     * @return void
     */
    public function update(ProfileRequest $request)
    {
        $dataDecode = TokenJWT::decode($request['token_update']);

        $profileId         = $dataDecode->profile_id;
        $profileCnpj       = $dataDecode->profile_cnpj;
        $uploadCertificado  = true;
        $uploadLogotipo     = true;

        // Cria array com dados para inserção
        $arrData = [
            "razao_social"      => filter_var($request->razao_social, FILTER_SANITIZE_STRING),
            "fantasia"          => filter_var($request->fantasia,FILTER_SANITIZE_STRING),
            "im"                => filter_var($request->im,FILTER_SANITIZE_NUMBER_INT),
            "ie"                => filter_var($request->ie,FILTER_SANITIZE_NUMBER_INT),
            "iest"              => filter_var($request->iest,FILTER_SANITIZE_NUMBER_INT),
            "cnae"              => filter_var($request->cnae,FILTER_SANITIZE_NUMBER_INT),
            "regime_trib"       => filter_var($request->regime_trib,FILTER_VALIDATE_INT),
            "number_start_nfe"  => filter_var($request->number_start_nfe,FILTER_VALIDATE_INT),
            "cep"               => filter_var(str_replace('-', '', $request->cep),FILTER_SANITIZE_NUMBER_INT),
            "place"             => filter_var($request->address,FILTER_SANITIZE_STRING),
            "number"            => filter_var($request->addressNumber,FILTER_SANITIZE_STRING),
            "complement"        => filter_var($request->complement,FILTER_SANITIZE_STRING),
            "district"          => filter_var($request->district,FILTER_SANITIZE_STRING),
            "cod_ibge_city"     => filter_var($request->city,FILTER_VALIDATE_INT)
        ];

        // Caso seja inserido uma senha, será atualizada, caso contrário mantem a já cadastrada
        if($request->pass_certificado !== null)
            $arrData["pass_certificado"] = filter_var($request->pass_certificado,FILTER_SANITIZE_STRING);

        // Upload logotipo caso seja enviado algum arquivo
        if($request->hasFile('logotipo') && $request->logotipo->isValid()) {
            $uploadLogotipo = $this->uploadFile($request, 0);
            $arrData['logotipo'] = 'logotipo.' . $request->logotipo->getClientOriginalExtension();
        }

        // Upload certificado caso seja enviado algum arquivo
        if($request->hasFile('certificado') && $request->certificado->isValid()) {
            $uploadCertificado = $this->uploadFile($request, 1);
            $arrData['path_certificado'] = 'certificado.' . $request->certificado->getClientOriginalExtension();
        }

        // Verifica se ocorreu algum erro no upload de algum arquivos
        if(!$uploadLogotipo || !$uploadCertificado)
            return redirect()->withErrors('Não foi possível realizar a alteração, logotipo ou certificado inválidos!')
                ->withInput($request);

        // Inicia transação para inserção a base de dados
        DB::beginTransaction();

        // Insere o endereço na base
        $updateProfile   = $this->profile->edit($arrData, $profileId, $profileCnpj);

        // Caso a inserção for bem sucedida, finaliza a inserção na base e retorna a página de listagem, com uma mensagem de sucesso
        if($updateProfile){
            DB::commit();
            return redirect()->route('admin.profile')
                ->with('success', 'Perfil alterado com sucesso!');
        }

        // Caso a inserção for mal sucedida, volta a inserção na base e retorna a página de cadastra, com uma mensagem de erro
        DB::rollBack();
        return redirect()->withErrors('Não foi possível realizar a alteração, reveja seus dados!')
            ->withInput($request);
    }

    /**
     * @param Request $request
     * @param int $type 0 - Logotipo, 1 - Certificado
     * @return boolean
     */
    public function uploadFile(Request $request, int $type)
    {
        $nameRequest = $type === 0 ? 'logotipo' : 'certificado'; // Define qual arquivo será feito o upload
        $pathRequest = $type === 0 ? 'logotipos' : 'certificados'; // Define o local onde será salvo o arquivo
        $cnpjCompany = auth()->user()->cnpj; // Cnpj da empresa

        $fileThumbnailUpload = true;

        $fileRequest = $request->file($nameRequest); // Arquivo para manipulação
        $nameFile    = $nameRequest.'.'.$fileRequest->getClientOriginalExtension(); // Cria nome do arquivo
        $localPath   = storage_path("app/public/file/{$pathRequest}/{$cnpjCompany}"); // Define local onde será salvo o arquivo


        if(!file_exists(storage_path("app/public/file/{$pathRequest}/" . auth()->user()->cnpj)))
            mkdir(storage_path("app/public/file/{$pathRequest}/" . auth()->user()->cnpj), 777);

        //Se for logotipo irá criar um arquivo thumbnail
        if($type === 0) {
            $resizeImage = Image::make($fileRequest->getRealPath()); // Inicializa redimenção
            //Define novos valores de dimensões a imagem
            $fileThumbnailUpload = $resizeImage->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save($localPath . '/thumbnail_' . $nameFile);
        }

        // Upload arquivo original
        $fileUpload = $fileRequest->move($localPath, $nameFile);

        // Retorna um boleano se o arquivo foi salvo com sucesso ou não no diretório informado
        return $fileUpload && $fileThumbnailUpload ? true : false;
    }
}
