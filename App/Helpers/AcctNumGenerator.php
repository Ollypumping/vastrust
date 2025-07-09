<?php
namespace App\Helpers;

class AcctNumGenerator
{
    public static function generate()
    {
        return str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
    }
}