<?php
/*
 * This file is part of the Knob-base package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Libs;

use Knob\Libs\Template as KnobTemplate;

/**
 * Template singleton
 *
 * @author José María Valera Reales
 */
class Template extends KnobTemplate
{

    static $mustacheHelpersFile = 'mustache_helpers';

    static $templatesDir = 'templates';

    /*
     * Widgets
     */
    static $widgetsRight = 'widgetsRight';

    static $widgetsFooter = 'widgetsFooter';

    /*
     * Menus
     */
    static $menuHeader = 'menuHeader';

    static $menuFooter = 'menuFooter';

    /**
     * Constructor
     */
    private function __construct()
    {
        parent::__construct();
    }

    /**
     * Return a list with the dinamic sidebar for widgets active
     *
     * @return array<string>
     */
    public static function getDinamicSidebarActive()
    {
        return [
            static::$widgetsRight,
            static::$widgetsFooter
        ];
    }

    /**
     * Return a list with the active menus
     */
    public static function getMenusActive()
    {
        return [
            static::$menuHeader,
            static::$menuFooter
        ];
    }
}
