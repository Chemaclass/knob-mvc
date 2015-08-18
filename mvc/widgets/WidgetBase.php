<?php

namespace Widgets;

use Libs\Template;

/**
 *
 * @author José María Valera Reales
 *
 */
abstract class WidgetBase extends \WP_Widget {

	/*
	 * Members
	 */
	protected $template;

	/**
	 *
	 * @param unknown $id
	 * @param unknown $title
	 * @param unknown $widgetOps
	 * @param unknown $controlOps
	 */
	public function __construct($id, $title, $widgetOps, $controlOps) {
		parent::__construct($id, $title, $widgetOps, $controlOps);
		$this->template = Template::getInstance();
	}

	/**
	 * getId
	 */
	public static function getId() {
		return get_called_class();
	}

	/**
	 * Register the widget
	 */
	public function register() {
		$id = static::getId();
		if (!is_active_widget($id)) {
			register_widget($id);
		}
	}
}
