<?php

namespace Config;

class Params {

	/*
	 * Singleton
	 */
	private static $instance = null;

	/**
	 *
	 * @return Params
	 */
	public static function getInstance() {
		if (!static::$instance) {
			static::$instance = new Params();
		}
		return static::$instance;
	}

	/**
	 *
	 * @return array
	 */
	public function all() {
		return [

			/*
			 * ====================================
			 * Params to pages
			 * ====================================
			 */
			'pages' => [
				'excludeSlugs' => [
					'lang',
					'random'
				]
			]
		];
	}
}