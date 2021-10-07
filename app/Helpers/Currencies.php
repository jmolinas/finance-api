<?php

namespace App\Helpers;

class Currencies
{
    /**
     * List Currencies by Country
     *
     * @return array
     */
    public static function byCountry()
    {
        $country = countries(true);
        return array_map(function ($item) {
            $name = $item['name'];
            return [
                'common' => $name['common'],
                'official' => $name['official'],
                'currency' => $item['currency']
            ];
        }, $country);
    }

    /**
     * List Currencies
     *
     * @return array
     */
    public static function get()
    {
        $country = countries(true);
        $currencies = [];
        array_walk($country, function ($item) use (&$currencies) {
            $currencies = array_merge($currencies, $item['currency']);
        });
        ksort($currencies);
        return $currencies;
    }
}
