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
		$this->configParams = Params::all();
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
		return $this->template->render($templateName, $this->addGlobalVariables($templateVars));
	}
}
