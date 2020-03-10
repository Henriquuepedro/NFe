@extends('adminlte::page')

@section('title', 'Perfil da Empresa')

@section('content_header')
    <h1 class="m-0 text-dark">Perfil da Empresa</h1>
@stop

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success display-flex justify-content-between">
                        <p class="no-margin">{{ session('success') }}</p><i class="fa fa-times mt-1" delete-alert="true"></i>
                    </div>
                @endif
                <div class="col-md-12 form-validate-error alert alert-warning"{{$errors->any() ? "style=display:block" : ""}}>
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <li>
                                {!! Form::label('error', $error) !!}
                            </li>
                        @endforeach
                    @endif
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Perfil</h3><br />
                        <small>Informações referente à empresa.</small>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body col-md-12">
                        {!! Form::open([
                                'route' => 'admin.profile.update',
                                'method' => 'POST',
                                'enctype' => "multipart/form-data",
                                'id' => "formRegister"
                        ]) !!}
                        <div class="card-body col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('razao_social', 'Nome Completo') !!}
                                        {!! Form::text('razao_social', old() ? old('razao_social') : (isset($profile) ? $profile['razao_social'] : ''),  ['class' => 'form-control', 'id' => 'razao_social', 'placeholder' => 'Insira o nome completo']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('fantasia', 'Fantasia') !!}
                                        {!! Form::text('fantasia', old() ? old('fantasia') : (isset($profile) ? $profile['fantasia'] : ''), ['class' => 'form-control', 'id' => "fantasia", 'placeholder' => "Insira a fantasia"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('im', 'Inscrição Municipal (IM)') !!}
                                        {!! Form::text('im', old() ? old('im') : (isset($profile) ? $profile['im']: ''), ['class' => "form-control", 'id' => "im", 'placeholder' => "Insira o CNPJ"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('ie', 'Inscrição Estadual (IE)') !!}
                                        {!! Form::text('ie', old() ? old('ie') : (isset($profile) ? $profile['ie']: ''), ['class' => "form-control", 'id' => "ie", 'placeholder' => "Insira o registro geral"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('iest', 'IE do Substituto Tributário') !!}
                                        {!! Form::text('iest', old() ? old('iest') : (isset($profile) ? $profile['iest']: ''), ['class' => "form-control", 'id' => "iest", 'placeholder' => "Insira a inscrição municipal"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('cnae', 'CNAE') !!}
                                        {!! Form::text('cnae', old() ? old('cnae') : (isset($profile) ? $profile['cnae']: ''), ['class' => "form-control", 'id' => "cnae", 'placeholder' => "Insira o CNPJ"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('regime_trib', 'Regime Tributário') !!}
                                        {!! Form::select('regime_trib', ['1' => 'Simples Nacional', '2' => 'Lucro Presumido', '3' => 'Lucro Real'], old() ? old('regime_trib') : (isset($profile) ? $profile['regime_trib'] : null), ['class' => "form-control", 'id' => "regime_trib"]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('number_start_nfe', 'Número Inicial NFe') !!}
                                        {!! Form::text('number_start_nfe', old() ? old('number_start_nfe') : (isset($profile) ? $profile['number_start_nfe'] : ''), ['class' => "form-control", 'id' => "number_start_nfe", 'placeholder' => "Insira o número inicial da NFe"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('', 'Alterar Logotipo') !!}
                                        <div class="custom-file">
                                            {!! Form::file('logotipo', ['class' => 'custom-file-input']) !!}
                                            {!! Form::label('logotipo', 'Extensões válidas - jpg, jpeg, png, bmp', ['class' => "custom-file-label"]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('', 'Alterar Certificado Digital') !!}
                                        <div class="custom-file">
                                            {!! Form::file('certificado', ['class' => 'custom-file-input']) !!}
                                            {!! Form::label('certificado', 'Extensão válida - pfx', ['class' => "custom-file-label"]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('pass_certificado', 'Senha Certificado') !!}
                                        {!! Form::password('pass_certificado', ['class' => "form-control", 'id' => "pass_certificado", 'placeholder' => "Altere a senha do certificado"]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body col-md-12 no-padding border-top-form-gray"></div>
                        <div class="card-body col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('cep', 'CEP') !!}
                                        <div class="input-group cep-input-icon">
                                            {!! Form::text('cep', old() ? old('cep') : (isset($profile) ? $profile['cep'] : ''), ['class' => 'form-control', 'id' => "cep", 'placeholder' => "Insira o CEP do endereço", 'data-mask' => "00.000-000"] ) !!}
                                            <div class="input-group-append" data-toggle="tooltip" data-placement="bottom" title="Procurar CEP">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('address', 'Nome de Endereço') !!}
                                        {!! Form::text('address', old() ? old('address') : (isset($profile) ? $profile['place'] : ''), ['class' => 'form-control', 'id' => "address", 'placeholder' => "Insira o nome do endereço"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('addressNumber', 'Número de Endereço') !!}
                                        {!! Form::text('addressNumber', old() ? old('addressNumber') : (isset($profile) ? $profile['number'] : ''), ['class' => 'form-control', 'id' => "addressNumber", 'placeholder' => "Insira o número do endereço"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        {!! Form::label('complement', 'Complemento') !!}
                                        {!! Form::text('complement', old() ? old('complement') : (isset($profile) ? $profile['complement'] : ''), ['class' => 'form-control', 'id' => "complement", 'placeholder' => "Insira o complemento do endereço"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('district', 'Bairro') !!}
                                        {!! Form::text('district', old() ? old('district') : (isset($profile) ? $profile['district'] : ''), ['class' => 'form-control', 'id' => "district", 'placeholder' => "Insira o bairro do endereço"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('', 'Pais') !!}
                                        {!! Form::text('', 'Brasil', ['class' => "form-control", "disabled" => "disabled"]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('state', 'Estado') !!}
                                        {!! Form::select('state', $dataStates, old() ? old('state') : (isset($profile) ? $profile['uf'] : null), ['class' => "form-control select2", 'id' => "state", 'placeholder' => 'SELECIONE']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('city', 'Cidade') !!}
                                        {!! Form::select('city', [], null, ['class' => "form-control select2", 'id' => "city", 'placeholder' => 'SELECIONE']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer col-md-12 d-flex justify-content-between">
                            <div class="col-md-6 text-left">
                                {!! link_to_route('admin.home', 'Cancelar', [], ['class' => "btn btn-danger"]); !!}
                            </div>
                            <div class="col-md-6 text-right">
                                {!! Form::submit('Alterar Perfil', ['class' => "btn btn-success"]); !!}
                            </div>
                        </div>
                        {!! csrf_field() !!}
                        {!! Form::hidden('cityOld', old() ? old('city') : (isset($profile) ? $profile['cod_ibge_city'] : '')) !!}
                        {!! Form::hidden('token_update', $profile['token_update']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/additional-methods.js" type="text/javascript"></script>
    <script src="{{asset('admin/js/user/index.js')}}" type="text/javascript"></script>
@stop
