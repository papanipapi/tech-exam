<?php

namespace App;

class Format
{
    public static function roundUp($value, $places=2)
    {
        if ($places < 0) { $places = 0; }
        $mult = pow(10, $places);
        return ceil($value * $mult) / $mult;
    }

    public static function currencyFormat($number, $decimal = 2, $isRoundUp = true)
    {
        if ($isRoundUp) {
            $number = self::roundUp($number);
        }
        return number_format($number, $decimal);
    }
}