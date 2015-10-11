<?php
namespace Controllers;

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
abstract class BaseController
{

    private $mustacheParams = [];

    protected $currentUser = null;

    protected $template = null;

    protected $widgets = [];

    protected $menus = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mustacheParams = Utils::getMustacheParams();
        $this->currentUser = User::getCurrent();
        $this->template = Template::getInstance();

        // Widgets
        foreach (Template::getDinamicSidebarActive() as $s) {
            ob_start();
            dynamic_sidebar($s);
            $this->widgets[$s] = ob_get_clean();
        }

        // Menus
        foreach (Template::getMenusActive() as $s) {
            $this->menus[$s] = wp_nav_menu([
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
                'content' => $this->widgets[Template::WIDGETS_RIGHT]
            ],
            'footer' => [
                'active' => $active,
                'content' => $this->widgets[Template::WIDGETS_FOOTER]
            ]
        ];

        // Menus
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

        return array_merge($this->mustacheParams, $globalVars);
    }

    /**
     * Render a partial
     *
     * @param string $templateName
     * @param array $templateVars
     */
    public function render($templateName, $templateVars = [], $addGlobalVariables = true)
    {
        if ($addGlobalVariables) {
            $templateVars = array_merge($templateVars, $this->getGlobalVariables());
        }
        return $this->template->getRenderEngine()->render($templateName, $templateVars);
    }

    /**
     * Print head + template + footer
     *
     * @param string $templateName
     *            Template name to print
     * @param array $templateVars
     *            Parameters to template
     */
    public function renderPage($templateName, $templateVars = [])
    {
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
