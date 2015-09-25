<?php

namespace Widgets;

use Models\Post;

/**
 *
 * @author José María Valera Reales
 *
 */
class PagesWidget extends WidgetBase {

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Widgets\WidgetBase::widget()
	 */
	public function widget($args, $instance) {

		/*
		 * Put the pages to show into the instance var.
		 */
		$instance['pages'] = Post::getAllPages($this->configParams['pages']);

		/*
		 * And call the widget func from the parent class WidgetBase.
		 */
		parent::widget($args, $instance);
	}
}
