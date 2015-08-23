<?php

namespace Widgets;

use Libs\Template;
use Config\Params;

/**
 *
 * @author José María Valera Reales
 *
 * @see https://codex.wordpress.org/es:API_de_Widget
 */
abstract class WidgetBase extends \WP_Widget {

	/*
	 * Some const.
	 */
	const PREFIX_TITLE = 'Knob ';
	const DIR_WIDGET_TEMPLATE = 'widget';
	const DIR_BACK = 'back';
	const DIR_FRONT = 'front';

	/*
	 * Members.
	 */
	protected $className, $classNameLower;
	protected $configParams;
	protected $template;

	/**
	 *
	 * @param string $id
	 * @param string $title
	 * @param array $widgetOps
	 * @param array $controlOps
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_widget/__construct/
	 */
	public function __construct($id = '', $title = '', array $widgetOps = [], array $controlOps = []) {
		$className = static::getId();
		$className = substr($className, strrpos($className, '\\') + 1);
		$this->className = substr($className, 0, strpos($className, 'Widget'));
		$this->classNameLower = strtolower($this->className);

		$id = ($id && strlen($id)) ? $id : $this->className . '_Widget';
		$title = ($title && strlen($title)) ? $title : self::PREFIX_TITLE . $this->className;
		$widgetOps = (count($widgetOps)) ? $widgetOps : [
			'classname' => strtolower($this->className) . '-widget',
			'description' => $this->className . ' widget'
		];
		parent::__construct($id, $title, $widgetOps, $controlOps);

		$this->template = Template::getInstance();

		$this->configParams = Params::getInstance()->getAll();
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
	 * Creating widget front-end
	 *
	 * @see https://codex.wordpress.org/es:API_de_Widget
	 */
	public function widget($args, $instance) {
		echo $this->renderFrontendWidget($args, $instance);
	}

	/**
	 * Widget Backend
	 *
	 * @param unknown $instance
	 *
	 * @see https://codex.wordpress.org/es:API_de_Widget
	 */
	public function form($instance) {
		$fields = [
			'title'
		];
		echo $this->renderBackForm($instance, $fields);
	}

	/**
	 * Updating widget replacing old instances with new
	 *
	 * @param unknown $newInstance
	 * @param unknown $oldInstance
	 * @return multitype:string
	 *
	 * @see https://codex.wordpress.org/es:API_de_Widget
	 */
	public function update($newInstance, $oldInstance) {
		$instance = array ();
		$instance['title'] = (!empty($newInstance['title'])) ? strip_tags($newInstance['title']) : '';
		return $instance;
	}

	/**
	 * Render backend form
	 *
	 * @param unknown $instance
	 */
	protected function renderBackForm($instance, array $fields) {
		$instance = array_merge($instance, $this->configParams['globalVars']);

		$fieldIds = [ ];
		$fieldNames = [ ];
		foreach ( $fields as $f ) {
			$fieldIds = array_merge($fieldIds, [
				$f => $this->get_field_id($f)
			]);
			$fieldNames = array_merge($fieldNames, [
				$f => $this->get_field_name($f)
			]);
		}
		return $this->template->getRenderEngine()->render($this->getTemplateName(self::DIR_BACK), [
			'instance' => array_merge($instance, [
				'fieldId' => $fieldIds,
				'fieldName' => $fieldNames
			])
		]);
	}

	/**
	 * Render fronted widget.
	 *
	 * @param unknown $args
	 * @param unknown $instance
	 */
	protected function renderFrontendWidget($args, $instance) {
		$instance = array_merge($instance, $this->configParams['globalVars']);

		return $this->template->getRenderEngine()->render($this->getTemplateName(self::DIR_FRONT), [
			'args' => $args,
			'instance' => $instance
		]);
	}

	/**
	 * Create the template name string
	 *
	 * @param string $dir
	 * @return string
	 */
	private function getTemplateName($dir) {
		return self::DIR_WIDGET_TEMPLATE . '/' . $this->classNameLower . '/' . $dir;
	}
}
