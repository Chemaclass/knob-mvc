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
	 * Some const.
	 */
	const PREFIX = 'Knob ';

	/*
	 * Members.
	 */
	protected $template;
	protected $className;

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
		$this->className = substr($className, 0, strpos($className, 'Widget'));

		$id = (strlen($id)) ? $id : $this->className . '_Widget';
		$title = (strlen($title)) ? $title : self::PREFIX . $this->className . ' Widget';
		$widgetOps = (count($widgetOps)) ? $widgetOps : [
			'classname' => strtolower($this->className) . '-widget',
			'description' => $this->className . ' widget'
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

	/**
	 * Render form
	 *
	 * @param unknown $instance
	 */
	protected function renderBackForm($instance, array $fields) {
		$fieldIds = [ ];
		$fieldNames = [ ];
		foreach ( $fields as $f ) {
			$fieldIds = array_merge($fieldIds, [
				$f => $this->get_field_id($f)
			]);
			$fieldNames = array_merge($fieldIds, [
				$f => $this->get_field_name($f)
			]);
		}
		return $this->template->getRenderEngine()->render('widget/' . strtolower($this->className) . '/back', [
			'instance' => array_merge($instance, [
				'fieldId' => $fieldIds,
				'fieldName' => $fieldNames
			])
		]);
	}
}
