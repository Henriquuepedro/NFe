<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use TokenJWT;

class UniqueDocumentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Validação razão social
        \Validator::extend('exists_razao_social', function ($attribute, $value, $parameters, $validator) {
            return ((DB::table('clients')->where('razao_social', $value)->count()) == 0);
        });

        // Validação CNPJ/CPF
        \Validator::extend('unique_cpf_cnpj', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace("/[^0-9]/", '', $value);
            return ((DB::table('clients')->where('cnpj_cpf', $value)->count()) == 0 && (strlen($value) == 11 || strlen($value) == 14));
        });
        \Validator::extend('cpf_cnpj_update', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace("/[^0-9]/", '', $value);
            return strlen($value) == 11 || strlen($value) == 14;
        });
         
        // Validação telefone
        \Validator::extend('telephone', function ($attribute, $value, $parameters, $validator) {
            $value = str_replace(['(',' ',')','-'],'', $value);
            return ($value == "" || strlen($value) == 10);
        });
         
        // Validação celular
        \Validator::extend('cellPhone', function ($attribute, $value, $parameters, $validator) {
            $value = str_replace(['(',' ',')','-'],'', $value);
            return ($value == "" || strlen($value) == 11);
        });
         
        // Validação celular
        \Validator::extend('cep', function ($attribute, $value, $parameters, $validator) {
            $value = str_replace(['.','-'],'', $value);
            return ($value == "" || strlen($value) == 8);
        });

        // Validação código existente
        \Validator::extend('decodeProduct', function ($attribute, $value, $parameters, $validator) {

            $decode         = TokenJWT::decode($value);
            $cod_product    = $decode->cod_product;

            return DB::table('products')->where('id', $cod_product)->count() != 0;
        });
    }
}
