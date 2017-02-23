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

if (!isset($_SESSION)) {
    session_start();
}

// BASE DIRECTORIES
define('PROJECT_DIR', dirname(__FILE__));
define('VENDOR_DIR', PROJECT_DIR . '/vendor');
define('VENDOR_KNOB_BASE_DIR', VENDOR_DIR . '/chemaclass/knob-base');
define('VENDOR_KNOB_BASE_WP_DIR', VENDOR_KNOB_BASE_DIR . '/wp');
define('APP_DIR', PROJECT_DIR . '/app');
define('PAGES_DIR', APP_DIR . '/pages');
define('CONFIG_DIR', APP_DIR . '/config');

$configPath = CONFIG_DIR . '/config.php';
$config = file_exists($configPath) ? require $configPath : [];

$env = isset($config['env']) ? $config['env'] : [];
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
    }
    return wp_title("-", false, "right") . " " . get_bloginfo('name');
}

define('BLOG_TITLE', getBlogTitle());
define('ADMIN_EMAIL', get_bloginfo('admin_email'));

use \Knob\Libs\Utils;
use \Knob\I18n\I18n;
use \Knob\App;

$i18n = new I18n(new Utils(APP_DIR, [
    Utils::AVAILABLE_LANGUAGES => [
        Utils::LANG_KEY => Utils::LANG_VALUE,
    ],
    Utils::DEFAULT_LANGUAGE => Utils::DEFAULT_LANG,
    Utils::DEFAULT_LANGUAGE_FILE => Utils::DEFAULT_LANG_FILE,
]));
App::register('i18n', $i18n);

// --------------------------------------------------------------
// Widgets
// --------------------------------------------------------------
$widgets = new Libs\Widgets($i18n);
App::register('widgets', $widgets);

// --------------------------------------------------------------
// Menus
// --------------------------------------------------------------
$menus = new Libs\Menus();
App::register('menus', $menus);

// --------------------------------------------------------------
// Actions
// --------------------------------------------------------------
$actions = new Libs\Actions($i18n, $widgets, $menus);
App::register('actions', $actions);

// --------------------------------------------------------------
// Filters
// --------------------------------------------------------------
$filters = new Libs\Filters($i18n);
App::register('filters', $filters);

@include_once 'test.php';
