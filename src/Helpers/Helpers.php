<?php

namespace Gets\Freshservice\Helpers;

function t($key = null)
{
    if (is_null($key)) {
        return $key;
    }

    $translations = [];
    $locale = env('LOCALE', 'en');

    if (file_exists("./resources/lang/{$locale}.php")) {
        $translations = require "./resources/lang/{$locale}.php";
    }

    return $translations[$key] ?? $key;
}

