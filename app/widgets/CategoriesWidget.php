<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
