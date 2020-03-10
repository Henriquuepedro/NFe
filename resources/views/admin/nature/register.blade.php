@extends('adminlte::page')

@section('title', 'Cadastro de Natureza')

@section('content_header')
    <h1 class="m-0 text-dark">Cadastro de Natureza</h1>
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
                    <h3 class="card-title">Natureza</h3><br />
                    <small>Formulário a ser inserido os dados para um novo cadastro</small>
                </div>
                {!! Form::open([
                                'route' => isset($nature) ? 'admin.nature.update' : 'admin.nature.insert',
                                'method' => 'POST',
                                'enctype' => "multipart/form-data",
                                'id' => "formRegister"
                ]) !!}
                    <div class="card-body col-md-12">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    {!! Form::label('description', 'Descrição do Natureza') !!}
                                    {!! Form::text('description', old() ? old('description') : $nature['description'] ?? '',  ['class' => 'form-control', 'id' => 'description', 'placeholder' => 'Insira a descrição']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('consumerType', 'Tipo de Consumidor') !!}
                                    {!! Form::select('consumerType', [0 => 'Consumidor Final', 1 => 'Consumidor Não Final'], old() ? old('consumerType') : (isset($nature) ? $nature['customer_type'] : null), ['class' => "form-control", 'id' => "consumerType"]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cfop_state', 'CFOP Dentro do Estado') !!}
                                    {!! Form::text('cfop_state', old() ? old('cfop_state') : $nature['cfop_state'] ?? '', ['class' => "form-control", 'id' => "cfop_state", 'placeholder' => "Insira o número de CFOP"] ) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cfop_state_st', 'CFOP Dentro do Estado ST') !!}
                                    {!! Form::text('cfop_state_st', old() ? old('cfop_state_st') : $nature['cfop_state_st'] ?? '', ['class' => "form-control", 'id' => "cfop_state_st", 'placeholder' => "Insira o número de CFOP"] ) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cfop_no_state', 'CFOP Fora do Estado') !!}
                                    {!! Form::text('cfop_no_state', old() ? old('cfop_no_state') : $nature['cfop_no_state'] ?? '', ['class' => "form-control", 'id' => "cfop_no_state", 'placeholder' => "Insira o número de CFOP"] ) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('cfop_no_state_st', 'CFOP Fora do Estado ST') !!}
                                    {!! Form::text('cfop_no_state_st', old() ? old('cfop_no_state_st') : $nature['cfop_no_state_st'] ?? '', ['class' => "form-control", 'id' => "cfop_no_state_st", 'placeholder' => "Insira o número de CFOP"] ) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer col-md-12 d-flex justify-content-between">
                        <div class="col-md-6 text-left">
                            {!! link_to_route('admin.nature.list', 'Cancelar', [], ['class' => "btn btn-danger"]); !!}
                        </div>
                        <div class="col-md-6 text-right">
                            {!! Form::submit(isset($nature) ? 'Alterar Natureza' : 'Cadastrar Natureza', ['class' => "btn btn-success"]); !!}
                        </div>
                    </div>
                    {!! csrf_field() !!}
                    @if(isset($nature)) {!! Form::hidden('token_update', $nature['token_update']) !!} @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@stop

@section('js')
    <script src="{{asset('admin/js/nature/register.js')}}"></script>
    <script src="{{asset('vendor/jquery-mask-money/jquery.maskMoney.js')}}"></script>
    <script src="{{asset('vendor/jquery-mask/jquery.mask.js')}}"></script>
@stop
