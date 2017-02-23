<?php

/*
 * Helpers
 */

if (!function_exists('dd')) {
    function dd($value)
    {
        die(var_dump($value));
    }
}