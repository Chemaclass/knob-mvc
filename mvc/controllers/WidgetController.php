<?php

namespace Controllers;

use Widgets\HelloWidget;

/**
 * Widget Controller
 *
 * @author José María Valera Reales
 */
class WidgetController extends BaseController {

	/**
	 * Setup
	 */
	public static function setup() {
		HelloWidget::register();

	}
}
