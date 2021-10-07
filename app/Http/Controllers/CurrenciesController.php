<?php

namespace App\Http\Controllers;

use App\Helpers\Currencies;

class CurrenciesController extends Controller
{
    /**
     * List Currencies
     *
     * @param Currencies $currencies
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Currencies $currencies)
    {
        $currencies = $currencies::get();
        return $this->get(null, $currencies);
    }
}
