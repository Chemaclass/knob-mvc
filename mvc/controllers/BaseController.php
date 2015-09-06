<?php

namespace Controllers;

use Config\Params;
use Libs\Template;
use Models\Archive;
use Models\Post;
use Models\Term;
use Models\User;
use Libs\WalkerNavMenu;

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
	protected $menus = [ ];
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
		 * Menus.
		 */
		foreach ( Template::getMenusActive() as $s ) {
			$this->menus[$s] = wp_nav_menu([
				'echo' => false,
				'theme_location' => $s,
				'menu_class' => 'nav navbar-nav menu ' . str_replace('_', '-', $s),
				'walker' => new WalkerNavMenu()
			]);
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
		 * Sidebar items
		 */
		$active = ($u = User::getCurrent()) ? $u->isWithSidebar() : User::WITH_SIDEBAR_DEFAULT;
		$globalVars['widgets'] = [
			'right' => [
				'active' => $active,
				'content' => $this->widgets[Template::WIDGETS_RIGHT]
			],
			'footer' => [
				'active' => $active,
				'content' => $this->widgets[Template::WIDGETS_FOOTER]
			]
		];

		/*
		 * Menus
		 */
		$globalVars['menu'] = [
			'header' => [
				'active' => true,
				'content' => $this->menus[Template::MENU_HEADER]
			],
			'footer' => [
				'active' => true,
				'content' => $this->menus[Template::MENU_FOOTER]
			]
		];

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
