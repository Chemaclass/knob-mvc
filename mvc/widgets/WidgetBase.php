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
	 * @param string $id
	 * @param string $title
	 * @param array $widgetOps
	 * @param array $controlOps
	 */
	public function __construct($id = '', $title = '', $widgetOps = [], $controlOps = []) {
		$className = get_called_class();
		$className = substr($className, strrpos($className, '\\') + 1);
		$className = substr($className, 0, strpos($className, 'Widget'));

		$id = (strlen($id)) ? $id : $className . '_Widget';
		$title = (strlen($title)) ? $title : $className . ' Widget';
		$widgetOps = (count($widgetOps)) ? $widgetOps : [
			'classname' => strtolower($className) . '-widget',
			'description' => $className . ' widget'
		];
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
