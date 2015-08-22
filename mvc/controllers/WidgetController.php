<?php

namespace Controllers;

use Widgets\LangWidget;
use Widgets\PagesWidget;
use Widgets\ArchivesWidget;

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
			new ArchivesWidget(),
			new LangWidget(),
			new PagesWidget()
		];

		foreach ( $widgets as $w ) {
			$w->register();
		}
	}
}
