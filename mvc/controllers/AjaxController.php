<?php

namespace Controllers;

use Libs\Ajax;
use I18n\I18n;
use Libs\KeysRequest;

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
	public $without_permissions;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->err = I18n::transu('error');
		$this->without_permissions = I18n::transu('without_permissions');
	}

	/**
	 * Listen the home petition
	 *
	 * @param array $_datas
	 * @return array JSON
	 */
	private function jsonHome($_datas) {
		switch ($_datas['type']) {
			case 'show-more' :
				$limit = $_datas['limit'];
				$offset = $_datas['offset'];
				$posts = HomeController::getPosts($limit, $offset);
				$content = $this->render('home/_all_posts', [
					'posts' => $posts,
					'postWith' => HomeController::getPostWithDefault()
				]);
				$json['content'] = $content;
				$json['code'] = KeysRequest::OK;
				break;
		}
		return $json;
	}

	/**
	 *
	 * @param string $submit
	 */
	public function getJsonBySubmit($submit, $_datas) {
		switch ($submit) {
			case Ajax::HOME :
				return $this->jsonHome($_datas);
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