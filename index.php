<?php
/*
 * ==============================================
 * Search the kind of file we are looking for.
 * ==============================================
 * If we're in this file means the original file doesn't exists
 * on the root directory. So come on to looking for on Knob default files.
 */
define('PAGES_DIR', APP_DIR . '/pages/');
define('VENDOR_KNOB_BASE_DIR', VENDOR_DIR . '/chemaclass/knob-base/');

$wp_the_query = $GLOBALS['wp_the_query'];
$query = $wp_the_query->query;

if (isset($query) && isset($query['pagename'])) {
	$fileName = PAGES_DIR . $query['pagename'] . '.php';
} else {
	foreach ( $wp_the_query as $k => $v ) {
		if ((substr($k, 0, 3) == 'is_') && $v) {
			$fileName = substr($k, 3) . '.php';
			break;
		}
	}
}

$fileNameInBase = VENDOR_KNOB_BASE_DIR . $fileName;

if (file_exists($fileName)) {
	// get the file from the knob-base
	require_once $fileName;
} else if (file_exists($fileNameInBase)) {
	// get the file from the knob-base
	require_once $fileNameInBase;
} else {
	// the file doesnt exists
	die('the file doesnt exists');
}