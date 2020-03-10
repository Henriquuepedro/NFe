@extends('adminlte::page')

@section('title', 'Visualizar NFe')

@section('content_header')
    <h1 class="m-0 text-dark">Visualizar NFe</h1>
@stop

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @if($nfeCancel)
                    <div class="alert alert-warning">Nota fiscal eletrônica <strong>CANCELADA</strong></div>
                @endif
                @if (session('success') || session('danger'))
                    <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} display-flex justify-content-between">
                        <p class="no-margin">{{ session('success') ? session('success') : session('danger') }}</p><i class="fa fa-times mt-1" delete-alert="true"></i>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Nota Fiscal Eletrônica</h3>
                    </div>
                    <div class="card-body">
                        <div class="row links-action-img">
                            <div class="col-md-@if($nfeCancel){{3}}@else{{4}}@endif text-center">
                                <label>Baixe o arquivo em PDF</label><br/>
                                <a href="{{ route('download.file.storage.pdf', ['cod' => $links_cript->cod_nfe]) }}" download><img src="{{ asset("admin/img/icon/pdf.png") }}"></a>
                            </div>
                            <div class="col-md-@if($nfeCancel){{3}}@else{{4}}@endif text-center">
                                <label>Baixe o arquivo em XML</label><br/>
                                <a href="{{ route('download.file.storage.xml', ['file_name' => $links_cript->xml]) }}" download><img src="{{ asset("admin/img/icon/xml.png") }}"></a>
                            </div>
                            @if($nfeCancel)
                                <div class="col-md-3 text-center">
                                    <label>Baixe o arquivo em PDF <span class="text-red">NF-e Cancelada</span></label><br/>
                                    <a href="{{ route('download.file.storage.pdf', ['cod' => $links_cript->cod_nfe_cancel]) }}" download><img src="{{ asset("admin/img/icon/pdf.png") }}"></a>
                                </div>
                                <div class="col-md-3 text-center">
                                    <label>Baixe o arquivo em XML <span class="text-red">NF-e Cancelada</span></label><br/>
                                    <a href="{{ route('download.file.storage.xml', ['file_name' => $links_cript->xml_cancel]) }}" download><img src="{{ asset("admin/img/icon/xml.png") }}"></a>
                                </div>
                            @else
                            <div class="col-md-4 text-center">
                                <label>Cancelar NFe</label><br/>
                                <a href="" id="btnCancelNfe" id-nfe="{{ $queryItems[0]->cod_nf }}"><img src="{{ asset("admin/img/icon/x-button.png") }}"></a>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label(null, 'Código Venda') !!}
                                    {!! Form::text(null, $dataPayment[0]->cod_sale, ['class' => "form-control", 'disabled' => 'disabled']) !!}
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    {!! Form::label(null, 'Clientes') !!}
                                    {!! Form::text(null, $dataPayment[0]->razao_social, ['class' => "form-control", 'disabled' => 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label(null, 'Código NFe') !!}
                                    {!! Form::text(null, $queryItems[0]->cod_nf, ['class' => "form-control", 'disabled' => 'disabled']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label(null, 'Data Emissão') !!}
                                    {!! Form::text(null, date('d/m/Y H:i', strtotime($queryItems[0]->date_emission)), ['class' => "form-control", 'disabled' => 'disabled']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label(null, 'Sequencial') !!}
                                    {!! Form::text(null, $queryItems[0]->seq, ['class' => "form-control", 'disabled' => 'disabled']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label(null, 'Natureza') !!}
                                    {!! Form::text(null,  $queryItems[0]->name_nature, ['class' => "form-control", 'disabled' => 'disabled']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label(null, 'Finalidade') !!}
                                    <input type="text" class="form-control" value="@if($queryItems[0]->finality == 1) NF-e Normal @elseif($queryItems[0]->finality == 2) NF-e Complementar @elseif($queryItems[0]->finality == 3) NF-e de Ajuste @endif" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    {!! Form::label(null, 'Mensagem NFe') !!}
                                    {!! Form::text(null, $queryItems[0]->return_sefaz, ['class' => "form-control", 'disabled' => 'disabled']) !!}
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    {!! Form::label(null, 'Chave de Acesso') !!}
                                    <div class="input-group">
                                        <div class="input-group-prepend key-nfe">
                                            <span class="input-group-text"><strong><i class="fa fa-key"></i></strong></span>
                                        </div>
                                        {!! Form::text(null, $queryItems[0]->key_format, ['class' => "form-control", 'disabled' => 'disabled',  "aria-hidden" => "true"]) !!}
                                        {!! Form::text('key-nfe', $queryItems[0]->key) !!}
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
                            <div class="col-md-12 table-responsive">
                                <h6><strong>Produtos</strong></h6>
                                <table class="table-products">
                                    <thead>
                                    <tr>
                                        <th style="width: 39%">Descrição</th>
                                        <th style="width: 5%">Quantidade</th>
                                        <th style="width: 10%" class="text-right">Valor Un.</th>
                                        <th style="width: 9%">ICMS ST</th>
                                        <th style="width: 7%">ICMS</th>
                                        <th style="width: 7%">IPI</th>
                                        <th style="width: 8%" class="text-right">Desconto</th>
                                        <th style="width: 10%" class="text-right">Valor Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($queryItems as $iten)
                                        <tr>
                                            <td class="description" data-toggle-click="tooltip" title='{{ $iten->description }}'>{{ $iten->description }}</td>
                                            <td class="quantity">{{ number_format($iten->qnty_iten, 2, ',', '.') }}</td>
                                            <td class="text-right value_sale">R$ {{ number_format($iten->value_iten, 2, ',', '.') }}</td>
                                            <td class="icmsst">R$ {{ number_format($iten->icms_st_ten, 2, ',', '.') }}</td>
                                            <td class="icms">{{ number_format($iten->icms_perc_iten, 2, ',', '.') }} %</td>
                                            <td class="ipi">R$ {{ number_format($iten->ipi_iten, 2, ',', '.') }}</td>
                                            <td class="text-right discount">R$ {{ number_format($iten->discount_iten, 2, ',', '.') }}</td>
                                            <td class="text-right amount" style="border-right: 0px">R$ {{ number_format($iten->value_total_iten, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <h6 class="col-md-12 text-red"><strong>DESPESAS</strong></h6>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('shipping', 'Frete') !!}
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><strong>R$</strong></span>
                                        </div>
                                        {!! Form::text('shipping', number_format($dataPayment[0]->shipping, 2, ',', '.'),  ['class' => 'form-control', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('insurance', number_format($dataPayment[0]->insurance, 2, ',', '.'),  ['class' => 'form-control', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('others_expenses', number_format($dataPayment[0]->other_expense, 2, ',', '.'),  ['class' => 'form-control', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('daily_charges', number_format($dataPayment[0]->daily_charges, 2, ',', '.'),  ['class' => 'form-control', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('value_icmsst', number_format($dataPayment[0]->icms_st, 2, ',', '.'),  ['class' => 'form-control', 'id' => 'value_icmsst', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('base_icmsst', number_format($dataPayment[0]->base_icms_st, 2, ',', '.'),  ['class' => 'form-control', 'id' => 'base_icmsst', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('value_icms', number_format($dataPayment[0]->icms, 2, ',', '.'),  ['class' => 'form-control', 'id' => 'value_icms', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('base_icms', number_format($dataPayment[0]->base_icms, 2, ',', '.'),  ['class' => 'form-control', 'id' => 'base_icms', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('value_ipi', number_format($dataPayment[0]->ipi, 2, ',', '.'),  ['class' => 'form-control', 'id' => 'value_ipi', 'disabled' => 'disabled']) !!}
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
                                        {!! Form::text('discount_general', number_format($dataPayment[0]->discount, 2, ',', '.'),  ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row installments">
                            @foreach($dataPayment as $installment)
                                <div class="row col-md-12 no-padding">
                                    <div class="offset-md-2 col-md-2 title-installment">
                                        <div class="form-group">
                                            <span>{{$installment->installment_number}}ª Parcela</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="col-md-12 text-center {{$installment->installment_number != 1 ? 'd-none' : ''}}">Dias</label>
                                            <input class="form-control" type="number" value='{{$dataPayment[$installment->installment_number - 1]->due_day}}' disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="col-md-12 text-center {{$installment->installment_number != 1 ? 'd-none' : ''}}">Vencimento</label>
                                            <input class="form-control" type="date" value='{{$dataPayment[$installment->installment_number - 1]->due_date}}' disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="col-md-12 text-center {{$installment->installment_number != 1 ? 'd-none' : ''}}">Valor</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><strong>R$</strong></span>
                                                </div>
                                                <input class="form-control" type="text" value='{{number_format($dataPayment[$installment->installment_number - 1]->value, 2, ',', '.')}}' disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <h6 class="col-md-12"><strong>INFORMAÇÕES</strong></h6>
                            <div class="col-md-12 text-right">
                                <h5 class="gross_value"><strong>Valor Bruto: </strong>R$ <span>{{ number_format($queryItems[0]->gross_value, 2, ',', '.') }}</span></h5>
                                <h5 class="liquid_value"><strong>Valor Líquido: </strong>R$ <span>{{ number_format($queryItems[0]->liquid_value, 2, ',', '.') }}</span></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer col-md-12">
                        {!! link_to_route('admin.sale.list', 'Cancelar', [], ['class' => "btn btn-danger"]); !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('js')
    <script src="{{asset('vendor/jquery-mask-money/jquery.maskMoney.js')}}"></script>
    <script>
        $('.key-nfe').click(function(){
            $('[name="key-nfe"]').select();
            try {
                var ok = document.execCommand('copy');
                if (ok) Toast.fire({ icon: 'success', title: "Chave de acesso copiada!" });
            } catch (e) {
                Toast.fire({ icon: 'success', title: "Ocorreu um problema para copiar a chave de acesso!" });
            }
        });
        $('#btnCancelNfe').click(function(){
            $.getJSON(`${window.location.origin}/admin/venda/nfe/cancelar/${window.location.href.split('/').pop()}`, function( result ) {
                let cStat, xMotivo;

                console.log(result);

                if (result[0].cStat != 128) {
                    //houve alguma falha e o evento não foi processado
                    Toast.fire({
                        icon: 'error',
                        title: "Ocorreu um problema para processar o cancelamento, consulte a disponibilidade geral dos serviços da fazenda!"
                    });
                } else {
                    cStat = result[0].retEvento.infEvento.cStat;
                    if (cStat == '101' || cStat == '135' || cStat == '155') {
                        //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
                        Toast.fire({
                            icon: 'success',
                            title: "NF-e cancelada com sucesso!"
                        });
                        $('.content .row .col-md-12:first').prepend('<div class="alert alert-warning">Nota fiscal eletrônica <strong>CANCELADA</strong></div>');
                        $('#btnCancelNfe').closest('div').remove();
                        $('.row.links-action-img div').toggleClass('col-md-4 col-md-3');
                        $('.row.links-action-img').append(`
                            <div class="text-center col-md-3">
                                <label>Baixe o arquivo em PDF <span class="text-red">NF-e Cancelada</span></label><br>
                                <a href="${window.location.origin}/admin/file/pdf/${result[1][1]}" download><img src="${window.location.origin}/admin/img/icon/pdf.png"></a>
                            </div>
                            <div class="text-center col-md-3">
                                <label>Baixe o arquivo em XML <span class="text-red">NF-e Cancelada</span></label><br>
                                <a href="${window.location.origin}/admin/file/xml/${result[1][0]}" download><img src="${window.location.origin}/admin/img/icon/xml.png"></a>
                            </div>
                        `);
                    } else {
                        xMotivo = result[0].retEvento.infEvento.xMotivo;
                        //houve alguma falha no evento
                        Toast.fire({
                            icon: 'error',
                            title: `Ocorreu um problema para processar o cancelamento, motivo: ${cStat} - ${xMotivo}`
                        });
                    }
                }
            });
            return false;
        });
    </script>
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/css/pages/sale/custom.css') }}">
    <style>
        input[name="key-nfe"]{
            position: absolute;
            left: -999em;
        }
        .key-nfe{
            cursor: pointer;
        }
        .links-action-img img{
            width: 50px;
            height: 50px;
        }
    </style>
@stop
