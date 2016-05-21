<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Libs;

use Knob\Libs\WidgetsInterface;
use Widgets\ArchivesWidget;
use Widgets\CategoriesWidget;
use Widgets\LangWidget;
use Widgets\LoginWidget;
use Widgets\PagesWidget;
use Widgets\SearcherWidget;
use Widgets\TagsWidget;

/**
 * Widget Controller
 *
 * @author José María Valera Reales
 */
class Widgets implements WidgetsInterface
{

    /*
     * Widgets
     */
    static $widgetsLeft = 'widgets_left';

    static $widgetsRight = 'widgets_right';

    static $widgetsFooter = 'widgets_footer';

    /**
     * Return a list with the dinamic sidebar for widgets active
     *
     * @return array<string>
     */
    public static function getDinamicSidebarActive()
    {
        return [
            'left' => static::$widgetsLeft,
            'right' => static::$widgetsRight,
            'footer' => static::$widgetsFooter
        ];
    }

    /**
     * Setup
     */
    public static function setup()
    {
        $widgets = [
            new ArchivesWidget(),
            new CategoriesWidget(),
            new LangWidget(),
            new LoginWidget(),
            new PagesWidget(),
            new SearcherWidget(),
            new TagsWidget()
        ];
        
        foreach ($widgets as $w) {
            $w->register();
        }
    }
}
