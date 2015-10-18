<?php
namespace Widgets;

use Knob\Models\Archive;
use Knob\Widgets\WidgetBase;

/**
 *
 * @author José María Valera Reales
 */
class ArchivesWidget extends WidgetBase
{

    /**
     * (non-PHPdoc)
     *
     * @see \Widgets\WidgetBase::widget()
     */
    public function widget($args, $instance)
    {

        /*
         * Put the archives to show into the instance var.
         */
        $instance['archives'] = Archive::getMonthly();

        /*
         * And call the widget func from the parent class WidgetBase.
         */
        parent::widget($args, $instance);
    }
}
