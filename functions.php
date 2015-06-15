<?php
require_once 'vendor/autoload.php';

use Libs\Actions;
use Libs\Filters;

define(URL_PRO, 'knob.chemaclass.com');
define(URL_DEV, 'knob.chemaclass.com');
define(URL_LOC, 'knob.chemaclass.local');

// --------------------------------------------------------------
// Actions
// --------------------------------------------------------------
Actions::wpBeforeAdminBarRender();
// --------------------------------------------------------------
// Filters
// --------------------------------------------------------------
// and so on...

$publicDir = get_template_directory_uri() . '/public';
$componentsDir = get_template_directory_uri() . '/vendor/components';

/**
 * Put styles into the admin view
 */
add_action('admin_print_styles', function () use($publicDir) {
	wp_enqueue_style('bootstrap', $componentsDir . '/bootstrap/css/bootstrap.css');
	wp_enqueue_style('font-awesome', $componentsDir . '/font-awesome/css/font-awesome.min.css');
	wp_enqueue_style('main', $publicDir . '/css/main.css');
});

/**
 * Put scripts into the admin view
 */
add_action('admin_print_scripts', function () use($publicDir) {
	wp_enqueue_script('jquery-plugin', $componentsDir . '/jquery/jquery.min.js');
	wp_enqueue_script('bootstrap-plugin', $componentsDir . '/bootstrap/js/bootstrap.min.js');
	wp_enqueue_script('nm', $publicDir . '/js/main.js');
});

/**
 * Remove the admin bar in prod
 */
show_admin_bar(false);

@include_once 'test.php';