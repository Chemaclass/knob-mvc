<?php
require_once 'vendor/autoload.php';

use Libs\Actions;
use Libs\Filters;

// --------------------------------------------------------------
// Some constants
// --------------------------------------------------------------
define(URL_PRO, 'knob.chemaclass.com');
define(URL_DEV, 'knob.chemaclass.com');
define(URL_LOC, 'knob.chemaclass.local');
define(PUBLIC_DIR, get_template_directory_uri() . '/public');
define(COMPONENTS_DIR, get_template_directory_uri() . '/vendor/components');

// --------------------------------------------------------------
// Actions
// --------------------------------------------------------------
Actions::adminPrintScripts();
Actions::adminPrintStyles();
Actions::wpBeforeAdminBarRender();

// --------------------------------------------------------------
// Filters
// --------------------------------------------------------------
Filters::wpFilterTest();

/**
 * Remove the admin bar in prod
 */
show_admin_bar(false);

@include_once 'test.php';