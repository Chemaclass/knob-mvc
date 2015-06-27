<?php

namespace Libs;

/**
 * Class for the enviroment
 *
 * @author José María Valera Reales
 */
class Env {

	/**
	 * Return true if we're in the production enviroment
	 *
	 * @return boolean
	 */
	public static function isProd() {
		$SERVER_NAME = $_SERVER['SERVER_NAME'];
		return ($SERVER_NAME == URL_PRO);
	}

	/**
	 * Return true if we're in the development enviroment
	 *
	 * @return boolean
	 */
	public static function isDev() {
		$SERVER_NAME = $_SERVER['SERVER_NAME'];
		return ($SERVER_NAME == URL_DEV);
	}

	/**
	 * Return true if we're in the local enviroment
	 *
	 * @return boolean
	 */
	public static function isLoc() {
		$SERVER_NAME = $_SERVER['SERVER_NAME'];
		return ($SERVER_NAME == URL_LOC);
	}
}