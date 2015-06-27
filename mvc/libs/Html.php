<?php

namespace Libs;

/**
 * Resources for HTML things
 *
 * @author José María Valera Reales
 */
class Html {

	/**
	 * Remove "more" tag
	 *
	 * @param string $str
	 * @return string
	 */
	public static function removeReadMoreTag($str) {
		return str_replace('<!--more-->', '', $str);
	}
}