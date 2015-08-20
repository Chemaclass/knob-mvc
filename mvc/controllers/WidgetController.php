<?php

namespace Controllers;

use Widgets\HelloWidget;
use Widgets\LangWidget;
use Widgets\WidgetBase;

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
			new HelloWidget(),
			new LangWidget()
		];

		foreach ( $widgets as $w ) {
			$w->register();
		}
	}
}
