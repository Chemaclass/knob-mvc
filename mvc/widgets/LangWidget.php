<?php

namespace Widgets;

use I18n\I18n;

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
		$instance['languages'] = [ ];
		foreach ( I18n::getAllLangAvailable() as $l ) {
			$instance['languages'][] = [
				'key' => $l,
				'value' => I18n::getLangFullnameBrowser($l)
			];
		}

		/*
		 * Sort by key
		 */
		usort($instance['languages'], function ($a, $b) {
			return strcasecmp($a['key'], $b['key']);
		});

		/*
		 * And call the widget func from the parent class WidgetBase.
		 */
		parent::widget($args, $instance);
	}
}
