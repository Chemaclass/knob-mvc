<?php

namespace Libs;

/**
 * Ajax class
 *
 * @author José María Valera Reales
 */
class Ajax {

	/*
	 * Some constant
	 */
	const ARCHIVE = 'archive';
	const HOME = 'home';
	const CATEGORY = 'category';
	const TAG = 'tag';
	const AUTHOR = 'author';
	const SEARCH = 'search';

	/**
	 *
	 * Check the nonce from the request from ajax
	 *
	 * @param string $nonce
	 *        	Key to compare
	 * @param string $typeOfNonce
	 *        	Tzpe of nonce created
	 * @param string $id
	 *        	Identifier
	 */
	public static function verifyNonce($nonce, $typeOfNonce, $id) {
		return wp_verify_nonce($nonce, $typeOfNonce . $id);
	}

	/**
	 * Envelop the array for the response to ajax
	 *
	 * @param integer $code
	 *        	Code error
	 * @param string $message
	 *        	Message explaining the error
	 * @param string $content
	 *        	Content result
	 * @return array
	 */
	public static function envelope($code = 0, $message = 'OK', $content = "") {
		return array (
			'code' => $code,
			'message' => (string) $message,
			'content' => $content
		);
	}

	/**
	 * Response OK
	 *
	 * @param array|string $content
	 *        	Content to send
	 */
	public static function responseOK($content = "") {
		return self::envelope(KeysRequest::OK, 'OK', $content);
	}

	/**
	 * Response with a generic Error
	 *
	 * @param string $message
	 *        	Message error
	 */
	public static function responseError($message = "") {
		return self::envelope(KeysRequest::NOT_CORRECT, $message, "");
	}

	/**
	 *
	 * Generic response from the server. We send a json.
	 *
	 * @param int $code
	 *        	Response code
	 * @param str $message
	 *        	Response message
	 * @param array $content
	 *        	Content to send
	 */
	public static function response($code, $message, $content = "") {
		return json_encode(self::envelope($code, $message, $content));
	}
}