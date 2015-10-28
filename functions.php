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

use Libs\Actions;
use Libs\Filters;
use Libs\Widgets;

// --------------------------------------------------------------
// Some constants
// --------------------------------------------------------------

// BASE DIRECTORIES
define('PROJECT_DIR', dirname(__FILE__));
define('VENDOR_DIR', PROJECT_DIR . '/vendor');
define('VENDOR_KNOB_BASE_DIR', VENDOR_DIR . '/chemaclass/knob-base');
define('VENDOR_KNOB_BASE_WP_DIR', VENDOR_KNOB_BASE_DIR . '/wp');
define('APP_DIR', PROJECT_DIR . '/app');
define('PAGES_DIR', APP_DIR . '/pages');
define('CONFIG_DIR', APP_DIR . '/config');

$configFile = require CONFIG_DIR . '/config.php';

$env = isset($configFile['env']) ? $configFile['env'] : [];
$siteUrl = get_site_url();
// URL ENVEROMENTS
define('URL_PRO', isset($env['pro']) ? $env['pro'] : $siteUrl);
define('URL_DEV', isset($env['dev']) ? $env['dev'] : $siteUrl);
define('URL_LOC', isset($env['loc']) ? $env['loc'] : $siteUrl);

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
define('BLOG_TITLE', getBlogTitle());
define('ADMIN_EMAIL', get_bloginfo('admin_email'));

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
