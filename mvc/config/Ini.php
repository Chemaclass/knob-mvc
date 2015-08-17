<?php

namespace Config;

use Controllers\WidgetController;

/**
 * Ini Class
 *
 * @author José María Valera Reales
 *
 */
class Ini {

	/**
	 * Setup
	 */
	public static function setup() {

		/*
		 * Widgets
		 */
		$widgetController = new WidgetController();
		$widgetController->setup();
	}
}