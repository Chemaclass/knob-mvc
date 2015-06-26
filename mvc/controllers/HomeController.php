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
	 * page.php
	 */
	public function getPage() {
		if (have_posts()) {
			the_post();
			$page = Post::find(get_the_ID());
		}
		if (!isset($page)) {
			return $this->getError();
		}
		return $this->renderPage('page', [ 
			'page' => $page 
		]);
	}
	
	/**
	 * post.php
	 */
	public function getPost() {
		if (have_posts()) {
			the_post();
			$post = Post::find(get_the_ID());
		}
		if (!isset($post)) {
			return $this->getError();
		}
		return $this->renderPage('post', [ 
			'post' => $post 
		]);
	}
}
