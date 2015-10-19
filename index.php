<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ==============================================
 * Search the kind of file we are looking for.
 * ==============================================
 * If we're in this file means the original file doesn't exists
 * on the root directory. So come on to looking for on Knob default files.
 */
$wp_the_query = $GLOBALS['wp_the_query'];
$query = $wp_the_query->query;

if (isset($query) && isset($query['pagename'])) {
    $kindFile = 'page';
    $fileName = PAGES_DIR . '/' . $query['pagename'] . '.php';
} else {
    foreach ($wp_the_query as $k => $v) {
        if ((substr($k, 0, 3) == 'is_') && $v) {
            $kindFile = substr($k, 3);
            $fileName = $kindFile . '.php';
            break;
        }
    }
}

if (file_exists($fileName)) {
    // get the file from the "knob-mvc/app/pages/$fileName"
    require_once $fileName;
} else if (file_exists($fileNameInBase = VENDOR_KNOB_BASE_DIR . '/wp/' . $fileName)) {
    // get the file from the "knob-base/wp/$fileNameInBase"
    require_once $fileNameInBase;
} else if ('page' == $kindFile) {
    // get the file from the "knob-base/page.php"
    require_once VENDOR_KNOB_BASE_DIR . '/page.php';
} else {
    // the file doesn't exists
    die('the file doesn\'t exists');
}