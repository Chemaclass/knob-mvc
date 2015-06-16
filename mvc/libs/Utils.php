<?php

namespace Libs;

use Models\User;

/**
 * Class with Utilities
 *
 * @author José María Valera Reales
 */
class Utils {
	
	/**
	 * Return the current lang of the browerser
	 *
	 * @return string Just the first two chars. Ex: de, es, en, fr
	 */
	public static function getLangBrowser() {
		return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	}
	
	/**
	 * Return the instance of the current user, or null if they're not logged
	 *
	 * @return User
	 */
	public static function getCurrentUser() {
		$user = wp_get_current_user();
		if ($user->ID) {
			return User::find($user->ID);
		}
		return null;
	}
	
	/**
	 * Return $_SERVER[ REQUEST_URI ]
	 *
	 * @return string
	 */
	public static function getRequestUri() {
		return $_SERVER[REQUEST_URI];
	}
}