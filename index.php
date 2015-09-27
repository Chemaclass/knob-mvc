<?php
/*
 * ==============================================
 * Search the kind of file we are looking for.
 * ==============================================
 * If we're in this file means the original file doesn't exists
 * on the root directory. So come on to looking for on Knob default files.
 */
$query = $GLOBALS['wp_the_query'];
foreach ( $query as $k => $v ) {
	if ((substr($k, 0, 3) == 'is_') && $v) {
		$fileName = substr($k, 3) . '.php';
		break;
	}
}

$fileNameInBase = VENDOR_DIR . '/chemaclass/knob-base/' . $fileName;

if (file_exists($fileNameInBase)) {
	// get the file from the knob-base
	require_once $fileNameInBase;
} else {
	// the file doesnt exists
	die('the file doesnt exists');
}