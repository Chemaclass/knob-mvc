<?php

namespace Config;

use I18n\I18n;
use Models\Post;
use Libs\Env;

class Params {

	/**
	 * Return all params.
	 * This class is for to separate the code and not put it
	 * all into the controller.
	 *
	 * @return array
	 */
	public static function all() {
		return [

			/*
			 * ====================================
			 * Params to pages
			 * ====================================
			 */
			'pages' => [
				'excludeSlugs' => [
					'lang',
					'random'
				]
			],

			/*
			 * ====================================
			 * Params to all templates
			 * ====================================
			 */
			'templateVars' => [
				'adminEmail' => ADMIN_EMAIL,
				'atomUrl' => get_bloginfo('atom_url'),

				'blogAuthor' => 'José María Valera Reales',
				'blogCharset' => get_bloginfo('charset'),
				'blogKeywords' => 'knob, wordpress, framework, mvc, template',
				'blogName' => get_bloginfo('name'),
				'blogTitle' => BLOG_TITLE,
				'blogDescription' => ($d = I18n::trans('internal.blog_description')) ? $d : get_bloginfo('description'),

				'categoryBase' => ($c = get_option('category_base')) ? $c : Post::CATEGORY_BASE_DEFAULT,
				'commentsAtomUrl' => get_bloginfo('comments_atom_url'),
				'commentsRss2Url' => get_bloginfo('comments_rss2_url'),
				'componentsDir' => COMPONENTS_DIR,
				'currentLang' => I18n::getLangBrowserByCurrentUser(),
				'currentLangFullname' => I18n::getLangFullnameBrowserByCurrentUser(),

				'homeUrl' => get_home_url(),
				'htmlType' => get_bloginfo('html_type'),

				'isEnvProd' => Env::isProd(),
				'isEnvDev' => Env::isDev(),
				'isEnvLoc' => Env::isLoc(),
				'isUserLoggedIn' => is_user_logged_in(),

				'language' => get_bloginfo('language'),
				'loginUrl' => wp_login_url($_SERVER['REQUEST_URI']),

				'pingbackUrl' => get_bloginfo('pingback_url'),
				'postsPerPage' => get_option('posts_per_page'),
				'publicDir' => PUBLIC_DIR,

				'rdfUrl' => get_bloginfo('rdf_url'),
				'rss2Url' => get_bloginfo('rss2_url'),
				'rssUrl' => get_bloginfo('rss_url'),

				'stylesheetDirectory' => get_bloginfo('stylesheet_directory'),
				'stylesheetUrl' => get_bloginfo('stylesheet_url'),

				'tagBase' => ($t = get_option('tag_base')) ? $t : Post::TAG_BASE_DEFAULT,
				'templateDirectory' => get_bloginfo('template_directory'),
				'templateUrl' => get_bloginfo('template_url'),
				'textDirection' => get_bloginfo('text_direction'),

				'version' => get_bloginfo('version'),

				'wpurl' => get_bloginfo('wpurl')
			]
		];
	}
}