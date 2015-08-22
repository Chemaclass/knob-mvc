<?php

namespace Controllers;

use Widgets\LangWidget;
use Widgets\PagesWidget;
use Widgets\ArchivesWidget;
use Widgets\CategoriesWidget;
use Widgets\TagsWidget;

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
			new CategoriesWidget(),
			new LangWidget(),
			new PagesWidget(),
			new TagsWidget()
		];

		foreach ( $widgets as $w ) {
			$w->register();
		}
	}
}
