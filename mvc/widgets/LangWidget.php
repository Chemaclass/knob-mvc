<?php

namespace Widgets;

/**
 *
 * @author José María Valera Reales
 *
 */
class LangWidget extends WidgetBase {

	/**
	 * Creating widget front-end
	 */
	public function widget($args, $instance) {
		echo $this->template->getRenderEngine()->render('widget/lang/front', [
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
		echo $this->template->getRenderEngine()->render('widget/lang/back', [
			'instance' => array_merge($instance, [
				'fieldId' => [
					'title' => $this->get_field_id('title')
				],
				'fieldName' => [
					'title' => $this->get_field_name('title')
				]
			])
		]);
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
