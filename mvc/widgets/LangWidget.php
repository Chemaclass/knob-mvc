<?php

namespace Widgets;

/**
 *
 * @author José María Valera Reales
 *
 */
class LangWidget extends WidgetBase {

	/**
	 * Constructor
	 */
	public function __construct() {
		$widgetOps = [
			'classname' => 'lang-widget',
			'description' => 'Language widget'
		];

		$controlOps = [ ];

		parent::__construct('Lang_Widget', 'Language Widget', $widgetOps, $controlOps);
	}

	/**
	 * Creating widget front-end
	 */
	public function widget($args, $instance) {

		echo $this->template->render('widget/_lang');
	}

	/**
	 * Widget Backend
	 *
	 * @param unknown $instance
	 */
	public function form($instance) {
		if (isset($instance['title'])) {
			$title = $instance['title'];
		} else {
			$title = 'Write the title';
		}

		// Widget admin form
		?>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
	<input class="widefat"
		id="<?php echo $this->get_field_id( 'title' ); ?>"
		name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
		value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php
		//echo $this->template->render('sidebar/_lang');
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
