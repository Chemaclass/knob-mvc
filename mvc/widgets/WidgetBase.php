<?php

namespace Widgets;

/**
 *
 * @author José María Valera Reales
 *
 */
abstract class WidgetBase extends \WP_Widget {

	/**
	 * getId
	 */
	public static function getId() {
		return get_called_class();
	}

	/**
	 * Register the widget
	 */
	public static function register() {
		$id = static::getId();
		if (!is_active_widget($id)) {
			register_widget($id);
		}
	}
}
