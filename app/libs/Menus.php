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

use Knob\Libs\MenusInterface;

/**
 * Menu
 *
 * @author José María Valera Reales
 */
class Menus implements MenusInterface
{
    /**
     * Return a list with the active menus
     */
    public function activeIds()
    {
        return [
            'header' => 'menu_header',
            'footer' => 'menu_footer',
        ];
    }
}
