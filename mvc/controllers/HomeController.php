<?php

namespace Controllers;

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
			] 
		];
		return $this->renderPage('home', $args);
	}
}
