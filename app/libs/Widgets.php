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

use Knob\I18n\I18n;
use Knob\Libs\WidgetsInterface;
use Knob\Widgets\WidgetBase;
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
    /** @var I18n */
    private $i18n;

    /** @var string */
    private $widgetsLeft;

    /** @var string */
    private $widgetsRight;

    /** @var string */
    private $widgetsFooter;

    /**
     * @param I18n $i18n
     */
    public function __construct(I18n $i18n)
    {
        $this->i18n = $i18n;
        $this->widgetsLeft = 'widgets_left';
        $this->widgetsRight = 'widgets_right';
        $this->widgetsFooter = 'widgets_footer';

        $widgets = [
            new ArchivesWidget(),
            new CategoriesWidget(),
            new LangWidget(),
            new LoginWidget(),
            new PagesWidget(),
            new SearcherWidget(),
            new TagsWidget(),
        ];
        /** @var WidgetBase $w */
        foreach ($widgets as $w) {
            $w->register($this->i18n);
        }
    }

    /**
     * Return a list with the dynamic sidebar for widgets active
     *
     * @return string[]
     */
    public function dynamicSidebarActive()
    {
        return [
            'left' => $this->widgetsLeft,
            'right' => $this->widgetsRight,
            'footer' => $this->widgetsFooter,
        ];
    }
}
