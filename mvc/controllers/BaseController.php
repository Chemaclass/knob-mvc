<?php

namespace Controllers;

use Config\Params;
use Libs\Template;
use Models\Archive;
use Models\Post;
use Models\Term;
use Models\User;

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
	protected $widgets = [ ];
	protected $template = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		/*
		 * Params.
		 */
		$this->configParams = Params::getInstance()->getAll();

		/*
		 * Current User.
		 */
		$this->currentUser = User::getCurrent();

		/*
		 * Sidebar.
		 */
		foreach ( Template::getDinamicSidebarActive() as $s ) {
			ob_start();
			dynamic_sidebar($s);
			$this->widgets[$s] = ob_get_clean();
		}

		/*
		 * Template Render Engine.
		 */
		$this->template = Template::getInstance();
	}

	/**
	 * Add the global variables for all controllers
	 *
	 * @param array $templateVars
	 */
	public function getGlobalVariables() {
		$globalVars = [ ];

		/*
		 * Active
		 */
		$globalVars['sidebar_right']['active'] = ($u = User::getCurrent()) ? $u->isWithSidebar() : User::WITH_SIDEBAR_DEFAULT;

		/*
		 * Sidebar items
		 */
		$globalVars[Template::SIDEBAR_RIGHT] = $this->widgets[Template::SIDEBAR_RIGHT];
		$globalVars[Template::FOOTER] = $this->widgets[Template::FOOTER];

		/*
		 * Generics variables
		 */
		return array_merge($globalVars, $this->configParams['globalVars']);
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
		return $this->template->getRenderEngine()->render($templateName, $templateVars);
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
}
