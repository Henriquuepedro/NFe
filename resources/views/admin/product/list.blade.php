@extends('adminlte::page')

@section('title', 'Listagem de Produtos')

@section('content_header')
    <h1 class="m-0 text-dark">Listagem de Produtos</h1>
@stop

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @if (session('success') || session('danger'))
                <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} display-flex justify-content-between">
                    <p class="no-margin">{{ session('success') ? session('success') : session('danger') }}</p><i class="fa fa-times mt-1" delete-alert="true"></i>
                </div>                
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between flex-wrap">
                    <div class="col-md-6 no-padding">
                        <h3 class="card-title">Produtos</h3><br />
                        <small>Tabela listando todos os produtos cadastrados</small>
                    </div>
                    <div class="col-md-6 text-right no-padding">
                        <a href="{{ route('admin.product.register') }}" class="btn btn-primary col-md-auto col-xs-12">Novo Registro</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body col-md-12">
                    <table class="dataTables table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Referência</th>
                                <th>Quantidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataProducts as $product)
                            <tr>
                                <td>{{ $product->description }}</td>
                                <td>{{ $product->reference }}</td>
                                <td>{{ $product->amount }}</td>
                                <td class="text-center">
                                    <div class="btn-group dropleft">
                                        <i class="fa fa-cog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}"><i class="fa fa-edit"></i> Alterar Cadastro</a> <br/>
                                            <a href="#" class="text-danger" id-token="{{ $product->idToken }}"><i class="fa fa-trash"></i> Excluir Cadastro</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Descrição</th>
                                <th>Referência</th>
                                <th>Quantidade</th>
                                <th>Ações</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="deleteProduct" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open([
                'route' => 'admin.product.delete',
                'method' => 'POST',
                'enctype' => "multipart/form-data",
                'id' => "formDelete"
            ]) !!}
                <div class="modal-header">
                    <h5 class="modal-title">Excluir Produto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <h5 class="font-weight-bold text-warning"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;&nbsp;&nbsp;ATENÇÃO&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-exclamation-triangle"></i></h5>
                        <h5>Tem certeza que deseja excluir esse produto ?</h5>
                        <h6>A exclusão é permanente, não será possível desfazer a ação.</h6>
                    </div>
                    <hr>
                    <h6 class="mt-2 text-center text-blue text-uppercase"><strong class="product"></strong></h6>
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
    <script src="{{asset('admin/js/product/index.js')}}"></script>
@stop