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

use Knob\Libs\MenuInterface;

/**
 * Menu
 *
 * @author José María Valera Reales
 */
class Menu implements MenuInterface
{

    static $menuHeader = 'menu_header';

    static $menuFooter = 'menu_footer';

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
