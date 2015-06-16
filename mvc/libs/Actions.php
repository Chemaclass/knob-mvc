<?php

namespace Libs;

/**
 * Actions for Wordpress
 *
 * @author José María Valera Reales
 */
class Actions {
	
	/**
	 * Put scripts into the admin view
	 */
	public static function adminPrintScripts() {
		add_action('admin_print_scripts', function () {
			wp_enqueue_script('jquery-plugin', COMPONENTS_DIR . '/jquery/jquery.min.js');
			wp_enqueue_script('bootstrap-plugin', COMPONENTS_DIR . '/bootstrap/js/bootstrap.min.js');
			wp_enqueue_script('main', PUBLIC_DIR . '/js/main.js');
		});
	}
	
	/**
	 * Put styles into the admin view
	 */
	public static function adminPrintStyles() {
		add_action('admin_print_styles', function () {
			wp_enqueue_style('bootstrap', COMPONENTS_DIR . '/bootstrap/css/bootstrap.css');
			wp_enqueue_style('font-awesome', COMPONENTS_DIR . '/font-awesome/css/font-awesome.min.css');
			wp_enqueue_style('main', PUBLIC_DIR . '/css/main.css');
		});
	}
	
	/**
	 * Delete the WP logo from the admin bar
	 */
	public static function wpBeforeAdminBarRender() {
		add_action('wp_before_admin_bar_render', function () {
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu('wp-logo');
		});
	}
}
