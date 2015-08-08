<?php

namespace Controllers;

use Libs\Utils;
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
	 * Members
	 */
	protected $configParams = [ ];
	protected $currentUser = null;
	protected $template = null;
	protected $widgets = [ ];

	/**
	 * Constructor
	 */
	public function __construct() {
		/*
		 * Params.
		 */
		$this->configParams = Params::all();

		/*
		 * Current User.
		 */
		$this->currentUser = User::getCurrent();

		/*
		 * Template Render Engine.
		 */
		$this->template = Template::getInstance();
		$this->renderEngine = $this->template->getRenderEngine();

		/*
		 * Widgets.
		 */
		foreach ( Template::getDinamicSidebarActive() as $s ) {
			ob_start();
			dynamic_sidebar($s);
			$this->widgets[$s] = ob_get_clean();
		}
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
		$templateVars[Template::SIDEBAR_RIGHT_TOP]['widgets'] = $this->widgets[Template::SIDEBAR_RIGHT_TOP];
		$templateVars[Template::SIDEBAR_RIGHT_BOTTOM]['widgets'] = $this->widgets[Template::SIDEBAR_RIGHT_BOTTOM];
		$templateVars[Template::FOOTER_TOP]['widgets'] = $this->widgets[Template::FOOTER_TOP];
		$templateVars[Template::FOOTER_BOTTOM]['widgets'] = $this->widgets[Template::FOOTER_BOTTOM];

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
		 * Current User
		 */
		$templateVars['currentUser'] = $this->currentUser;

		/*
		 * Generics variables
		 */
		return array_merge($templateVars, $this->configParams['templateVars']);
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
		return $this->renderEngine->render($templateName, $this->addGlobalVariables($templateVars));
	}
}
