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

use Knob\Libs\Widgets as KnobWidgets;
use Knob\Widgets\WidgetBase;

/**
 * Widget Controller
 *
 * @author José María Valera Reales
 */
class WidgetsRegister implements KnobWidgets
{
    /** @var string */
    private $widgetsLeft;

    /** @var string */
    private $widgetsRight;

    /** @var string */
    private $widgetsFooter;

    /**
     * @param array $widgets
     * @internal param I18n $i18n
     */
    public function __construct(array $widgets)
    {
        $this->widgetsLeft = 'widgets_left';
        $this->widgetsRight = 'widgets_right';
        $this->widgetsFooter = 'widgets_footer';

        /** @var WidgetBase $w */
        foreach ($widgets as $w) {
            $w->register();
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
