@extends('adminlte::page')

@section('title', 'Alterar Venda de Produto')

@section('content_header')
    <h1 class="m-0 text-dark">Alterar Venda de Produto</h1>
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
                    <h3 class="card-title">Venda</h3><br />
                    <small>Formulário a ser alterado os dados da venda</small>
                </div>
                {!! Form::open([
                                'route'     => 'admin.sale.update',
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
                                                {!! Form::select('client', $dataClient, old() ? old('client') : $dataItems['sale']['cod_client'], ['class' => "form-control select2", 'id' => "client", 'placeholder' => 'SELECIONE']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
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
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><strong>R$</strong></span>
                                                    </div>
                                                    {!! Form::text('', '0,00', ['class' => "form-control", 'id' => "ipi"]) !!}
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
                                        <div class="col-md-12 table-responsive products-list">
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
                                                @php
                                                    $count_items = old() ? old('qnt_items') : $dataItems['sale']['count_items'];
                                                @endphp
                                                <tbody>
                                                    @for($countProd = 1; $countProd <= $count_items; $countProd++)
                                                    <tr>
                                                        <td class="text-center">
                                                            <div class="custom-control custom-checkbox">
                                                                <input class="custom-control-input" type="checkbox" id="{{ old() ? old("cod_product_$countProd") : $dataItems['items'][$countProd - 1]['cod_product'] }}">
                                                                <label for="{{ old() ? old("cod_product_$countProd") : $dataItems['items'][$countProd - 1]['cod_product'] }}" class="custom-control-label"></label>
                                                            </div>
                                                        </td>
                                                        <td class="description" data-toggle-click="tooltip" title='{{ old() ? old("name_product_$countProd") : $dataItems['items'][$countProd - 1]['description'] }}'>{{ old() ? old("name_product_$countProd") : $dataItems['items'][$countProd - 1]['description'] }}</td>

                                                        <td class="quantity">{{ old() ? number_format(old("quantity_$countProd"), 2, ',', '.') : $dataItems['items'][$countProd - 1]['qnty_iten']}}</td>

                                                        <td class="text-right value_sale">R$ {{ old() ? number_format(old("value_sale_$countProd"), 2, ',', '.') : $dataItems['items'][$countProd - 1]['value_iten']}}</td>

                                                        <td class="icmsst">R$ {{ old() ? number_format(old("icmsst_$countProd"), 2, ',', '.') : $dataItems['items'][$countProd - 1]['icms_st_iten_grid']}}</td>

                                                        <td class="icms">{{ old() ? number_format(old("icms_$countProd"), 2, ',', '.') : $dataItems['items'][$countProd - 1]['icms_perc_iten']}} %</td>

                                                        <td class="ipi">R$ {{ old() ? number_format(old("ipi_$countProd"), 2, ',', '.') : $dataItems['items'][$countProd - 1]['ipi_iten']}}</td>

                                                        <td class="text-right discount">R$ {{ old() ? number_format(old("discount_$countProd"), 2, ',', '.') : $dataItems['items'][$countProd - 1]['discount_iten']}}</td>

                                                        <td class="text-right amount">R$ {{ old() ? number_format(old("amount_$countProd"), 2, ',', '.') : $dataItems['items'][$countProd - 1]['value_total_iten']}}</td>

                                                        <td class="d-flex justify-content-around pt-1 action no-padding mt-1"><i class="text-red fa fa-times"></i><i class="text-orange fa fa-pencil-alt"></i></td>

                                                        <td class="d-none haveSt">{{ old() ? old("haveSt_$countProd") : $dataItems['items'][$countProd - 1]['have_st_iten'] }}</td>
                                                        <td class="d-none valueSt">{{ old() ? old("valueSt_$countProd") : $dataItems['items'][$countProd - 1]['icms_st_iten'] }}</td>
                                                        <td class="d-none valueBaseSt">{{ old() ? old("valueBaseSt_$countProd") : $dataItems['items'][$countProd - 1]['base_icms_st_iten'] }}</td>
                                                        <td class="d-none valueIcms">{{ old() ? old("valueIcms_$countProd") : $dataItems['items'][$countProd - 1]['icms_iten'] }}</td>
                                                        <td class="d-none valueBaseIcms">{{ old() ? old("valueBaseIcms_$countProd") : $dataItems['items'][$countProd - 1]['base_icms_iten'] }}</td>
                                                        <td class="d-none valueStReal">{{ old() ? old("valueStReal_$countProd") : $dataItems['items'][$countProd - 1]['st_iten'] }}</td>
                                                        <td class="d-none valueIpiReal">{{ old() ? old("ipi_perc_$countProd") : $dataItems['items'][$countProd - 1]['ipi_perc_iten'] }}</td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-12 text-right mt-4 total-product-list">
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
                                                    {!! Form::text('shipping', old() ? old('shipping') : $dataPayment[0]->shipping,  ['class' => 'form-control', 'id' => 'shipping']) !!}
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
                                                    {!! Form::text('insurance', old() ? old('insurance') : $dataPayment[0]->insurance,  ['class' => 'form-control', 'id' => 'insurance']) !!}
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
                                                    {!! Form::text('others_expenses', old() ? old('others_expenses') : $dataPayment[0]->other_expense,  ['class' => 'form-control', 'id' => 'others_expenses']) !!}
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
                                                    {!! Form::text('daily_charges', old() ? old('daily_charges') : $dataPayment[0]->daily_charges,  ['class' => 'form-control', 'id' => 'daily_charges']) !!}
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
                                                    {!! Form::text('value_icmsst', old() ? old('value_icmsst') : $dataPayment[0]->icms_st,  ['class' => 'form-control', 'id' => 'value_icmsst', 'disabled' => 'disabled']) !!}
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
                                                    {!! Form::text('base_icmsst', old() ? old('base_icmsst') : $dataPayment[0]->base_icms_st,  ['class' => 'form-control', 'id' => 'base_icmsst', 'disabled' => 'disabled']) !!}
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
                                                    {!! Form::text('value_icms', old() ? old('value_icms') : $dataPayment[0]->icms,  ['class' => 'form-control', 'id' => 'value_icms', 'disabled' => 'disabled']) !!}
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
                                                    {!! Form::text('base_icms', old() ? old('base_icms') : $dataPayment[0]->base_icms,  ['class' => 'form-control', 'id' => 'base_icms', 'disabled' => 'disabled']) !!}
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
                                                    {!! Form::text('value_ipi', old() ? old('value_ipi') : $dataPayment[0]->ipi,  ['class' => 'form-control', 'id' => 'value_ipi', 'disabled' => 'disabled']) !!}
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
                                                    {!! Form::text('discount_general', old() ? old('discount_general') : $dataPayment[0]->discount,  ['class' => 'form-control', 'id' => 'discount_general']) !!}
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
                                                {!! Form::select('installment', [1 => '1 Vez', 2 => '2 Vezes', 3 => '3 Vezes', 4 => '4 Vezes', 5 => '5 Vezes', 6 => '6 Vezes', 7 => '7 Vezes', 8 => '8 Vezes', 9 => '9 Vezes', 10 => '10 Vezes', 11 => '11 Vezes', 12 => '12 Vezes'], old() ? old('installment') : $dataPayment[0]->quantity_installment, ['class' => "form-control select2", 'id' => "installment"]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4 no-padding text-center">
                                            <div class="form-group">
                                                <div class="icheck-primary d-inline">
                                                    {!! Form::checkbox('calculate_automatic', 'true', old() ? old('calculate_automatic') : ($dataPayment[0]->calculate_automatic == 1 ? true : false) ,['id' => "automatic_distribution"]) !!}
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
                                    @php
                                        $count_installment = old() ? old('installment') : $dataPayment[0]->quantity_installment ;
                                    @endphp
                                    <div class="row installments">
                                        @for($countInst = 1; $countInst <= $count_installment; $countInst++)
                                            <div class="row installment_{{$countInst}} col-md-12 no-padding">
                                                <div class="offset-md-2 col-md-2 title-installment">
                                                    <div class="form-group">
                                                        <span>{{$countInst}}ª Parcela</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                    <label for="days_p_{{$countInst}}" class="col-md-12 text-center {{$countInst != 1 ? 'd-none' : ''}}">Dias</label>
                                                        <input class="form-control" id="days_p_{{$countInst}}" min="-9999999" name="days_p_{{$countInst}}" type="number" value='{{old() ? old("days_p_$countInst") : $dataPayment[$countInst - 1]->due_day}}'>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="date_p_{{$countInst}}" class="col-md-12 text-center {{$countInst != 1 ? 'd-none' : ''}}">Vencimento</label>
                                                        <input class="form-control" id="date_p_{{$countInst}}" name="date_p_{{$countInst}}" type="date" value='{{old() ? old("date_p_$countInst") : $dataPayment[$countInst - 1]->due_date}}'>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="value_p_{{$countInst}}" class="col-md-12 text-center {{$countInst != 1 ? 'd-none' : ''}}">Valor</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><strong>R$</strong></span>
                                                            </div>
                                                            <input class="form-control" id="value_p_{{$countInst}}" name="value_p_{{$countInst}}" type="text" value='{{old() ? number_format(old("value_p_$countInst"), 2, ',', '.') : number_format($dataPayment[$countInst - 1]->value, 2, ',', '.')}}'>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
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
                        {!! link_to_route('admin.sale.list', 'Voltar', [], ['class' => "btn btn-danger col-md-2"]); !!}
                        @if($codNFe !== null)
                            {!! link_to_route('admin.sale.nfe.view', 'Visualizar NF-e', ['id' => $codNFe], ['class' => "btn btn-warning col-md-2"]); !!}
                        @elseif($codNFe === null)
                            {!! link_to_route('admin.sale.nfe', 'Emitir NF-e', ['id' => $dataItems['sale']['cod_sale']], ['class' => "btn btn-warning col-md-2"]); !!}
                        @endif
                        @if($codNFe === null)
                            {!! Form::submit('Alterar Venda', ['class' => "btn btn-success col-md-2"]); !!}
                        @endif
                    </div>
                    @if($codNFe === null)
                    <input type="hidden" name="token_update" value="{{$dataItems['sale']['cod_sale_token']}}">
                    @endif
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
