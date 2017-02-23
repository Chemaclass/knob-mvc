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
use Knob\I18n\I18n;
use Knob\Libs\MenusInterface;
use Knob\Libs\WidgetsInterface;
use Libs\WalkerNavMenu;

/**
 * @author José María Valera Reales
 */
abstract class BaseController extends KnobBaseController
{
    protected $widgetsContent = [];
    protected $menusContent = [];

    /**
     * @param I18n $i18n
     * @param WidgetsInterface $widgets
     * @param MenusInterface $menus
     */
    public function __construct(I18n $i18n, WidgetsInterface $widgets, MenusInterface $menus)
    {
        parent::__construct($i18n, $widgets, $menus);

        $this->loadWidgets();
        $this->loadMenus();
    }

    private function loadWidgets()
    {
        foreach ($this->widgets->dynamicSidebarActive() as $key => $sidebarActive) {
            ob_start();
            dynamic_sidebar($sidebarActive);
            $this->widgetsContent[$sidebarActive] = ob_get_clean();
        }
    }

    private function loadMenus()
    {
        foreach ($this->menus->activeIds() as $key => $menuActive) {
            $this->menusContent[$menuActive] = wp_nav_menu([
                'echo' => false,
                'theme_location' => $menuActive,
                'menu_class' => 'nav navbar-nav menu ' . str_replace('_', '-', $menuActive),
                'walker' => new WalkerNavMenu(),
            ]);
        }
    }

    /**
     * Add the global variables for all controllers
     *
     * @return array
     */
    public function globalVariables()
    {
        $globalVars = [];
        // Sidebar items
        foreach ($this->widgets->dynamicSidebarActive() as $key => $sidebarActiveId) {
            $globalVars['widgets'][$key] = [
                'active' => is_active_sidebar($sidebarActiveId),
                'content' => $this->widgetsContent[$sidebarActiveId],
            ];
        }
        // Menus
        foreach ($this->menus->activeIds() as $key => $menuActiveId) {
            $globalVars['menu'][$key] = [
                'active' => has_nav_menu($menuActiveId),
                'content' => $this->menusContent[$menuActiveId],
            ];
        }

        return $globalVars;
    }

    /**
     * @param string $key
     * @param array $args
     * @return string
     */
    public function trans($key, array $args = [])
    {
        return $this->i18n->trans($key, $args);
    }

    /**
     * @param string $key
     * @param array $args
     * @return string
     */
    public function transU($key, array $args = [])
    {
        return $this->i18n->transU($key, $args);
    }
}
