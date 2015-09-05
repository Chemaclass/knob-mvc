<?php

namespace Controllers;

use Libs\Ajax;
use I18n\I18n;
use Libs\KeysRequest;
use Models\Post;
use Models\Term;
use Models\Archive;

// Load WP.
// We have to require this file, in other case we cant call to the WP functions
require_once dirname(__FILE__) . '/../../../../../wp-load.php';

/**
 * AJAX Controller
 *
 * @author José María Valera Reales
 */
class AjaxController extends BaseController {

	/*
	 * Members
	 */
	public $err;
	public $withoutPermissions;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->err = I18n::transu('error');
		$this->withoutPermissions = I18n::transu('without_permissions');
	}

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
			'pages' => Post::getAllPages(),
			'tags' => Term::getTags()
		];
		$type = str_replace('-', '_', $type);
		$content = $this->render('menu/' . $type, $args);
		$json['content'] = $content;
		$json['code'] = KeysRequest::OK;
		return $json;
	}

	/**
	 *
	 * @param string $submit
	 */
	public function getJsonBySubmit($submit, $_datas) {
		switch ($submit) {
			case 'show-more' :
				return $this->jsonShowMore($_datas);
			case 'menu' :
				return $this->jsonMenu($_datas);
		}
	}

	/**
	 * -------------------------------------
	 * Main Controller for AJAX request
	 * -------------------------------------
	 */
	public function main() {
		$json = [
			'code' => 504
		]; // Error default

		$submit = $_REQUEST['submit'];
		$post_id = $_REQUEST['post'];

		// check if we don't have any submit
		if (!$submit) {
			die('');
		}

		$json = $this->getJsonBySubmit($submit, $_REQUEST);

		// cast the content to UTF-8
		$json['content'] = mb_convert_encoding($json['content'], "UTF-8");
		echo json_encode($json);
	}
}
$ajax = new AjaxController();
$ajax->main();
