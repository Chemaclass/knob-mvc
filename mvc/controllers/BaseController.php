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
	protected $configParams;
	protected $currentUser;
	protected $template;
	protected $widgets;

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
		$this->template = Template::getInstance()->getRenderEngine();

		/*
		 * Widgets.
		 */
		$this->widgets = [ ];
		ob_start();
		dynamic_sidebar('sidebar_right_top');
		$this->widgets['sidebar_right_top'] = ob_get_clean();

		ob_start();
		dynamic_sidebar('sidebar_right_bottom');
		$this->widgets['sidebar_right_bottom'] = ob_get_clean();

		ob_start();
		dynamic_sidebar('footer_top');
		$this->widgets['footer_top'] = ob_get_clean();

		ob_start();
		dynamic_sidebar('footer_bottom');
		$this->widgets['footer_bottom'] = ob_get_clean();
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
