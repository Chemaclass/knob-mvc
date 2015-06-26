<?php

namespace Controllers;

use Models\Post;
use Models\User;

/**
 * Home Controller
 *
 * @author José María Valera Reales
 */
class HomeController extends BaseController {
	
	/**
	 * author.php
	 */
	public function getAuthor() {
		$author = get_queried_object();
		$user = User::find($author->ID);
		if (!$user) {
			return $this->getError();
		}
		$args = [ 
			'user' => $user 
		];
		return $this->renderPage('user', $args);
	}
	
	/**
	 * home.php
	 */
	public function getHome() {
		$args = [ 
			'posts' => self::getPosts(),
			'sidebar' => [ 
				'active' => true,
				'content' => [ 
					'pages' => 'all' 
				],
				'position' => 'right' 
			] 
		];
		return $this->renderPage('home', $args);
	}
	
	/**
	 * Error
	 */
	public function getError($code = 404, $message = 'Not found') {
		$args = [ 
			'error' => [ 
				'code' => $code,
				'message' => $message 
			] 
		];
		return $this->renderPage('error', $args);
	}
	
	/**
	 * single.php
	 */
	public function getSingle($type = 'post') {
		if (have_posts()) {
			the_post();
			$post = Post::find(get_the_ID());
		}
		if (!isset($post)) {
			return $this->getError();
		}
		return $this->renderPage($type, [ 
			$type => $post 
		]);
	}
}
