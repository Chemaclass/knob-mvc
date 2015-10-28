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
use Knob\Models\User;
use Libs\Template;
use Libs\WalkerNavMenu;

/**
 * Base Controller
 *
 * @author José María Valera Reales
 */
class BaseController extends KnobBaseController
{

    protected $widgets = [];

    protected $menus = [];

    protected $template = null;

    public function __construct()
    {
        parent::__construct();

        $this->template = Template::getInstance();

        // Widgets
        foreach (Template::getDinamicSidebarActive() as $s) {
            ob_start();
            dynamic_sidebar($s);
            $this->widgets[$s] = ob_get_clean();
        }

        // Menus
        foreach (Template::getMenusActive() as $s) {
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
     * Return the template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add the global variables for all controllers
     *
     * @return array $templateVars
     */
    public function getGlobalVariables()
    {
        $globalVars = [];

        // Sidebar items
        $active = ($u = User::getCurrent()) ? $u->isWithSidebar() : User::WITH_SIDEBAR_DEFAULT;
        $globalVars['widgets'] = [
            'right' => [
                'active' => $active,
                'content' => $this->widgets[Template::$widgetsRight]
            ],
            'footer' => [
                'active' => $active,
                'content' => $this->widgets[Template::$widgetsFooter]
            ]
        ];

        // Menus
        $globalVars['menu'] = [
            'header' => [
                'active' => has_nav_menu(Template::$menuHeader),
                'content' => $this->menus[Template::$menuHeader]
            ],
            'footer' => [
                'active' => has_nav_menu(Template::$menuFooter),
                'content' => $this->menus[Template::$menuFooter]
            ]
        ];

        return array_merge(parent::getGlobalVariables(), $globalVars);
    }
}
