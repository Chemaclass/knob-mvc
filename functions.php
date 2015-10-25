<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once 'vendor/autoload.php';
// require_once 'app/libs/Helpers.php';

use Libs\Actions;
use Libs\Filters;
use Libs\Widgets;

// --------------------------------------------------------------
// Some constants
// --------------------------------------------------------------

// BASE DIRECTORIES
$baseDir = dirname(__FILE__);
define('BASE_DIR', dirname(__FILE__));
define('VENDOR_DIR', $baseDir . '/vendor');
define('APP_DIR', $baseDir . '/app');
define('PAGES_DIR', APP_DIR . '/pages');
define('VENDOR_KNOB_BASE_DIR', VENDOR_DIR . '/chemaclass/knob-base');

// URL ENVEROMENTS
define('URL_PRO', 'knob.chemaclass.com');
define('URL_DEV', 'knob.chemaclass.com');
define('URL_LOC', 'knob.chemaclass.local');

// SOME DIRECTORIES
define('PUBLIC_DIR', get_template_directory_uri() . '/public');
define('COMPONENTS_DIR', get_template_directory_uri() . '/vendor/components');

// BLOG_INFO
function getBlogTitle()
{
    if (is_home()) {
        return get_bloginfo('name');
    } else {
        return wp_title("-", false, "right") . " " . get_bloginfo('name');
    }
}
define(BLOG_TITLE, getBlogTitle());
define(ADMIN_EMAIL, get_bloginfo('admin_email'));

// --------------------------------------------------------------
// Actions
// --------------------------------------------------------------
Actions::setup();

// --------------------------------------------------------------
// Filters
// --------------------------------------------------------------
Filters::setup();

// --------------------------------------------------------------
// WidgetController
// --------------------------------------------------------------
Widgets::setup();

/**
 * Remove the admin bar in prod
 */
show_admin_bar(false);

@include_once 'test.php';
