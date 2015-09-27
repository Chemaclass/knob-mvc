<?php

namespace Config;

use I18n\I18n;
use Models\Post;
use Libs\Env;
use Models\User;
use Knob\Config\Params as KnobParams;

/**
 * Singleton Params class
 *
 * @author José María Valera Reales
 *
 */
class Params extends KnobParams {

	/**
	 */
	protected function mountAll() {
		$this->allParams = array_merge(parent::getAll(), [

			/*
			 * ====================================
			 * Params to pages
			 * ====================================
			 */
			'pages' => [
				'excludeSlugs' => [
					'ajax',
					'lang',
					'random'
				]
			]
		]);

		$this->allParams['globalVars'] = array_merge($this->allParams['globalVars'], [
			'blogAuthor' => 'Chemaclass'
		]);
	}
}