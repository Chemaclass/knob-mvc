<?php
namespace Widgets;

use Knob\Models\Post;
use Knob\Widgets\WidgetBase;

/**
 *
 * @author José María Valera Reales
 */
class PagesWidget extends WidgetBase
{

    /**
     * (non-PHPdoc)
     *
     * @see \Widgets\WidgetBase::widget()
     */
    public function widget($args, $instance)
    {

        /*
         * Put the pages to show into the instance var.
         */
        $instance['pages'] = Post::getPages();

        /*
         * And call the widget func from the parent class WidgetBase.
         */
        parent::widget($args, $instance);
    }
}
