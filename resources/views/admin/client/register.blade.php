@extends('adminlte::page')

@section('title', 'Cadastro de Cliente')

@section('content_header')
    <h1 class="m-0 text-dark">Cadastro de Cliente</h1>
@stop

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">

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
                    <h3 class="card-title">Cliente</h3><br />
                    <small>Formulário a ser inserido os dados para um novo cadastro</small>
                </div>
                {!! Form::open([
                                'route' => isset($client) ? 'admin.client.update' : 'admin.client.insert',
                                'method' => 'POST',
                                'enctype' => "multipart/form-data",
                                'id' => "formRegister"
                ]) !!}
                    <div class="card-body col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group clearfix d-flex no-margin">
                                    <div class="icheck-success col-md-6 text-center">
                                        {!! Form::radio('tipoPessoa', 'pf', old('tipoPessoa') == "pf" ? true : ((isset($client) && $client['tipo_cliente'] == "pf") ? true : false), ['id' => 'pf']) !!}
                                        {!! Form::label('pf', 'Pessoa Física') !!}
                                    </div>
                                    <div class="icheck-success col-md-6 text-center">
                                        {!! Form::radio('tipoPessoa', 'pj', old('tipoPessoa') == "pj" ? true : ((isset($client) && $client['tipo_cliente'] == "pj") ? true : false), ['id' => 'pj']) !!}
                                        {!! Form::label('pj', 'Pessoa Jrídica') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="formHidden display-none">
                        <div class="card-body col-md-12 no-padding border-top-form-gray"></div>
                        <div class="card-body col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('nameComplet', 'Nome Completo') !!}
                                        {!! Form::text('nameComplet', old() ? old('nameComplet') : (isset($client) ? $client['razao_social'] : ''),  ['class' => 'form-control', 'id' => 'nameComplet', 'placeholder' => 'Insira o nome completo']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6 display-none">
                                    <div class="form-group">
                                        {!! Form::label('fantasia', 'Fantasia') !!}
                                        {!! Form::text('fantasia', old() ? old('fantasia') : (isset($client) ? $client['fantasia'] : ''), ['class' => 'form-control', 'id' => "fantasia", 'name' => "fantasia", 'placeholder' => "Insira a fantasia"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('documentCpfCnpj', 'CPF') !!}
                                        {!! Form::text('documentCpfCnpj', old() ? old('documentCpfCnpj') : (isset($client) ? $client['cnpj_cpf']: ''), ['class' => "form-control", 'id' => "documentCpfCnpj", 'placeholder' => "Insira o CPF"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('documentRgIe', 'Registro Geral (RG)') !!}
                                        {!! Form::text('documentRgIe', old() ? old('documentRgIe') : (isset($client) ? $client['rg_ie']: ''), ['class' => "form-control", 'id' => "documentRgIe", 'placeholder' => "Insira o registro geral"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 display-none">
                                    <div class="form-group">
                                        {!! Form::label('documentIm', 'Inscrição Municipla (IM)') !!}
                                        {!! Form::text('documentIm', old() ? old('documentIm') : (isset($client) ? $client['im']: ''), ['class' => "form-control", 'id' => "documentIm", 'placeholder' => "Insira a inscrição municipal"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('email', 'E-mail') !!}
                                        {!! Form::email('email', old() ? old('email') : (isset($client) ? $client['email'] : ''), ['class' => "form-control", 'id' => "email", 'placeholder' => "Insira o e-mail"]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('telephone', 'Telefone (ddd + 8 digitos)') !!}
                                        {!! Form::text('telephone', old() ? old('telephone') : (isset($client) ? $client['telefone'] : ''), ['class' => "form-control", 'id' => "telephone", 'placeholder' => "Insira o número do telefone fixo", 'data-mask' => "(00) 0000-0000"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('cellPhone', 'Celular (ddd + 9 digitos)') !!}
                                        {!! Form::text('cellPhone', old() ? old('cellPhone') : (isset($client) ? $client['celular'] : ''), ['class' => "form-control", 'id' => "cellPhone", 'placeholder' => "Insira o número do celular móvel", 'data-mask' => "(00) 00000-0000"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('consumerType', 'Tipo de Consumidor') !!}
                                        {!! Form::select('consumerType', ['final' => 'Consumidor Final', 'nao_final' => 'Consumidor Não Final'], old() ? old('consumerType') : (isset($client) ? $client['tipo_consumidor'] : null), ['class' => "form-control", 'id' => "consumerType"]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('taxSituation', 'Situação Tributária') !!}
                                        {!! Form::select('taxSituation', ['nenhum' => 'Nenhuma', 'simples' => 'Simples Nacional', 'lucro' => 'Lucro Presumido'], old() ? old('taxSituation') : (isset($client) ? $client['situacao_tributaria'] : null), ['class' => "form-control", 'id' => "taxSituation"]) !!}

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('consumerFinalCpf', 'CPF Consumidor Final') !!} <br/>
                                        {!! Form::checkbox('consumerFinalCpf', 'on', old() && old('consumerFinalCpf') == 'on' ? true : (isset($client) && $client['cpf_consumidor_final'] == 1 ? true : false), ['id' => "consumerFinalCpf", 'data-bootstrap-switch' => '', 'data-inverse' => "true", 'data-off-color' => "danger", 'data-on-color' => "success", 'data-on-text' => "Sim", 'data-off-text' => "Não"]) !!}
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
                                            {!! Form::text('cep', old() ? old('cep') : (isset($client) ? $client['cep'] : ''), ['class' => 'form-control', 'id' => "cep", 'placeholder' => "Insira o CEP do endereço", 'data-mask' => "00.000-000"] ) !!}
                                            <div class="input-group-append" data-toggle="tooltip" data-placement="bottom" title="Procurar CEP">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('address', 'Nome de Endereço') !!}
                                        {!! Form::text('address', old() ? old('address') : (isset($client) ? $client['place'] : ''), ['class' => 'form-control', 'id' => "address", 'placeholder' => "Insira o nome do endereço"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('addressNumber', 'Número de Endereço') !!}
                                        {!! Form::text('addressNumber', old() ? old('addressNumber') : (isset($client) ? $client['number'] : ''), ['class' => 'form-control', 'id' => "addressNumber", 'placeholder' => "Insira o número do endereço"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        {!! Form::label('complement', 'Complemento') !!}
                                        {!! Form::text('complement', old() ? old('complement') : (isset($client) ? $client['complement'] : ''), ['class' => 'form-control', 'id' => "complement", 'placeholder' => "Insira o complemento do endereço"] ) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('district', 'Bairro') !!}
                                        {!! Form::text('district', old() ? old('district') : (isset($client) ? $client['district'] : ''), ['class' => 'form-control', 'id' => "district", 'placeholder' => "Insira o bairro do endereço"] ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('state', 'Estado') !!}
                                        {!! Form::select('state', $dataStates, old() ? old('state') : (isset($client) ? $client['uf'] : null), ['class' => "form-control select2", 'id' => "state", 'placeholder' => 'SELECIONE']) !!}

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('city', 'Cidade') !!}
                                        {!! Form::select('city', [], null, ['class' => "form-control select2", 'id' => "city", 'placeholder' => 'SELECIONE']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer col-md-12 d-flex justify-content-between">
                            <div class="col-md-6 text-left">
                                {!! link_to_route('admin.client.list', 'Cancelar', [], ['class' => "btn btn-danger"]); !!}
                            </div>
                            <div class="col-md-6 text-right">
                                {!! Form::submit(isset($client) ? 'Alterar Cliente' : 'Cadastrar Cliente', ['class' => "btn btn-success"]); !!}
                            </div>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                    {!! Form::hidden('cityOld', old() ? old('city') : (isset($client) ? $client['codigo_ibge'] : '')) !!}
                    @if(isset($client)) {!! Form::hidden('token_update', $client['token_update']) !!} @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@stop

@section('js')
    <script src="{{asset('admin/js/client/register.js')}}"></script>
@stop
