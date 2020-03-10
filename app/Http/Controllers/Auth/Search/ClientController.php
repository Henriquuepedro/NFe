<?php

namespace App\Http\Controllers\Auth\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function searchClient($id)
    {

        $queryClient = $this->client
                            ->select(['clients.tipo_consumidor', 'states.uf'])
                            ->leftjoin('locales', 'clients.cod_locale', '=', 'locales.id')
                            ->leftjoin('cities', 'locales.cod_ibge_city', '=', 'cities.codigo_ibge')
                            ->leftjoin('states', 'cities.codigo_uf', '=', 'states.codigo_uf')
                            ->where('clients.id', $id)
                            ->get();

        echo json_encode($queryClient[0]);
    }
}
