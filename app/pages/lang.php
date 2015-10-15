<?php
use Knob\I18n\I18n;

/**
 * NOTE:
 * For to use this page remember that you have to create your own
 * page through the backend from Wordpress.
 *
 * @example yoursite.com/lang?lang=en
 */

$lang = $_GET['lang'];
$redirect = $_GET['redirect'];

/*
 * Check if the lang is available
 */
if (in_array($lang, I18n::getAllLangAvailable())) {
    session_start();
    $_SESSION[I18n::CURRENT_LANG] = $lang;
}

/*
 * We only redirect the url if not is absolute
 */
if (! $redirect || strpos($redirect, 'http') !== false || strpos($redirect, 'https') !== false) {
    header("Location: /");
} else {
    header("Location: $redirect");
}
