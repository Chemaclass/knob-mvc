<?php

namespace Controllers;

use Widgets\LangWidget;
use Widgets\PagesWidget;

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
			new LangWidget(),
			new PagesWidget()
		];

		foreach ( $widgets as $w ) {
			$w->register();
		}
	}
}
