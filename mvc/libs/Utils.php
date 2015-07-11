<?php

namespace Libs;

use Models\User;

/**
 * Class with Utilities
 *
 * @author José María Valera Reales
 */
class Utils {
	const TYPE_TAG = 'tag';
	const TYPE_CATEGORY = 'category';
	const TYPE_SEARCH = 'search';
	const TYPE_AUTHOR = 'author';

	/**
	 * Check the value: not only spaces, with value and more than 0.
	 *
	 * @param string $value
	 *        	String to check.
	 * @return boolean true: valid, false: not valid.
	 */
	public static function isValidStr($value) {
		return (isset($value) && !ctype_space($value) && strlen($value) > 0);
	}

	/**
	 * Devuelve el ID del attachment apartir de su url
	 *
	 * @param string $attachmentUrl
	 *        	URL del attachment
	 * @return integer ID del attachment
	 */
	function getAttachmentIdFromUrl($attachmentUrl = '') {
		global $wpdb;
		$attachmentId = false;
		// If there is no url, return.
		if ('' == $attachmentUrl) {
			return;
		}
		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();
		// Make sure the upload path base directory exists in the attachment URL,
		// to verify that we're working with a media library image
		if (false !== strpos($attachmentUrl, $upload_dir_paths['baseurl'])) {
			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachmentUrl = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachmentUrl);
			// Remove the upload path base directory from the attachment URL
			$attachmentUrl = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachmentUrl);
			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachmentId = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID
					FROM {$wpdb->posts} wposts, {$wpdb->postmeta} wpostmeta
					WHERE wposts.ID = wpostmeta.post_id
					AND wpostmeta.meta_key = '_wp_attached_file'
					AND wpostmeta.meta_value = '%s'
					AND wposts.post_type = 'attachment'", $attachmentUrl));
		}
		return $attachmentId;
	}

	/**
	 * Return the current lang of the browerser
	 *
	 * @return string Just the first two chars. Ex: de, es, en, fr
	 */
	public static function getLangBrowser() {
		return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
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
		return PUBLIC_DIR . '/img/avatar/avatar_' . $size . '.png';
	}

	/**
	 * Return the ID from the tag name
	 *
	 * @param string $tagName
	 *        	Tag name
	 * @return number ID from the tag name
	 */
	public static function getTagIdbyName($tagName) {
		$tag = get_term_by('name', $tagName, 'post_tag');
		return ($tag) ? $tag->term_id : 0;
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