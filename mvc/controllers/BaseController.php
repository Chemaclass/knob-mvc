<?php

namespace Controllers;

use Libs\Utils;
use Libs\Env;
use Models\User;
use Models\Post;
use I18n\I18n;
use Models\Term;
use Models\Archive;
use Libs\Template;
use Config\Params;

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
	protected $configParams;
	protected $currentUser;
	protected $template;
	protected $widgets;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->configParams = Params::getInstance()->all();
		$this->currentUser = User::getCurrent();
		$this->template = Template::getInstance()->getRenderEngine();
		$this->widgets = [ ];
	}

	/**
	 * Add the global variables for all controllers
	 *
	 * @param array $templateVars
	 */
	private function addGlobalVariables(&$templateVars = []) {
		/*
		 * Active
		 */
		$templateVars['sidebar']['active'] = ($u = User::getCurrent()) ? $u->isWithSidebar() : User::WITH_SIDEBAR_DEFAULT;

		/*
		 * Sidebar items
		 */
		$templateVars['sidebar_right_top']['widgets'] = $this->widgets['sidebar_right_top'];
		$templateVars['sidebar_right_bottom']['widgets'] = $this->widgets['sidebar_right_bottom'];
		$templateVars['footer_top']['widgets'] = $this->widgets['footer_top'];
		$templateVars['footer_bottom']['widgets'] = $this->widgets['footer_bottom'];

		/*
		 * Archives
		 */
		$templateVars['archives'] = Archive::getMonthly();

		/*
		 * Pages
		 */
		$templateVars['pages'] = Post::getAllPages($this->configParams['pages']);

		/*
		 * Categories
		 */
		$templateVars['categories'] = Term::getCategories();

		/*
		 * Tags
		 */
		$templateVars['tags'] = Term::getTags();

		/*
		 * Generics variables
		 */
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
		echo $this->render('head', $templateVars);
		wp_head();
		echo '</head>';
		echo $this->render($templateName, $templateVars);
		wp_footer();
		echo $this->render('footer', $templateVars);
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
