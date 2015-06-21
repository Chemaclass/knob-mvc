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
	
	/**
	 *
	 * @param integer $size        	
	 * @return string URL with the img by default for users
	 */
	public static function getUrlAvatarDefault($size = User::AVATAR_SIZE_DEFAULT) {
		return PUBLIC_DIR . '/img/avatar/avatar_' . $size;
	}
	
	/**
	 *
	 * @param unknown $str        	
	 * @param number $cant        	
	 * @param string $separator        	
	 * @return string
	 */
	public static function getWordsByStr($str, $cant = 8, $separator = ' ') {
		// Generate an arraz from the str cut by the separator
		$words = explode($separator, $str, $cant + 1);
		$numWords = count($words);
		// remove all empty values
		$filteredWords = array_filter($words, 'strlen');
		$numWordsFiltradas = count($filteredWords);
		// if they're a different number of words that mean something was filtered
		if ($numWordsFiltradas != $numWords) {
			$cant -= ($numWords - $numWordsFiltradas);
			$words = $filteredWords;
		}
		// if the content it's longer than the excerpt put '...'
		if (count($words) > $cant) {
			array_pop($words);
			$words[] = '...';
		}
		return implode($separator, $words);
	}
}