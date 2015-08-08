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
	private function getGlobalVariables() {
		$globalVars = [ ];
		/*
		 * Active
		 */

		$globalVars['sidebar']['active'] = ($u = User::getCurrent()) ? $u->isWithSidebar() : User::WITH_SIDEBAR_DEFAULT;

		/*
		 * Sidebar items
		 */
		$globalVars[Template::SIDEBAR_RIGHT_TOP]['widgets'] = $this->widgets[Template::SIDEBAR_RIGHT_TOP];
		$globalVars[Template::SIDEBAR_RIGHT_BOTTOM]['widgets'] = $this->widgets[Template::SIDEBAR_RIGHT_BOTTOM];
		$globalVars[Template::FOOTER_TOP]['widgets'] = $this->widgets[Template::FOOTER_TOP];
		$globalVars[Template::FOOTER_BOTTOM]['widgets'] = $this->widgets[Template::FOOTER_BOTTOM];

		/*
		 * Archives
		 */
		$globalVars['archives'] = Archive::getMonthly();

		/*
		 * Pages
		 */
		$globalVars['pages'] = Post::getAllPages($this->configParams['pages']);

		/*
		 * Categories
		 */
		$globalVars['categories'] = Term::getCategories();

		/*
		 * Tags
		 */
		$globalVars['tags'] = Term::getTags();

		/*
		 * Current User
		 */
		$globalVars['currentUser'] = $this->currentUser;

		/*
		 * Generics variables
		 */
		return array_merge($globalVars, $this->configParams['globalVars']);
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
		$templateVars = array_merge($templateVars, $this->getGlobalVariables());
		$addGlobalVariablesToVars = false; // cause we already did it.
		echo $this->render('head', $templateVars, $addGlobalVariablesToVars);
		wp_head();
		echo '</head>';
		echo $this->render($templateName, $templateVars, $addGlobalVariablesToVars);
		wp_footer();
		echo $this->render('footer', $templateVars, $addGlobalVariablesToVars);
	}

	/**
	 * Render a partial
	 *
	 * @param string $templateName
	 * @param array $templateVars
	 */
	public function render($templateName, $templateVars = [], $addGlobalVariables = true) {
		if ($addGlobalVariables) {
			$templateVars = array_merge($templateVars, $this->getGlobalVariables());
		}
		return $this->renderEngine->render($templateName, $templateVars);
	}
}
