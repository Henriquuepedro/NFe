@extends('adminlte::page')

@section('title', 'Listagem de Vendas')

@section('content_header')
    <h1 class="m-0 text-dark">Listagem de Vendas</h1>
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
            <div class="card">
                <div class="card-header d-flex justify-content-between flex-wrap">
                    <div class="col-md-6 no-padding">
                        <h3 class="card-title">Vendas</h3><br />
                        <small>Tabela listando todas as vendas cadastradas</small>
                    </div>
                    <div class="col-md-6 text-right no-padding">
                        <a href="{{ route('admin.sale.register') }}" class="btn btn-primary col-md-auto col-xs-12">Novo Registro</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body col-md-12">
                    <table class="dataTables table table-bordered table-striped" id="tableSales">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Valor Líquido</th>
                                <th>Data Realizada</th>
                                <th>Código NF-e</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataSales as $sale)
                            <tr>
                                <td>#{{ $sale->id_table }}</td>
                                <td>{{ $sale->client }}</td>
                                <td data-order="{{ $sale->liquid_value }}">R$ {{ $sale->liquid_value_f }}</td>
                                <td data-order="{{$sale->time_create}}">{{ $sale->date_create }}</td>
                                <td>{{ $sale->cod_nf }}</td>
                                <td class="text-center">
                                    <div class="btn-group dropleft">
                                        <i class="fa fa-cog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('admin.sale.edit', ['id' => $sale->id]) }}"><i class="fa fa-eye"></i> Visualizar Venda</a>
                                            <a href="#" class="text-danger" id-token="{{ $sale->idToken }}"><i class="fa fa-trash"></i> Excluir Venda</a>
                                            <hr>
                                            @if($sale->status === null)
                                                <a href="{{ route('admin.sale.nfe', ['id' => $sale->id]) }}"><i class="fa fa-file-text"></i> Emitir NFE</a>
                                            @elseif($sale->status === 100)
                                                <a href="{{ route('admin.sale.nfe.view', ['id' => $sale->cod_nf]) }}"><i class="fa fa-file-text"></i> Visualizar NFE</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Valor Líquido</th>
                                <th>Data Realizada</th>
                                <th>Código NF-e</th>
                                <th>Ações</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="deleteSale" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open([
                'route' => 'admin.sale.delete',
                'method' => 'POST',
                'enctype' => "multipart/form-data",
                'id' => "formDelete"
            ]) !!}
                <div class="modal-header">
                    <h5 class="modal-title">Excluir Venda</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <h5 class="font-weight-bold text-warning"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;&nbsp;ATENÇÃO&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-exclamation-triangle"></i></h5>
                        <h5>Tem certeza que deseja excluir essa venda ?</h5>
                        <h6>A exclusão é permanente, não será possível desfazer a ação.</h6>
                    </div>
                    <hr>
                    <h6 class="mt-2 text-blue text-uppercase pl-5">
                        Código: <strong class="cod_sale"></strong>
                    </h6>
                    <h6 class="mt-2 text-blue text-uppercase pl-5">
                        Cliente: <strong class="name_client"></strong>
                    </h6>
                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <button type="button" class="btn btn-primary col-md-12" data-dismiss="modal">Cancelar</button>
                    </div>
                    <div class="col-md-6 text-right">
                        {!! Form::submit('Excluir Permanentemente', ['class' => "btn btn-danger col-md-12"]) !!}
                    </div>
                </div>
                {!! Form::hidden('idToken') !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@section('js')
    <script src="{{asset('admin/js/sale/index.js')}}"></script>
@stop
