<?php

namespace Libs;

use Models\User;

/**
 * Filters from Wordpress
 *
 * @author José María Valera Reales
 */
class Filters {

	/**
	 * Override the get_avatar by default from WP
	 *
	 * @return string
	 */
	public static function getAvatar() {
		/*
		 * We will get the avatar from our models
		 */
		add_filter('get_avatar', function ($avatar = '', $id_or_email, $size = User::AVATAR_SIZE_DEFAULT, $default = '', $alt = '') {
			if (is_numeric($id_or_email)) {
				$user_id = (int) $id_or_email;
			} elseif (is_string($id_or_email) && ($user = get_user_by('email', $id_or_email))) {
				$user_id = $user->ID;
			} elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
				$user_id = (int) $id_or_email->user_id;
			}
			$user = User::find($user_id);
			if (!$user) {
				return Utils::getUrlAvatarDefault($size);
			}
			if (!Utils::isValidStr($alt)) {
				$alt = $user->getDisplayName() . ' avatar';
			}
			$img = '<img alt="' . esc_attr($alt) . '" src="' . $user->getAvatar($size) . '" ';
			$img .= 'class="avatar photo" height="' . $size . '" width="' . $size . '">';
			return $img;
		}, 10, 5);
	}
}
