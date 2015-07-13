<?php

namespace Controllers;

use Libs\Utils;
use Libs\Env;
use Models\User;
use Models\Post;
use I18n\I18n;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Mustache_Logger_StreamLogger;
use Models\Term;

/**
 *
 * @author José María Valera Reales
 */
abstract class BaseController {

	/*
	 * Some const
	 */
	const LIMIT_POST_DEFAULT = 5;

	/*
	 * Members
	 */
	protected $currentUser;
	protected $template;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->currentUser = User::getCurrent();

		$templatesFolder = self::getTemplatesFolderLocation();

		$this->template = new Mustache_Engine(array (
			'cache_file_mode' => 0660,
			'cache_lambda_templates' => true,
			'loader' => new Mustache_Loader_FilesystemLoader($templatesFolder),
			'partials_loader' => new Mustache_Loader_FilesystemLoader($templatesFolder),
			'helpers' => array (
				'trans' => function ($value) {
					return I18n::trans($value);
				},
				'transu' => function ($value) {
					return I18n::transu($value);
				},
				'case' => [
					'lower' => function ($value) {
						return strtolower((string) $value);
					},
					'upper' => function ($value) {
						return strtoupper((string) $value);
					}
				],
				'count' => function ($value) {
					return count($value);
				},
				'moreThan1' => function ($value) {
					return count($value) > 1;
				},
				'date' => [
					'xmlschema' => function ($value) {
						return date('c', strtotime($value));
					},
					'string' => function ($value) {
						return date('l, d F Y', strtotime($value));
					},
					'format' => function ($value) {
						return date(get_option('date_format'), strtotime($value));
					}
				],
				'toArray' => function ($value) {
					return explode(',', $value);
				},
				'ucfirst' => function ($value) {
					return ucfirst($value);
				}
			),
			'escape' => function ($value) {
				return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
			},
			'charset' => 'UTF-8',
			'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
			'strict_callables' => true,
			'pragmas' => [
				Mustache_Engine::PRAGMA_FILTERS,
				Mustache_Engine::PRAGMA_BLOCKS
			]
		));
	}

	/**
	 * Return the relative path location where are the templates.
	 *
	 * @return string
	 */
	protected static function getTemplatesFolderLocation() {
		return str_replace('//', '/', dirname(__FILE__) . '/') . '../templates';
	}

	/**
	 * Add the global variables for all controllers
	 *
	 * @param array $templateVars
	 */
	private function addGlobalVariables(&$templateVars = []) {
		return array_merge($templateVars, [
			'adminEmail' => ADMIN_EMAIL,
			'atomUrl' => get_bloginfo('atom_url'),

			'blogTitle' => BLOG_TITLE,
			'blogDescription' => ($d = I18n::trans('internal.blog_description')) ? $d : get_bloginfo('description'),

			'categoryBase' => ($c = get_option('category_base')) ? $c : Post::CATEGORY_BASE_DEFAULT,
			'charset' => get_bloginfo('charset'),
			'commentsAtomUrl' => get_bloginfo('comments_atom_url'),
			'commentsRss2Url' => get_bloginfo('comments_rss2_url'),
			'componentsDir' => COMPONENTS_DIR,
			'currentLang' => I18n::getLangBrowserByCurrentUser(),
			'currentLangFullname' => I18n::getLangFullnameBrowserByCurrentUser(),
			'currentUser' => $this->currentUser,

			'homeUrl' => get_home_url(),
			'htmlType' => get_bloginfo('html_type'),

			'isEnvProd' => Env::isProd(),
			'isEnvDev' => Env::isDev(),
			'isEnvLoc' => Env::isLoc(),
			'isUserLoggedIn' => is_user_logged_in(),

			'language' => get_bloginfo('language'),
			'loginUrl' => wp_login_url($_SERVER['REQUEST_URI']),

			'name' => get_bloginfo('name'),

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
		]);
	}

	/**
	 * Print head + template + footer
	 *
	 * @param string $templateName
	 *        	Template name to print
	 * @param array $templateVars
	 *        	Parameters to template
	 */
	public function renderPage($templateName, $templateVars = []) {
		$this->addGlobalVariables($templateVars);
		$this->addSidebarVars($templateVars, true);
		echo $this->render('head', $templateVars);
		wp_head();
		echo '</head>';
		echo $this->render($templateName, $templateVars);
		wp_footer();
		echo $this->render('footer', $templateVars);
	}

	/**
	 * Sidebar variables.
	 *
	 * @param array $templateVars
	 * @param boolean $withSidebar
	 */
	private function addSidebarVars(&$templateVars, $withSidebar = true) {
		/*
		 * Active
		 */
		$templateVars['sidebar']['active'] = $withSidebar;

		/*
		 * Pages
		 */
		$templateVars['pages'] = Post::getAllPages([
			'excludeSlugs' => [
				'lang'
			]
		]);
		/*
		 * Categories
		 */
		$templateVars['categories'] = Term::getCategories();

		/*
		 * Tags
		 */
		$templateVars['tags'] = Term::getTags();
	}

	/**
	 * Render a partial
	 *
	 * @param string $templateName
	 * @param array $templateVars
	 */
	public function render($templateName, $templateVars = []) {
		return $this->template->render($templateName, $this->addGlobalVariables($templateVars));
	}
}
