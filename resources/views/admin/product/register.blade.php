@extends('adminlte::page')

@section('title', 'Cadastro de Produto')

@section('content_header')
    <h1 class="m-0 text-dark">Cadastro de Produto</h1>
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
                    <h3 class="card-title">Produto</h3><br />
                    <small>Formulário a ser inserido os dados para um novo cadastro</small>
                </div>
                {!! Form::open([
                                'route' => isset($product) ? 'admin.product.update' : 'admin.product.insert',
                                'method' => 'POST',
                                'enctype' => "multipart/form-data",
                                'id' => "formRegister"
                ]) !!}
                    <div class="card-body col-md-12">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    {!! Form::label('description', 'Descrição do Produto') !!}
                                    {!! Form::text('description', old() ? old('description') : $product[0]['descProduct'] ?? '',  ['class' => 'form-control', 'id' => 'description', 'placeholder' => 'Insira a descrição', 'maxlength' => 120]) !!}
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    {!! Form::label('reference', 'Referência') !!}
                                    {!! Form::text('reference', old() ? old('reference') : $product[0]['reference'] ?? '', ['class' => 'form-control', 'id' => "reference", 'name' => "reference", 'placeholder' => "Insira a referência"] ) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('bar_code', 'Código de barras') !!}
                                    {!! Form::text('bar_code', old() ? old('bar_code') : $product[0]['bar_code'] ?? '', ['class' => "form-control", 'id' => "bar_code", 'placeholder' => "Insira o código de barras"]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('unity', 'Unidade') !!}
                                    {!! Form::select('unity', ['UN' => 'Unidade', 'G' => 'Grama', 'JOGO' => 'Jogo', 'LT' => 'Litro', 'MWHORA' => 'MegaWatt Hora', 'METRO' => 'Metro', 'M3' => 'Metro Cúbico', 'M2' => 'Metro Quadrado', '1000UN' => 'Mil Unidade', 'PARES' => 'Pares', 'QUILAT' => 'Quilate', 'KG' => 'Quilograma'], old() ? old('unity') : $product['unity'] ?? null, ['class' => "form-control select2", 'id' => "unity"]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('amount', 'Quantidade') !!}
                                    {!! Form::text('amount', old() ? old('amount') : $product[0]['amount'] ?? '0,00', ['class' => "form-control", 'id' => "amount", 'placeholder' => "Insira a quantidade disponível no estoque"] ) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('active', 'Produto Ativo ?') !!} <br/>
                                    {!! Form::checkbox('active', 'true', !old() ? isset($product) ? $product[0]['active'] == 1 ? true : false : true : old('active') ?? true , ['id' => "active", 'data-bootstrap-switch' => '', 'data-inverse' => "true", 'data-off-color' => "danger", 'data-on-color' => "success", 'data-on-text' => "Ativo", 'data-off-text' => "Não Ativo"]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group text-right">
                                    <br/>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#taxProduct">
                                        Impostos do Produto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body col-md-12 no-padding border-top-form-gray"></div>
                    <div class="card-body col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('price', 'Valor de Venda') !!}
                                    {!! Form::text('', '0,00', ['class' => 'form-control', 'id' => "price", 'placeholder' => "Insira o valor de venda"] ) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('tablesPrice', 'Tabelas de Preço') !!}
                                    {!! Form::select('', $dataTablesPrice, null, ['class' => "form-control select2", 'id' => "tablesPrice"]) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label><br/>
                                    <button type="button" class="btn btn-primary col-md-12 addTablePrice">Adicionar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table tablePriceList mt-4 display-none">
                                <thead>
                                    <th>Tabela de Preço</th>
                                    <th>Valor de Venda</th>
                                    <th>Ações</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer col-md-12 d-flex justify-content-between">
                        <div class="col-md-6 text-left">
                            {!! link_to_route('admin.product.list', 'Cancelar', [], ['class' => "btn btn-danger"]); !!}
                        </div>
                        <div class="col-md-6 text-right">
                            {!! Form::submit(isset($product) ? 'Alterar Produto' : 'Cadastrar Produto', ['class' => "btn btn-success"]); !!}
                        </div>
                    </div>
                    <div class="d-none tablePriceInput">
                        @if(old('tablesPrice') || isset($product))
                            @foreach(old() ? old('tablesPrice') : $product as $key => $input)
                                <input type="hidden" name="tablesPrice[]" value="{{ old() ? $input : $input['cod_table_price'] }}" id-table-price="{{ old() ? $input : $input['cod_table_price'] }}">
                                <input type="hidden" name="valuesPrice[]" value="{{ old() ? old('valuesPrice')[$key] : $input['price'] }}" id-table-price="{{ old() ? $input : $input['cod_table_price'] }}">
                            @endforeach
                        @endif

                    </div>
                    {!! csrf_field() !!}
                    @if(isset($product)) {!! Form::hidden('token_update', $product[0]['token_update']) !!} @endif


                    <div class="modal fade" id="taxProduct" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Impostos do Produto</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('icms_1', 'ICMS de MG / RJ / PR / SP / RS') !!}
                                                    <div class="input-group">
                                                        {!! Form::text('icms_1', old() ? old('icms_1') : $product[0]['icms_1'] ?? '0,00',  ['class' => 'form-control', 'id' => 'icms_1', 'placeholder' => 'MG / RJ / PR / SP / RS', 'maxlength' => "6"]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('icms_2', 'ICMS dentro do estado') !!}
                                                    <div class="input-group">
                                                        {!! Form::text('icms_2', old() ? old('icms_2') : $product[0]['icms_2'] ?? '0,00',  ['class' => 'form-control', 'id' => 'icms_2', 'placeholder' => 'Dentro do estado', 'maxlength' => "6"]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('icms_3', 'ICMS de outros estados') !!}
                                                    <div class="input-group">
                                                        {!! Form::text('icms_3', old() ? old('icms_3') : $product[0]['icms_3'] ?? '0,00',  ['class' => 'form-control', 'id' => 'icms_3', 'placeholder' => 'Outros estados', 'maxlength' => "6"]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('ipi_saida', 'IPI de Saída') !!}
                                                    <div class="input-group">
                                                        {!! Form::text('ipi_saida', old() ? old('ipi_saida') : $product[0]['ipi_saida'] ?? '0,00',  ['class' => 'form-control', 'id' => 'ipi_saida', 'placeholder' => 'IPI de Saída', 'maxlength' => "6"]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('fcp', 'FCP') !!}
                                                    <div class="input-group">
                                                        {!! Form::text('fcp', old() ? old('fcp') : $product[0]['fcp'] ?? '0,00',  ['class' => 'form-control', 'id' => 'fcp', 'placeholder' => 'FCP', 'maxlength' => "6"]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('ncm', 'Classificação Fiscal(NCM)') !!}
                                                    {!! Form::text('ncm', old() ? old('ncm') : $product[0]['ncm'] ?? '',  ['class' => 'form-control', 'id' => 'ncm', 'placeholder' => 'Classificação Fiscal', 'maxlength' => '10', 'data-mask' => "00.00.0000"]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('cest', 'CEST') !!}
                                                    {!! Form::text('cest', old() ? old('cest') : $product[0]['cest'] ?? '',  ['class' => 'form-control', 'id' => 'cest', 'placeholder' => 'CEST', 'maxlength' => '8', 'data-mask' => "00.000.00"]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('lucro_pres', 'Lucro Presumido') !!}
                                                    <div class="input-group">
                                                        {!! Form::text('lucro_pres', old() ? old('lucro_pres') : $product[0]['lucro_pres'] ?? '0,00',  ['class' => 'form-control', 'id' => 'lucro_pres', 'placeholder' => 'Lucro Presumido', 'maxlength' => "6"]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('inci_imposto', 'Incidência dos Impostos') !!}
                                                    <div class="input-group">
                                                        {!! Form::text('inci_imposto', old() ? old('inci_imposto') : $product[0]['inci_imposto'] ?? '0,00',  ['class' => 'form-control', 'id' => 'inci_imposto', 'placeholder' => 'Incidência dos Impostos', 'maxlength' => "6"]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('imposto_impor', 'Imposto de Importação') !!}
                                                    <div class="input-group">
                                                        {!! Form::text('imposto_impor', old() ? old('imposto_impor') : $product[0]['imposto_impor'] ?? '0,00',  ['class' => 'form-control', 'id' => 'imposto_impor', 'placeholder' => 'Lucro Presumido', 'maxlength' => "6"]) !!}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('procedencia', 'Procedência') !!}
                                                    {!! Form::text('procedencia', 'Nacional',  ['class' => 'form-control', 'id' => 'procedencia', 'placeholder' => 'Procedência', 'disabled' => "disabled"]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('subst_trib', 'Substituição Tributária') !!} <br/>
                                                    {!! Form::checkbox('subst_trib', 'true', !old() ? isset($product) ? $product[0]['subst_trib'] == 1 ? true : false : false: old('subst_trib') ?? true ,['id' => "subst_trib", 'data-bootstrap-switch' => '', 'data-inverse' => "true", 'data-off-color' => "danger", 'data-on-color' => "success", 'data-on-text' => "Sim", 'data-off-text' => "Não"]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('incid_pis_cofins', 'Incidência de PIS/COFINS') !!} <br/>
                                                    {!! Form::checkbox('incid_pis_cofins', 'true', !old() ? isset($product) ? $product[0]['incid_pis_cofins'] == 1 ? true : false : false: old('incid_pis_cofins') ?? true , ['id' => "incid_pis_cofins", 'data-bootstrap-switch' => '', 'data-inverse' => "true", 'data-off-color' => "danger", 'data-on-color' => "success", 'data-on-text' => "Sim", 'data-off-text' => "Não"]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('indic_produc', 'Indicador de Produção') !!} <br/>
                                                    {!! Form::checkbox('indic_produc', 'true', !old() ? isset($product) ? $product[0]['indic_produc'] == 1 ? true : false : false: old('indic_produc') ?? true , ['id' => "indic_produc", 'data-bootstrap-switch' => '', 'data-inverse' => "true", 'data-off-color' => "danger", 'data-on-color' => "success", 'data-on-text' => "Sim", 'data-off-text' => "Não"]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('isento', 'Isento de Impostos') !!} <br/>
                                                    {!! Form::checkbox('isento', 'true', !old() ? isset($product) ? $product[0]['isento'] == 1 ? true : false : false: old('isento') ?? true , ['id' => "isento", 'data-bootstrap-switch' => '', 'data-inverse' => "true", 'data-off-color' => "danger", 'data-on-color' => "success", 'data-on-text' => "Sim", 'data-off-text' => "Não"]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('imune', 'Imune de Impostos') !!} <br/>
                                                    {!! Form::checkbox('imune', 'true', !old() ? isset($product) ? $product[0]['imune'] == 1 ? true : false : false: old('imune') ?? true , ['id' => "imune", 'data-bootstrap-switch' => '', 'data-inverse' => "true", 'data-off-color' => "danger", 'data-on-color' => "success", 'data-on-text' => "Sim", 'data-off-text' => "Não"]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('suspensao_icms', 'Suspensão de ICMS') !!} <br/>
                                                    {!! Form::checkbox('suspensao_icms', 'true', !old() ? isset($product) ? $product[0]['suspensao_icms'] == 1 ? true : false : false: old('suspensao_icms') ?? true , ['id' => "suspensao_icms", 'data-bootstrap-switch' => '', 'data-inverse' => "true", 'data-off-color' => "danger", 'data-on-color' => "success", 'data-on-text' => "Sim", 'data-off-text' => "Não"]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary col-md-2" data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>


                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@stop

@section('js')
    <script src="{{asset('admin/js/product/register.js')}}"></script>
    <script src="{{asset('vendor/jquery-mask-money/jquery.maskMoney.js')}}"></script>
@stop
