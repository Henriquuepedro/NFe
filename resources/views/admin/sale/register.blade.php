@extends('adminlte::page')

@section('title', 'Venda de Produto')

@section('content_header')
    <h1 class="m-0 text-dark">Venda de Produto</h1>
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
                    <h3 class="card-title">Venda</h3><br />
                    <small>Formulário a ser inserido os dados para uma nova venda</small>
                </div>
                {!! Form::open([
                                'route'     => 'admin.sale.insert',
                                'method'    => 'POST',
                                'enctype'   => "multipart/form-data",
                                'id'        => "formRegister",
                                'class'     => "saleForm"
                ]) !!}
                    <div id="collapseSale" class="sale">
                        <div class="card">
                            <div class="card-header" id="headInfoSale">
                                <h5 class="mb-0">
                                    <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#infoSale" aria-expanded="true" aria-controls="infoSale">
                                        Informações da Venda
                                    </button>
                                </h5>
                            </div>
                            <div id="infoSale" class="collapse show" aria-labelledby="headInfoSale" data-parent="#collapseSale">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('client', 'Clientes') !!}
                                                {!! Form::select('client', $dataClient, old('client'), ['class' => "form-control select2", 'id' => "client", 'placeholder' => 'SELECIONE']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row footer">
                                        <div class="col-md-12 text-right">
                                            {!! Form::button('Avançar', ['class' => 'btn btn-primary stepNext-0', 'disabled' => 'disabled']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headProducts">
                                <h5 class="mb-0">
                                    <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#products" aria-expanded="false" aria-controls="products" disabled>
                                        Produtos
                                    </button>
                                </h5>
                            </div>
                            <div id="products" class="collapse" aria-labelledby="headProducts" data-parent="#collapseSale">
                                <div class="card-body no-border">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('product', 'Produto') !!}
                                                {!! Form::select('', $dataProducts, null, ['class' => "form-control select2", 'id' => "product", 'placeholder' => 'SELECIONE']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('quantity', 'Quantidade') !!}
                                                <div class="input-group">
                                                    {!! Form::text('', '0,00', ['class' => "form-control", 'id' => "quantity"]) !!}
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><span class="unity_quantity">&nbsp;&nbsp;&nbsp;</span></span>
                                                    </div>
                                                </div>
                                                <small id="qnt_stock">&nbsp;</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('value_sale', 'Valor de Venda') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('', '0,00', ['class' => "form-control", 'id' => "value_sale"]) !!}
                                                </div>
                                                <small id="table_price">&nbsp;</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('discount', 'Desconto') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('', '0,00', ['class' => "form-control", 'id' => "discount"]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('icms', 'ICMS') !!}
                                                <div class="input-group">
                                                    {!! Form::text('', '0,00', ['class' => "form-control", 'id' => "icms"]) !!}
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><strong>%</strong></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('ipi', 'IPI') !!}
                                                <div class="input-group">
                                                    {!! Form::text('', '0,00', ['class' => "form-control", 'id' => "ipi"]) !!}
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><strong>%</strong></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::hidden('', 0, ['id' => 'haveSt']) !!}
                                        {!! Form::hidden('', 0, ['id' => 'valueSt']) !!}
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            {!! Form::button('Limpar Campos', ['class' => 'btn btn-danger clear-input-product col-md-3']) !!}
                                            {!! Form::button('Adicionar Produto', ['class' => 'btn btn-primary col-md-3 add-product-table']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 table-responsive products-list {{ old() ? '' : 'display-none'}}">
                                            <button type="button" class="btn btn-primary btn-xs mb-2 remove-checkeds">Remove Selecionados</button>
                                            <table class="table-products">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width: 5%">
                                                            <div class="custom-control custom-checkbox">
                                                                <input class="custom-control-input" type="checkbox" id="selectAll" value="option1">
                                                                <label for="selectAll" class="custom-control-label"></label>
                                                            </div>
                                                        </th>
                                                        <th style="width: 39%">Descrição</th>
                                                        <th style="width: 5%">Quantidade</th>
                                                        <th style="width: 10%" class="text-right">Valor Un.</th>
                                                        <th style="width: 9%">ICMS ST</th>
                                                        <th style="width: 7%">ICMS</th>
                                                        <th style="width: 7%">IPI</th>
                                                        <th style="width: 8%" class="text-right">Desconto</th>
                                                        <th style="width: 10%" class="text-right">Valor Total</th>
                                                        <th style="width: 5%" class="text-center"><i class="fa fa-cog"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(old())
                                                        @for($countProd = 1; $countProd <= old('qnt_items'); $countProd++)
                                                            <tr>
                                                                <td class="text-center">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input class="custom-control-input" type="checkbox" id="{{ old("cod_product_$countProd") }}">
                                                                        <label for="{{ old("cod_product_$countProd") }}" class="custom-control-label"></label>
                                                                    </div>
                                                                </td>
                                                                <td class="description" data-toggle-click="tooltip" title='{{ old("name_product_$countProd") }}'>{{ old("name_product_$countProd") }}</td>
                                                                <td class="quantity">{{ number_format(old("quantity_$countProd"), 2, ',', '.') }}</td>
                                                                <td class="text-right value_sale">R$ {{ number_format(old("value_sale_$countProd"), 2, ',', '.') }}</td>
                                                                <td class="icmsst">R$ {{ number_format(old("icmsst_$countProd"), 2, ',', '.') }}</td>
                                                                <td class="icms">{{ number_format(old("icms_$countProd"), 2, ',', '.') }} %</td>
                                                                <td class="ipi">R$ {{ number_format(old("ipi_$countProd"), 2, ',', '.') }}</td>
                                                                <td class="text-right discount">R$ {{ number_format(old("discount_$countProd"), 2, ',', '.') }}</td>
                                                                <td class="text-right amount">R$ {{ number_format(old("amount_$countProd"), 2, ',', '.') }}</td>
                                                                <td class="d-flex justify-content-around pt-1 action no-padding mt-1"><i class="text-red fa fa-times"></i><i class="text-orange fa fa-pencil-alt"></i></td>
                                                                
                                                                <td class="d-none haveSt">{{ old("haveSt_$countProd") }}</td>
                                                                <td class="d-none valueSt">{{ old("valueSt_$countProd") }}</td>
                                                                <td class="d-none valueBaseSt">{{ old("valueBaseSt_$countProd") }}</td>
                                                                <td class="d-none valueIcms">{{ old("valueIcms_$countProd") }}</td>
                                                                <td class="d-none valueBaseIcms">{{ old("valueBaseIcms_$countProd") }}</td>
                                                                <td class="d-none valueStReal">{{ old("valueStReal_$countProd") }}</td>
                                                                <td class="d-none valueIpiReal">{{ old("ipi_perc_$countProd") }}</td>
                                                            </tr>
                                                        @endfor
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-12 text-right mt-4 total-product-list {{ old() ? '' : 'display-none'}}">
                                            <p class="gross_value no-margin"><strong>Valor Bruto: </strong>R$ <span>0,00</span></p>
                                            <p class="liquid_value no-margin"><strong>Valor Líquido: </strong>R$ <span>0,00</span></p>
                                        </div>
                                    </div>
                                    <div class="row footer">
                                        <div class="col-md-12 text-right">
                                            {!! Form::button('Avançar', ['class' => 'btn btn-primary stepNext-1 mt-5', 'disabled' => 'disabled']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headFormPayment">
                                <h5 class="mb-0">
                                    <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#formPayment" aria-expanded="false" aria-controls="formPayment" disabled>
                                        Forma de Pagamento
                                    </button>
                                </h5>
                            </div>
                            <div id="formPayment" class="collapse" aria-labelledby="headFormPayment" data-parent="#collapseSale">
                                <div class="card-body">
                                    <div class="row">
                                        <h6 class="col-md-12 text-red"><strong>DESPESAS</strong></h6>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('shipping', 'Frete') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('shipping', old() ? old('shipping') : '0,00',  ['class' => 'form-control', 'id' => 'shipping']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('insurance', 'Seguro') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('insurance', old() ? old('insurance') : '0,00',  ['class' => 'form-control', 'id' => 'insurance']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('others_expenses', 'Outras Despesas') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('others_expenses', old() ? old('others_expenses') : '0,00',  ['class' => 'form-control', 'id' => 'others_expenses']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('daily_charges', 'Encargos Diários') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('daily_charges', old() ? old('daily_charges') : '0,00',  ['class' => 'form-control', 'id' => 'daily_charges']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h6 class="col-md-12 text-blue"><strong>IMPOSTOS</strong></h6>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('value_icmsst', 'Valor ICMS Substituição') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('value_icmsst', old() ? old('value_icmsst') : '0,00',  ['class' => 'form-control', 'id' => 'value_icmsst', 'disabled' => 'disabled']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('base_icmsst', 'Base ICMS Substituição') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('base_icmsst', old() ? old('base_icmsst') : '0,00',  ['class' => 'form-control', 'id' => 'base_icmsst', 'disabled' => 'disabled']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('value_icms', 'Valor ICMS') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('value_icms', old() ? old('value_icms') : '0,00',  ['class' => 'form-control', 'id' => 'value_icms', 'disabled' => 'disabled']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('base_icms', 'Base ICMS') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('base_icms', old() ? old('base_icms') : '0,00',  ['class' => 'form-control', 'id' => 'base_icms', 'disabled' => 'disabled']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2-5">
                                            <div class="form-group">
                                                {!! Form::label('value_ipi', 'Valor IPI') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('value_ipi', old() ? old('value_ipi') : '0,00',  ['class' => 'form-control', 'id' => 'value_ipi', 'disabled' => 'disabled']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h6 class="col-md-12 text-green"><strong>DESCONTO</strong></h6>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('discount_general', 'Desconto Geral') !!}
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('discount_general', old() ? old('discount_general') : '0,00',  ['class' => 'form-control', 'id' => 'discount_general']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h6 class="col-md-12 d-flex justify-content-between"><strong>PARCELAS</strong><i class="fa fa-refresh refreshValues"></i></h6>
                                        <div class="offset-md-2 col-md-4 no-padding">
                                            <div class="form-group">
                                                {!! Form::label('installment', 'Parcela') !!}
                                                {!! Form::select('installment', [1 => '1 Vez', 2 => '2 Vezes', 3 => '3 Vezes', 4 => '4 Vezes', 5 => '5 Vezes', 6 => '6 Vezes', 7 => '7 Vezes', 8 => '8 Vezes', 9 => '9 Vezes', 10 => '10 Vezes', 11 => '11 Vezes', 12 => '12 Vezes'], old() ? old('installment') : null, ['class' => "form-control select2", 'id' => "installment"]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4 no-padding text-center">
                                            <div class="form-group">
                                                <div class="icheck-primary d-inline">                                                    
                                                    {!! Form::checkbox('calculate_automatic', 'true', old() ? old('calculate_automatic') : true ,['id' => "automatic_distribution"]) !!}
                                                    {!! Form::label('automatic_distribution', 'Calcular Valores Automáticos', ['class' => 'mt-4']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="offset-md-2 col-md-8">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row installments">
                                        @if(old())
                                            @for($countInst = 1; $countInst <= old('installment'); $countInst++)
                                                <div class="row installment_{{$countInst}} col-md-12 no-padding">
                                                    <div class="offset-md-2 col-md-2 title-installment">
                                                        <div class="form-group">
                                                            <span>{{$countInst}}ª Parcela</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="days_p_{{$countInst}}" class="col-md-12 text-center {{$countInst != 1 ? 'd-none' : ''}}">Dias</label>
                                                            <input class="form-control" id="days_p_{{$countInst}}" name="days_p_{{$countInst}}" type="number" value='{{old("days_p_$countInst")}}'>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="date_p_{{$countInst}}" class="col-md-12 text-center {{$countInst != 1 ? 'd-none' : ''}}">Vencimento</label>
                                                            <input class="form-control" id="date_p_{{$countInst}}" name="date_p_{{$countInst}}" type="date" value='{{old("date_p_$countInst")}}'>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="value_p_{{$countInst}}" class="col-md-12 text-center {{$countInst != 1 ? 'd-none' : ''}}">Valor</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                                </div>
                                                                <input class="form-control" id="value_p_{{$countInst}}" name="value_p_{{$countInst}}" type="text" value='{{number_format(old("value_p_$countInst"), 2, ',', '.')}}'>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        @else
                                            <div class="row installment_1 col-md-12 no-padding">
                                                <div class="offset-md-2 col-md-2 title-installment">
                                                    <div class="form-group">
                                                        <span>1ª Parcela</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        {!! Form::label('days_p_1', 'Dias', ['class' => "col-md-12 text-center"]) !!}
                                                        {!! Form::number('days_p_1', 0,  ['class' => 'form-control', 'id' => 'days_p_1']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        {!! Form::label('date_p_1', 'Vencimento', ['class' => "col-md-12 text-center"]) !!}
                                                        {!! Form::date('date_p_1', date('Y-m-d'),  ['class' => 'form-control', 'id' => 'date_p_1']) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        {!! Form::label('value_p_1', 'Valor', ['class' => "col-md-12 text-center"]) !!}
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><strong>R$</strong></span>
                                                            </div>
                                                            {!! Form::text('value_p_1', '0,00',  ['class' => 'form-control', 'id' => 'value_p_1', 'disabled' => 'disabled']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h6 class="col-md-12"><strong>INFORMAÇÕES</strong></h6>
                                        <div class="col-md-12 text-right">
                                            <h5 class="gross_value"><strong>Valor Bruto: </strong>R$ <span>0,00</span></h5>
                                            <h5 class="liquid_value"><strong>Valor Líquido: </strong>R$ <span>0,00</span></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer col-md-12 d-flex justify-content-between">
                        <div class="col-md-6 text-left">
                            {!! link_to_route('admin.sale.list', 'Cancelar', [], ['class' => "btn btn-danger"]); !!}
                        </div>
                        <div class="col-md-6 text-right">
                            {!! Form::submit('Gravar Venda', ['class' => "btn btn-success"]); !!}
                        </div>
                    </div>
                    <div class="d-none tablePriceInput">

                    </div>
                    {!! csrf_field() !!}
                    
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@stop

@section('js')
    <script src="{{asset('admin/js/sale/register.js')}}"></script>
    <script src="{{asset('vendor/jquery-mask-money/jquery.maskMoney.js')}}"></script>
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('admin/css/pages/sale/custom.css') }}">
@stop