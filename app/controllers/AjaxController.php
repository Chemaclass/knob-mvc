<?php

namespace Controllers;

use Knob\Controllers\AjaxController as KnobAjaxController;
use Knob\Libs\Ajax;
use Knob\I18n\I18n;
use Knob\Libs\KeysRequest;
use Knob\Models\Post;
use Knob\Models\Term;
use Knob\Models\Archive;

/**
 * AJAX Controller
 *
 * @author José María Valera Reales
 */
class AjaxController extends KnobAjaxController {

	/**
	 * Listen the home petition
	 *
	 * @param array $_datas
	 * @return array JSON
	 */
	private function jsonShowMore($_datas) {
		$postsWhereKey = $_datas['postsWhereKey'];
		$postsWhereValue = $_datas['postsWhereValue'];
		$limit = $_datas['limit'];
		$offset = $_datas['offset'];

		$getPostsBy = Post::getFuncBy($postsWhereKey);
		$posts = $getPostsBy($postsWhereValue, $limit, $offset);

		$content = $this->render('home/_all_posts', [
			'posts' => $posts
		]);
		$json['limit'] = count($posts);
		$json['content'] = $content;
		$json['code'] = KeysRequest::OK;

		return $json;
	}

	/**
	 * Response the menu
	 *
	 * @param array $_datas
	 * @return array JSON
	 */
	private function jsonMenu($_datas) {
		$type = $_datas['type'];
		$args = [
			'archives' => Archive::getMonthly(),
			'categories' => Term::getCategories(),
			'languages' => I18n::getAllLangAvailableKeyValue(),
			'pages' => Post::getPages(),
			'tags' => Term::getTags()
		];
		$type = str_replace('-', '_', $type);
		$content = $this->render('menu/' . $type . '_default', $args);
		$json['content'] = $content;
		$json['code'] = KeysRequest::OK;
		return $json;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Knob\Controllers\AjaxController::getJsonBySubmit()
	 */
	public function getJsonBySubmit($submit, $_datas) {
		switch ($submit) {
			case 'show-more' :
				return $this->jsonShowMore($_datas);
			case 'menu' :
				return $this->jsonMenu($_datas);
		}
	}
}

$ajax = new AjaxController();
$ajax->main();
