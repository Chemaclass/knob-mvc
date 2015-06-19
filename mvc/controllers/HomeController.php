<?php

namespace Controllers;

use Models\Post;

/**
 * Home Controller
 *
 * @author José María Valera Reales
 */
class HomeController extends BaseController {
	
	/**
	 * home.php
	 */
	public function getHome() {
		$args = [ 
			'project' => [ 
				'name' => 'Knob',
				'description' => 'Knob is one PHP MVC Framework for Templates for Wordpress' 
			],
			'author' => [ 
				'name' => 'José María Valera Reales' 
			],
			'posts' => self::getPosts() 
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
