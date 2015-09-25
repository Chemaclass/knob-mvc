<?php

namespace Controllers;

use Widgets\ArchivesWidget;
use Widgets\CategoriesWidget;
use Widgets\LangWidget;
use Widgets\LoginWidget;
use Widgets\PagesWidget;
use Widgets\SearcherWidget;
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
			new LoginWidget(),
			new PagesWidget(),
			new SearcherWidget(),
			new TagsWidget()
		];

		foreach ( $widgets as $w ) {
			$w->register();
		}
	}
}
