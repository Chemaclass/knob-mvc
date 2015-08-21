<?php

namespace Controllers;

use Widgets\LangWidget;

/**
 * Widget Controller
 *
 * @author José María Valera Reales
 */
class WidgetController extends BaseController {

	/**
	 * Setup
	 */
	public function setup() {
		$widgets = [
			new LangWidget()
		];

		foreach ( $widgets as $w ) {
			$w->register();
		}
	}
}
