<?php

namespace I18n;

use Libs\Utils;

/**
 *
 * Library who works with the i18n.
 *
 * @author José María Valera Reales
 */
class I18n {

	// English
	const LANG_EN = 'en';
	// Spanish
	const LANG_ES = 'es';
	// Default
	const LANG_DEFAULT = self::LANG_EN;
	// Current language
	const CURRENT_LANG = 'current-lang';

	/**
	 * Return a list with all languages availables
	 *
	 * @return array<string> names of directories availables
	 */
	public static function getAllLangAvailable() {
		return [
			self::LANG_ES,
			self::LANG_EN
		];
	}

	/**
	 * Return the lang by current user if it was established
	 *
	 * @return string|boolean Lang from the current user
	 */
	public static function getLangBrowserByCurrentUser($forceLang = false) {
		$langAvailables = self::getAllLangAvailable();

		$isLangAvailable = function ($langToCheck) use($langAvailables) {
			return in_array($langToCheck, $langAvailables);
		};

		// we can fonce the lang
		if ($forceLang && $isLangAvailable($forceLang)) {
			return $forceLang;
		}

		// or set by session
		if (session_start() && $_SESSION[self::CURRENT_LANG] && $isLangAvailable($_SESSION[self::CURRENT_LANG])) {
			return $_SESSION[self::CURRENT_LANG];
		}

		$langBrowser = Utils::getLangBrowser();
		return $isLangAvailable($langBrowser) ? $langBrowser : self::LANG_DEFAULT;
	}

	/**
	 * Return the fullname of the lang by current user.
	 *
	 * @return string|boolean Lang from the current user
	 */
	public static function getLangFullnameBrowserByCurrentUser($forceLang = false) {
		$lang = static::getLangBrowserByCurrentUser($forceLang);
		switch ($lang) {
			case self::LANG_ES :
				return I18n::trans('spanish');
			case self::LANG_EN :
			default :
				return I18n::trans('english');
		}
	}

	/**
	 * Return the translate word by key
	 *
	 * @param string $key
	 *        	Key from the file
	 * @return string Value translated from the key
	 */
	public static function trans($toTranslate, $params = [], $forceLang = false) {
		$toTranslate = strtolower($toTranslate);
		static::_getParams($toTranslate, $params);

		$dir = self::getLangBrowserByCurrentUser($forceLang);

		list($file, $key) = explode('.', $toTranslate);
		/*
		 * Get the file called 'global' by default. Only if we didn't specify any file
		 */
		if (is_null($key)) {
			$key = $file;
			$file = "global";
		}
		// List with all keys/values with current lang
		$langArray = self::getLangFile($file, $dir);
		$key = trim($key);
		$value = isset($langArray[$key]) ? $langArray[$key] : $key;
		if (is_numeric(strpos($value, ':')) && !empty($params) && is_array($params)) {
			$value = static::_setParams($value, $params);
		}
		return $value;
	}

	/**
	 * Return the associative array (the file language)
	 *
	 * @param string $lang
	 *        	The 2 first chars of the language
	 * @param string $file
	 *        	Filename language
	 */
	public static function getLangFile($file, $lang = false) {
		if (!$lang) {
			$lang = static::getLangBrowserByCurrentUser();
			if (!$lang) {
				$lang = Utils::getLangBrowser();
			}
		}
		return require (dirname(__FILE__) . "/$lang/$file.php");
	}

	/**
	 * Set the params into the string
	 *
	 * @param string $value
	 *        	Input
	 * @param arrsy $params
	 *        	List of params
	 */
	private static function _setParams($value, $params) {
		$strFinal = $value;
		$key = '';
		for($i = 0; $i < strlen($strFinal); $i++) {
			if ($strFinal[$i] == ':') { // 1º
				$_a = $i + 1;
				for($j = $_a; $j < strlen($strFinal); $j++) {
					$esUltimo = ($j == strlen($strFinal) - 1);
					if (in_array($strFinal[$j], [
						' ',
						',',
						'\\',
						'\'',
						'"'
					]) || $esUltimo) { // 2º
						$_b = $j;
						$_b = ($esUltimo) ? $_b + 1 : $_b;
						$key = substr($strFinal, $_a, $_b - $_a);
						$i = $_b;
						break;
					}
				}

				// We found the key
				if (isset($params[$key])) {
					$langKey = $params[$key];
					$strFinalA = substr($strFinal, 0, $_a - 1);
					$strFinalB = substr($strFinal, $_b);
					$strFinal = $strFinalA . $langKey . $strFinalB;
				}
			}
		}
		return $strFinal;
	}

	/**
	 * Format, if necessary, and translate the text with his parameters.
	 * Put into &$params (2nd parameter) all possible parameters from the text $toTranslate.
	 *
	 * @param string $toTranslate
	 *        	Text to translate their parameters as "JSON".
	 *        	That array are identified as being in square brackets '[]'
	 *        	and each key / value pairs are separated by ':' and each element of a ','
	 * @param array $params
	 */
	private static function _getParams(&$toTranslate, &$params) {
		/*
		 * ( If the params-array is empty or it's an object)
		 * And ( If the toTranslate-string contain '[' where would be the parameters )
		 */
		if (((is_array($params) && empty($params)) || is_object($params)) && ($pos = strpos($toTranslate, '['))) {
			$params = [ ];
			$_params = $params;
			// +1 and -1 It's for to remove the brackets '[]'
			$strParams = substr($toTranslate, $pos + 1, strlen($strParams) - 1);
			$toTranslate = substr($toTranslate, 0, $pos);
			// Split by a comma the parameters
			$_params = explode(',', $strParams);
			foreach ( $_params as $value ) {
				list($k, $v) = explode(':', $value);
				$params[$k] = $v;
			}
		}
	}

	/**
	 * /**
	 * Return the translated word with the first letter in uppercase.
	 *
	 * @param string $key
	 *        	Language key file.
	 * @param array $params
	 *        	optional parameters.
	 * @param string $forceLang
	 *        	optional lang to force.
	 * @return string Value translated.
	 *
	 */
	public static function transu($key, $params = [], $forceLang = false) {
		return ucfirst(self::trans($key, $params, $forceLang));
	}

	/**
	 * Cut a string
	 *
	 * @param string $key
	 * @param array $params
	 * @param string $forceLang
	 */
	public static function substr($key, $params = [], $forceLang = false) {
		list($string, $len) = explode(' ', $key);
		if ($len)
			return substr($string, 0, $len);
		else
			return $string;
	}
}