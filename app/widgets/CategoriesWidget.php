<?php
namespace Widgets;

use Knob\Models\Term;
use Knob\Widgets\WidgetBase;

/**
 *
 * @author José María Valera Reales
 */
class CategoriesWidget extends WidgetBase
{

    /**
     * (non-PHPdoc)
     *
     * @see \Widgets\WidgetBase::widget()
     */
    public function widget($args, $instance)
    {

        /*
         * Put the categories to show into the instance var.
         */
        $instance['categories'] = Term::getCategories();

        /*
         * And call the widget func from the parent class WidgetBase.
         */
        parent::widget($args, $instance);
    }
}
