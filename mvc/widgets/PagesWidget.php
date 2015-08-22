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
	 * Creating widget front-end
	 */
	public function widget($args, $instance) {
		$instance['pages'] = Post::getAllPages($this->configParams['pages']);

		echo $this->template->getRenderEngine()->render('widget/pages/front', [
			'args' => $args,
			'instance' => $instance
		]);
	}

	/**
	 * Widget Backend
	 *
	 * @param unknown $instance
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
	 */
	public function update($newInstance, $oldInstance) {
		$instance = array ();
		$instance['title'] = (!empty($newInstance['title'])) ? strip_tags($newInstance['title']) : '';
		return $instance;
	}
}
