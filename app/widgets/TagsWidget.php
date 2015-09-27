<?php

namespace Widgets;

use Knob\Models\Term;

/**
 *
 * @author José María Valera Reales
 *
 */
class TagsWidget extends WidgetBase {

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Widgets\WidgetBase::widget()
	 */
	public function widget($args, $instance) {

		/*
		 * Put the tags to show into the instance var.
		 */
		$instance['tags'] = Term::getTags();

		/*
		 * And call the widget func from the parent class WidgetBase.
		 */
		parent::widget($args, $instance);
	}
}
