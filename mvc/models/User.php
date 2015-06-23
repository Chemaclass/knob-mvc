<?php

namespace Models;

use Libs\Utils;
use Controllers\HomeController;

/**
 * User Model
 *
 * @author José María Valera Reales
 */
class User extends Image {
	public static $table = "users";
	
	/*
	 * Avatar Size
	 */
	const AVATAR_SIZE_ICO = 26;
	const AVATAR_SIZE_SMALL = 64;
	const AVATAR_SIZE_DEFAULT = 96;
	const AVATAR_SIZE_PROFILE = 190;
	
	/*
	 * Const
	 */
	const KEY_FIRST_NAME = 'first_name';
	const KEY_LAST_NAME = 'last_name';
	const KEY_AVATAR = 'avatar';
	const KEY_LANGUAGE = 'language';
	
	/*
	 * Total constants
	 */
	const TOTAL_POSTS_TO_SHOW = 10;
	
	/**
	 * Return the URL with the avatar from the User
	 *
	 * @param integer $size        	
	 * @return string
	 */
	public function getAvatar($size = self::AVATAR_SIZE_DEFAULT) {
		$avatar = $this->getImage(self::KEY_AVATAR, $size, $size);
		if (empty($avatar)) {
			return Utils::getUrlAvatarDefault($size);
		}
		return $avatar;
	}
	
	/**
	 * Return the URL from the user avatar profile(190)
	 *
	 * @return string url
	 */
	public function getAvatarProfile() {
		return $this->getAvatar(self::AVATAR_SIZE_PROFILE);
	}
	
	/**
	 * Return the URL from the user avatar size ico(26)
	 *
	 * @return string url
	 */
	public function getAvatarIco() {
		return $this->getAvatar(self::AVATAR_SIZE_ICO);
	}
	
	/**
	 * Return the URL from the user avatar size small(64)
	 *
	 * @return string url
	 */
	public function getAvatarSmall() {
		return $this->getAvatar(self::AVATAR_SIZE_SMALL);
	}
	
	/**
	 * Establecer un nuevo avatar al User
	 *
	 * @param FILE $newAvatar        	
	 * @return boolean
	 */
	public function setAvatar($newAvatar = false) {
		return $this->setAvatar(self::KEY_AVATAR, $newAvatar);
	}
	
	/**
	 * Get all posts
	 *
	 * @param integer $max
	 *        	total posts to show
	 * @return array<Post>
	 */
	public function getPosts($max = self::TOTAL_POSTS_TO_SHOW) {
		$moreQuerySettings['author'] = $this->ID;
		return HomeController::getPosts(Post::TYPE_POST, $max, [ ], false, $moreQuerySettings);
	}
	
	/**
	 * Return the login user
	 *
	 * @return string
	 */
	public function getUserLogin() {
		return stripslashes($this->user_login);
	}
	
	/**
	 * Return the email
	 *
	 * @return string
	 */
	public function getUserEmail() {
		return stripslashes($this->user_email);
	}
	
	/**
	 * Return the public name
	 *
	 * @return string
	 */
	public function getDisplayName() {
		return stripslashes($this->display_name);
	}
	
	/**
	 * Return the first name
	 *
	 * @return string
	 */
	public function getFirstName() {
		return get_user_meta($this->ID, self::KEY_FIRST_NAME, true);
	}
	
	/**
	 * Return the last name
	 *
	 * @return string
	 */
	public function getLastName() {
		return get_user_meta($this->ID, self::KEY_LAST_NAME, true);
	}
	
	/**
	 * Devuelve el nombre y los apellidos del user.
	 * Si no tiene puesto ninguno, mostrará el alias.
	 *
	 * @return string
	 */
	public function getFullName() {
		$fullName = $this->getFirstName() . ' ' . $this->getLastName();
		if (strlen(trim($fullName))) {
			return $fullName;
		}
		return $this->getDisplayName();
	}
	
	/**
	 * Return the URL for to edit the User
	 *
	 * @return string
	 */
	public function getEditUrl() {
		return admin_url('user-edit.php?user_id=' . $this->ID, 'http');
	}
	
	/**
	 * Return the user URL
	 *
	 * @return string
	 */
	public function getUserUrl() {
		return get_the_author_meta('user_url', $this->ID);
	}
	
	/**
	 * Return the author posts url
	 */
	public function getPostsUrl() {
		return get_author_posts_url($this->ID);
	}
	
	/**
	 * Return all roles/capabilities
	 *
	 * @return array<string>
	 */
	public function getCapabilities() {
		$qRolesArr = get_user_meta($this->ID, 'wp_capabilities', true);
		return is_array($qRolesArr) ? array_keys($qRolesArr) : array (
			'non-user' 
		);
	}
}