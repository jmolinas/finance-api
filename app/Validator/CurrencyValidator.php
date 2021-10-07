<?php

namespace App\Validator;

use App\Helpers\Currencies;

class CurrencyValidator
{
    /**
     * Currency Validator
     *
     * @return bool
     */
    public function currency($attribute, $value, $parameters, $validator)
    {
        return array_key_exists($value, Currencies::get());
    }
}
