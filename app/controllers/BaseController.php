<?php

namespace Controllers;

use Knob\Controllers\BaseController as KnobBaseController;
use Knob\Models\Archive;
use Knob\Models\Post;
use Knob\Models\Term;
use Knob\Models\User;
use Knob\Libs\Template;
use Knob\Libs\WalkerNavMenu;
use Knob\Libs\Utils;

/**
 *
 * @author José María Valera Reales
 */
abstract class BaseController extends KnobBaseController {

	/*
	 * Members
	 */
	protected $widgets = [ ];
	protected $menus = [ ];

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		/*
		 * Widgets.
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
			$this->menus[$s] = wp_nav_menu(
					[
						'echo' => false,
						'theme_location' => $s,
						'menu_class' => 'nav navbar-nav menu ' . str_replace('_', '-', $s),
						'walker' => new WalkerNavMenu()
					]);
		}
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
				'active' => has_nav_menu(Template::MENU_HEADER),
				'content' => $this->menus[Template::MENU_HEADER]
			],
			'footer' => [
				'active' => has_nav_menu(Template::MENU_FOOTER),
				'content' => $this->menus[Template::MENU_FOOTER]
			]
		];

		return array_merge(parent::getGlobalVariables(), $globalVars);
	}
}
