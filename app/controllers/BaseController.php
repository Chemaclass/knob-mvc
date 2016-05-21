<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Controllers;

use Knob\Controllers\BaseController as KnobBaseController;
use Libs\WalkerNavMenu;
use Libs\Menu;
use Libs\Widgets;
use Models\User;

/**
 * Base Controller
 *
 * @author José María Valera Reales
 */
abstract class BaseController extends KnobBaseController
{

    protected $widgets = [];

    protected $menus = [];

    public function __construct()
    {
        parent::__construct();
        
        // Widgets
        foreach (Widgets::getDinamicSidebarActive() as $key => $sidebarActive) {
            ob_start();
            dynamic_sidebar($sidebarActive);
            $this->widgets[$sidebarActive] = ob_get_clean();
        }
        
        // Menus
        foreach (Menu::getMenusActive() as $key => $menuActive) {
            $this->menus[$menuActive] = wp_nav_menu([
                'echo' => false,
                'theme_location' => $menuActive,
                'menu_class' => 'nav navbar-nav menu ' . str_replace('_', '-', $menuActive),
                'walker' => new WalkerNavMenu()
            ]);
        }
    }

    /**
     * Add the global variables for all controllers
     *
     * @return array
     */
    public function getGlobalVariables()
    {
        $globalVars = [];
        // Sidebar items
        foreach (Widgets::getDinamicSidebarActive() as $key => $sidebarActive) {
            $globalVars['widgets'][$key] = [
                'active' => is_active_sidebar($sidebarActive),
                'content' => $this->widgets[$sidebarActive]
            ];
        }
        // Menus
        foreach (Menu::getMenusActive() as $key => $menuActive) {
            $globalVars['menu'][$key] = [
                'active' => has_nav_menu($menuActive),
                'content' => $this->menus[$menuActive]
            ];
        }
        
        return $globalVars;
    }
}
