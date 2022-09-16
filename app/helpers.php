<?php

if (! function_exists('format_money')) {
    function format_money(int $money): string
    {
        $money = $money / 100;

        return "€ {$money}";
    }
}

if (!function_exists('extend_url_with_query_data')) {
    function extend_url_with_query_data(string $url, array $queryData): string
    {
        if ($queryData == []) {
            return $url;
        }

        $glue = mb_strpos($url, '?') === false ? '?' : '&';
        $queryString = http_build_query($queryData);

        return  "{$url}{$glue}{$queryString}";
    }
}
