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
		WidgetController::setup();
	}
}