<?php

namespace Libs;

/**
 *
 * @author José María Valera Reales
 *
 */
class WalkerNavMenu extends \Walker_Nav_Menu {

	/**
	 *
	 * @param unknown $output
	 * @param unknown $depth
	 */
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent";

		if (strpos($output, 'menu-item-has-children')) {
			$output = preg_replace('/<a(.*)href="([^"]*)"(.*)>/', '<a$1
					href="#" class="dropdown-toggle" data-toggle="dropdown"
					role="button" aria-haspopup="true" aria-expanded="false"$3>', $output);
			$output = str_replace('</a>', '<span class="caret"></span></a>', $output, $count);
		}
		$output .= '<ul class="dropdown-menu">';
	}

	/**
	 *
	 * @param unknown $output
	 * @param unknown $depth
	 */
	function end_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>";
	}

	/**
	 * Start the element output.
	 *
	 * @param string $output
	 *        	Passed by reference. Used to append additional content.
	 * @param object $item
	 *        	Menu item data object.
	 * @param int $depth
	 *        	Depth of menu item. May be used for padding.
	 * @param array $args
	 *        	Additional strings.
	 */
	function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
		$classes = empty($item->classes) ? array () : (array) $item->classes;

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));

		!empty($class_names) and $class_names = ' class="' . esc_attr($class_names) . '"';

		$output .= "<li id='menu-item-$item->ID' $class_names>";

		$attributes = '';

		!empty($item->attr_title) and $attributes .= ' title="' . esc_attr($item->attr_title) . '"';
		!empty($item->target) and $attributes .= ' target="' . esc_attr($item->target) . '"';
		!empty($item->xfn) and $attributes .= ' rel="' . esc_attr($item->xfn) . '"';
		!empty($item->url) and $attributes .= ' href="' . esc_attr($item->url) . '"';

		// insert description for top level elements only you may change this
		$description = (!empty($item->description) and 0 == $depth) ? '<small class="nav_desc">' . esc_attr($item->description) . '</small>' : '';

		$title = apply_filters('the_title', $item->title, $item->ID);

		$item_output = $args->before . "<a $attributes>" . $args->link_before . $title . '</a> ' . $args->link_after . $description . $args->after;

		// Since $output is called by reference we don't need to return anything.
		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}
}