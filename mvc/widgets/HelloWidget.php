<?php

namespace Widgets;

/**
 *
 * @author José María Valera Reales
 *
 */
class HelloWidget extends WidgetBase {

	//
	public function __construct() {
		$widgetOps = [
			'classname' => 'hello-widget',
			'description' => 'Hello widget description'
		];
		$controlOps = [ ];

		parent::__construct('Hello_Widget', 'Hello Widget', $widget_ops, $control_ops);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget($args, $instance) {
		$title = apply_filters('widget_title', $instance['title']);
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if (!empty($title))
			echo $args['before_title'] . $title . $args['after_title'];

			// This is where you run the code and display the output
		echo 'Hello widget! :D';
		echo $args['after_widget'];
	}

	// Widget Backend
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
	}

	// Updating widget replacing old instances with new
	public function update($new_instance, $old_instance) {
		$instance = array ();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		return $instance;
	}
}
