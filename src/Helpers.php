<?php

namespace Gets\Freshservice\Helpers;

function t($key = null)
{
    if (is_null($key)) {
        return $key;
    }

    $translations = [];
    $locale = env('LOCALE', 'en');

    $translationsPath = __DIR__ . "/resources/lang/{$locale}.php";

    if (file_exists($translationsPath)) {
        $translations = require $translationsPath;
    }

    return $translations[$key] ?? $key;
}

