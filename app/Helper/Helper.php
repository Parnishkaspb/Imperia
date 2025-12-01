<?php

namespace App\Helper;

class Helper
{
    public static function cleanSearchString($s) {
        return preg_replace('/[^a-zA-Z0-9]/', '', $s);
    }
}
