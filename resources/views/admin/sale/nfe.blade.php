@extends('adminlte::page')

@section('title', 'Emissão NFE')

@section('content_header')
    <h1 class="m-0 text-dark">Emissão NFE</h1>
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
            <div class="col-md-12 form-validate-error alert alert-warning"{{$errors->any() || count($validateSaleNfe) > 0 ? "style=display:block" : ""}}>
                @if ($errors->any() || count($validateSaleNfe) > 0)
                    @foreach ($errors->all() ? $errors->all() : $validateSaleNfe as $error)
                        <li>
                            {!! Form::label('error', $error) !!}
                        </li>
                    @endforeach
                @endif
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nota Fiscal Eletrônica</h3><br />
                    <small>Formulário a ser preenchido os dados para emissão de nota fiscal eletrônica</small>
                </div>
                {!! Form::open([
                                'route'     => 'admin.sale.nfe.send',
                                'method'    => 'POST',
                                'enctype'   => "multipart/form-data",
                                'id'        => "formRegister",
                                'class'     => "saleForm"
                ]) !!}
                    <div id="collapseSale" class="sale">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('', 'Cliente') !!}
                                            {!! Form::text('', $dataPayment->razao_social, ['class' => "form-control", 'disabled' => 'disabled']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2-5">
                                        <div class="form-group">
                                            {!! Form::label('', 'Valor ICMS Substituição') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->icms_st, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2-5">
                                        <div class="form-group">
                                            {!! Form::label('', 'Base ICMS Substituição') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->base_icms_st, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2-5">
                                        <div class="form-group">
                                            {!! Form::label('', 'Valor ICMS') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->icms, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2-5">
                                        <div class="form-group">
                                            {!! Form::label('', 'Base ICMS') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->base_icms, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2-5">
                                        <div class="form-group">
                                            {!! Form::label('', 'Valor IPI') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->ipi, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('', 'Frete') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->shipping, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('', 'Seguro') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->insurance, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('', 'Outras Despesas') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->other_expense, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('', 'Encargos Diários') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->daily_charges, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('', 'Valor Total Produtos') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->gross_value, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('', 'Desconto') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->discount, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('', 'Valor Total Nota') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::text('', $dataPayment->liquid_value, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('', 'Parcelas') !!}
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                {!! Form::number('', $dataPayment->quantity_installment, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
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
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {!! Form::label('seq', 'Sequencial') !!}
                                            {!! Form::number('seq', 1, ['class' => 'form-control', 'id' => 'seq']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            {!! Form::label('nature', 'Natureza') !!}
                                            {!! Form::select('nature', $dataNatures, null, ['class' => 'form-control', 'id' => 'nature']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            {!! Form::label('finality', 'Finalidade') !!}
                                            {!! Form::select('finality', [ 1 => 'NF-e Normal', 2 => 'NF-e Complementar', 3 => 'NF-e de
Ajuste' ], null, ['class' => 'form-control', 'id' => 'goal']) !!}
                                        </div>
                                    </div>
{{--                                    <div class="col-md-2">--}}
{{--                                        <div class="form-group">--}}
{{--                                            {!! Form::label('btn_csosn_cfop', 'CSOSN / CFOP') !!}--}}
{{--                                            {!! Form::button('Visualizar', ['class' => 'btn btn-primary col-md-12', 'id' => 'btn_csosn_cfop', "data-toggle" => "modal", "data-target" => "#csosn_cfop"]) !!}--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('qnty', 'Quantidade') !!}
                                            {!! Form::number('qnty', '0', ['class' => 'form-control', 'id' => 'qnty']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('specie', 'Espécie') !!}
                                            {!! Form::text('specie', null, ['class' => 'form-control', 'id' => 'specie']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('gross_weight', 'Peso Bruto') !!}
                                            {!! Form::text('gross_weight', '0,00', ['class' => 'form-control', 'id' => 'gross_weight']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('liquid_weight', 'Peso Líquido') !!}
                                            {!! Form::text('liquid_weight', '0,00', ['class' => 'form-control', 'id' => 'liquid_weight']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! Form::label('', 'Frete') !!}
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-around">
                                        <div class="icheck-primary d-inline">
                                            {!! Form::radio('shipping', '0', true ,['id' => "ship_issuer"]) !!}
                                            {!! Form::label('ship_issuer', 'Emitente') !!}
                                        </div>
                                        <div class="icheck-primary d-inline">
                                            {!! Form::radio('shipping', '1', false ,['id' => "ship_recipient"]) !!}
                                            {!! Form::label('ship_recipient', 'Destinatário') !!}
                                        </div>
                                        <div class="icheck-primary d-inline">
                                            {!! Form::radio('shipping', '2', false ,['id' => "ship_third_party"]) !!}
                                            {!! Form::label('ship_third_party', 'Terceiros') !!}
                                        </div>
                                        <div class="icheck-primary d-inline">
                                            {!! Form::radio('shipping', '9', false ,['id' => "ship_nothing"]) !!}
                                            {!! Form::label('ship_nothing', 'Sem Frete') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! Form::label('message_complement', 'Mensagem Adicional') !!}
                                        {!! Form::text('message_complement', null, ['class' => 'form-control', 'id' => 'message_complement']) !!}
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
                            {!! Form::submit('Enviar NFE',
                                count($validateSaleNfe) > 0
                                    ? ['class' => "btn btn-success", 'disabled' => 'disabled']
                                    : ['class' => "btn btn-success"]
                            ); !!}
                        </div>
                    </div>
                    <input type="hidden" name="token_sale" value="{{$token_sale}}">
                    {!! csrf_field() !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
{{--<div class="modal fade" id="csosn_cfop" tabindex="-1" role="dialog" aria-hidden="true">--}}
{{--    <div class="modal-dialog modal-lg" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title">CSOSN / CFOP</h5>--}}
{{--                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">--}}
{{--                    <span aria-hidden="true">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body bg-gray-light">--}}
{{--                <table class="table table-sm table-light">--}}
{{--                    <thead class="thead-dark">--}}
{{--                        <th width="70%">Produto</th>--}}
{{--                        <th width="10%">ST</th>--}}
{{--                        <th width="10%">CSOSN</th>--}}
{{--                        <th width="10%">CFOP</th>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                        @foreach($dataItems as $iten)--}}
{{--                        <tr>--}}
{{--                            <td>{{$iten['description']}}</td>--}}
{{--                            <td>{{$iten['have_st_iten']}}</td>--}}
{{--                            <td>0</td>--}}
{{--                            <td>0</td>--}}
{{--                        </tr>--}}
{{--                        @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn btn-primary text-right" data-dismiss="modal">Fechar</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
@stop

@section('js')
    <script src="{{asset('vendor/jquery-mask-money/jquery.maskMoney.js')}}"></script>
    <script>
    $(document).ready(function(){
        $('input[type="text"]').each(function(){
            if($(this).parent().hasClass('input-group') && $(this).attr('name') === "" && $(this).is(':disabled'))
                $(this).val(parseFloat($(this).val()).toFixed(2).replace('.', ','));
        });

        $('#gross_weight, #liquid_weight').maskMoney({thousands:'.', decimal:',', allowZero:true});
    });
    </script>
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('admin/css/pages/sale/custom.css') }}">
@stop
