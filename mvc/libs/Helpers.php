<?php

/**
 *
 * @param string|array $expression
 * @param string $tag
 */
function dd($expression, $tag = "Tag") {
	echo '' . $tag . '<br>';
	var_dump($expression);
	exit();
}

/**
 * Cadena para debug
 *
 * @param string $str
 */
function debug($str) {
	error_log(" DEBUG - " . $str);
}

/**
 * Cadena para info 'debug'
 *
 * @param string $str
 */
function info($str) {
	error_log(" INFO - " . $str);
}