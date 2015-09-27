<?php

namespace Widgets;

use Knob\I18n\I18n;

/**
 *
 * @author José María Valera Reales
 *
 */
class LangWidget extends WidgetBase {

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Widgets\WidgetBase::widget()
	 */
	public function widget($args, $instance) {

		/*
		 * Put all languages available to show into the instance var.
		 */
		$instance['languages'] = I18n::getAllLangAvailableKeyValue();

		/*
		 * And call the widget func from the parent class WidgetBase.
		 */
		parent::widget($args, $instance);
	}
}
