<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/home', function() { return view('welcome'); })->name('home');

Auth::routes();

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
    /**
     * Rota página inicial
     */
    Route::get('home', 'Auth\Home\HomeController@index')->name('admin.home');

    /**
     * Rotas view listagens
     */
    Route::get('cliente/listagem', 'Auth\Client\ClientController@list')->name('admin.client.list');
    Route::get('produto/listagem', 'Auth\Product\ProductController@list')->name('admin.product.list');
    Route::get('venda/listagem', 'Auth\Sale\SaleController@list')->name('admin.sale.list');
    Route::get('natureza/listagem', 'Auth\Nature\NatureController@list')->name('admin.nature.list');

    /**
     * Rotas view cadastro
     */
    Route::get('cliente/cadastro', 'Auth\Client\ClientController@register')->name('admin.client.register');
    Route::get('produto/cadastro', 'Auth\Product\ProductController@register')->name('admin.product.register');
    Route::get('venda/cadastro', 'Auth\Sale\SaleController@register')->name('admin.sale.register');
    Route::get('natureza/cadastro', 'Auth\Nature\NatureController@register')->name('admin.nature.register');

    /**
     * Rotas view Edição
     */
    Route::get('cliente/editar/{id}', 'Auth\Client\ClientController@edit')->name('admin.client.edit');
    Route::get('produto/editar/{id}', 'Auth\Product\ProductController@edit')->name('admin.product.edit');
    Route::get('venda/editar/{id}', 'Auth\Sale\SaleController@edit')->name('admin.sale.edit');
    Route::get('natureza/editar/{id}', 'Auth\Nature\NatureController@edit')->name('admin.nature.edit');
    Route::get('perfil', 'Auth\ProfileController@edit')->name('admin.profile');

    /**
     * Rotas inserts
     */
    Route::post('cliente/salvar', 'Auth\Client\ClientController@insert')->name('admin.client.insert');
    Route::post('produto/salvar', 'Auth\Product\ProductController@insert')->name('admin.product.insert');
    Route::post('venda/salvar', 'Auth\Sale\SaleController@insert')->name('admin.sale.insert');
    Route::post('natureza/salvar', 'Auth\Nature\NatureController@insert')->name('admin.nature.insert');

    /**
     * Rotas updates
     */
    Route::post('cliente/alterar', 'Auth\Client\ClientController@update')->name('admin.client.update');
    Route::post('produto/alterar', 'Auth\Product\ProductController@update')->name('admin.product.update');
    Route::post('venda/alterar', 'Auth\Sale\SaleController@update')->name('admin.sale.update');
    Route::post('natureza/alterar', 'Auth\Nature\NatureController@update')->name('admin.nature.update');
    Route::post('perfil/alterar', 'Auth\ProfileController@update')->name('admin.profile.update');

    /**
     * Rotas deletes
     */
    Route::post('cliente/delete', 'Auth\Client\ClientController@delete')->name('admin.client.delete');
    Route::post('produto/delete', 'Auth\Product\ProductController@delete')->name('admin.product.delete');
    Route::post('venda/delete', 'Auth\Sale\SaleController@delete')->name('admin.sale.delete');
    Route::post('natureza/delete', 'Auth\Nature\NatureController@delete')->name('admin.nature.delete');

    /**
     * Emissão NFE
     */
    Route::get('venda/nfe/{id}', 'Auth\NFe\NFeController@index')->name('admin.sale.nfe');
    Route::get('venda/nfe/visualizar/{id}', 'Auth\NFe\NFeController@viewNfeEmit')->name('admin.sale.nfe.view');
    Route::post('venda/nfe/emitir', 'Auth\NFe\NFeController@store')->name('admin.sale.nfe.send');
    Route::get('venda/nfe/cancelar/{id}', 'Auth\NFe\NFeController@cancelNFe')->name('admin.sale.nfe.cancel');

    /**
     * Consultas
     */
    Route::group(['prefix' => 'search'], function() {
        // Consulta cidades de um estado
        Route::get('/citys/{uf}', 'Auth\Search\CityController@searchCitys');

        // Consulta dados de uma mercadoria
        Route::get('/product/{id}/{uf}', 'Auth\Search\ProductController@searchProduct');

        // Consulta dados de um client
        Route::get('/client/{id}', 'Auth\Search\ClientController@searchClient');
    });

    /**
     * Baixar arquivos
     */
    Route::get('file/xml/{file_name}', 'Auth\NFe\NFeController@downloadXML')->name('download.file.storage.xml');
    Route::get('file/pdf/{cod}', 'Auth\NFe\NFeController@downloadPDF')->name('download.file.storage.pdf');

});
