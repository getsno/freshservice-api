<?php

namespace Gets\Freshservice\Helpers;

/*
 * Translates the given language line using localization files
 */
function t(string $key)
{
    $translations = [];

    $locale = env('FRESHSERVICE_LOCALE', 'en');
    $translationsPath = __DIR__ . "/resources/lang/$locale.php";
    if (file_exists($translationsPath)) {
        $translations = require $translationsPath;
    }

    return $translations[$key] ?? $key;
}
