<?php

namespace Libs;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Mustache_Logger_StreamLogger;
use I18n\I18n;

/**
 * Template singleton
 *
 * @author José María Valera Reales
 */
class Template {

	/*
	 * Const
	 */
	const CACHE_FILE_MODE = 0660;
	const CACHE_LAMBDA_TEMPLATES = true;
	const CHARSET = 'UTF-8';
	const STRICT_CALLABLES = true;

	/*
	 * Widgets
	 */
	const SIDEBAR_RIGHT_TOP = 'sidebar_right_top';
	const SIDEBAR_RIGHT_BOTTOM = 'sidebar_right_bottom';
	const FOOTER_TOP = 'footer_top';
	const FOOTER_BOTTOM = 'footer_bottom';

	/*
	 * Singleton
	 */
	private static $instance = null;

	/*
	 * Members
	 */
	private $renderEngine = null;

	/**
	 * Constructor
	 */
	private function __construct() {
		$templatesFolder = static::getTemplatesFolderLocation();
		$this->renderEngine = new Mustache_Engine([
			'charset' => static::CHARSET,
			'strict_callables' => static::STRICT_CALLABLES,
			'cache_file_mode' => static::CACHE_FILE_MODE,
			'cache_lambda_templates' => static::CACHE_LAMBDA_TEMPLATES,
			'loader' => new Mustache_Loader_FilesystemLoader($templatesFolder),
			'partials_loader' => new Mustache_Loader_FilesystemLoader($templatesFolder),
			'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
			'helpers' => self::getHelpers(),
			'pragmas' => static::getPragmas(),
			'escape' => function ($value) {
				return htmlspecialchars($value, ENT_COMPAT, static::CHARSET);
			}
		]);
	}

	/**
	 *
	 * @return NULL
	 */
	public static function getInstance() {
		if (!static::$instance) {
			static::$instance = new Template();
		}
		return static::$instance;
	}

	/**
	 * Return the relative path location where are the templates.
	 *
	 * @return string
	 */
	private static function getTemplatesFolderLocation() {
		return str_replace('//', '/', dirname(__FILE__) . '/') . '../templates';
	}

	/**
	 * Return a list with the dinamic sidebar for widgets active
	 *
	 * @return array<string>
	 */
	public static function getDinamicSidebarActive() {
		return [
			Template::SIDEBAR_RIGHT_TOP,
			Template::SIDEBAR_RIGHT_BOTTOM,
			Template::FOOTER_TOP,
			Template::FOOTER_BOTTOM
		];
	}

	/**
	 *
	 * @return \Mustache_Engine
	 */
	public function getRenderEngine() {
		return $this->renderEngine;
	}

	/**
	 *
	 * @param \Mustache_Engine $renderEngine
	 * @return Template
	 */
	public function setRenderEngine($renderEngine) {
		$this->renderEngine = $renderEngine;
		return $this;
	}

	/**
	 *
	 * @return multitype:string
	 */
	private function getPragmas() {
		return [
			Mustache_Engine::PRAGMA_FILTERS,
			Mustache_Engine::PRAGMA_BLOCKS
		];
	}

	/**
	 *
	 * @return multitype:NULL multitype:NULL |string|boolean
	 */
	private function getHelpers() {
		return [
			'trans' => function ($value) {
				return I18n::trans($value);
			},
			'transu' => function ($value) {
				return I18n::transu($value);
			},
			'case' => [
				'lower' => function ($value) {
					return strtolower((string) $value);
				},
				'upper' => function ($value) {
					return strtoupper((string) $value);
				}
			],
			'count' => function ($value) {
				return count($value);
			},
			'moreThan1' => function ($value) {
				return count($value) > 1;
			},
			'date' => [
				'xmlschema' => function ($value) {
					return date('c', strtotime($value));
				},
				'string' => function ($value) {
					return date('l, d F Y', strtotime($value));
				},
				'format' => function ($value) {
					return date(get_option('date_format'), strtotime($value));
				}
			],
			'toArray' => function ($value) {
				return explode(',', $value);
			},
			'ucfirst' => function ($value) {
				return ucfirst($value);
			}
		];
	}
}