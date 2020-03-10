<?php

namespace App\Http\Controllers\Auth\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\State;

class CityController extends Controller
{
    private $city;

    public function __construct(City $city, State $state)
    {
        $this->city = $city;
        $this->state = $state;
    }

    public function searchCitys($uf)
    {
        $codeUf = $this->state->getCodeUf($uf);

        echo $this->city->searchCitysForState($codeUf);
    }
}
