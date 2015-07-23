<?php
require_once 'vendor/autoload.php';
require_once 'mvc/libs/Helpers.php';

use Libs\Actions;
use Libs\Filters;

// --------------------------------------------------------------
// Some constants
// --------------------------------------------------------------

// URL_ENVEROMENTS
define(URL_PRO, 'knob.chemaclass.com');
define(URL_DEV, 'knob.chemaclass.com');
define(URL_LOC, 'knob.chemaclass.local');

// SOME DIRECTORIES
define(PUBLIC_DIR, get_template_directory_uri() . '/public');
define(COMPONENTS_DIR, get_template_directory_uri() . '/vendor/components');

// BLOG_INFO
function getBlogTitle() {
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
Actions::adminPrintScripts();
Actions::adminPrintStyles();
Actions::loginView();
Actions::wpBeforeAdminBarRender();
Actions::userProfileAddImgAvatarAndHeader();
Actions::userProfileAddSocialNetworks();
Actions::userProfileAddLanguage();
Actions::widgetsInit();

// --------------------------------------------------------------
// Filters
// --------------------------------------------------------------
Filters::getAvatar();

/**
 * Remove the admin bar in prod
 */
show_admin_bar(false);

@include_once 'test.php';