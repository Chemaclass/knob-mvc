<?php

namespace Models;

use Libs\Utils;
use Controllers\HomeController;
use I18n\I18n;

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
	const KEY_AVATAR = 'img_avatar';
	const KEY_HEADER = 'img_header';
	const KEY_LANGUAGE = 'language';
	const KEY_TWITTER = 'twitter';
	const KEY_TWITTER_URL = 'twitter_url';
	const KEY_FACEBOOK = 'facebook';
	const KEY_FACEBOOK_URL = 'facebook_url';
	const KEY_GOOGLE_PLUS = 'google_plus';
	const KEY_GOOGLE_PLUS_URL = 'google_plus_url';
	const KEY_TYPE = 'user_type';

	/*
	 * Possible roles
	 */
	const ROL_ADMIN = 'administrator';
	const ROL_EDITOR = 'editor';
	const ROL_AUTHOR = 'author';
	const ROL_CONTRIBUTOR = 'contributor';
	const ROL_SUBSCRIBER = 'subscriber';

	/*
	 * Possible kind of users
	 */
	const TYPE_AUTHOR = 'author';
	const TYPE_USER = 'user';
	const TYPE_DEFAULT = self::TYPE_USER;

	/*
	 * Header sizes
	 */
	const HEADER_WIDTH = 1200;
	const HEADER_HEIGHT = 270;

	/*
	 * Sidebar
	 */
	const WITH_SIDEBAR_DEFAULT = true;

	/**
	 * Return all valid user types
	 *
	 * @return arraz<string>
	 */
	public static function getValidTypes() {
		return [
			self::TYPE_AUTHOR,
			self::TYPE_USER
		];
	}

	/**
	 * Return the user type
	 */
	public function getType() {
		$userType = get_user_meta($this->ID, self::KEY_TYPE, true);
		return ($userType) ? $userType : self::TYPE_DEFAULT;
	}

	/**
	 *
	 * @param string $type
	 * @return boolean
	 */
	private function isType($type) {
		return ($this->getType() == $type);
	}

	/**
	 *
	 * @return boolean
	 */
	public function isTypeAuthor() {
		return $this->isType(self::TYPE_AUTHOR);
	}

	/**
	 *
	 * @return boolean
	 */
	public function isTypeUser() {
		return $this->isType(self::TYPE_USER);
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Models\Image::getImageSizesToDelete()
	 */
	protected function getImageSizesToDelete() {
		return $this->getSizesAvatar();
	}

	/**
	 * Return true if the user is allowed as admin
	 *
	 * @param array $args
	 * @return boolean
	 */
	public function canAdmin($args = []) {
		$args[] = self::ROL_ADMIN;
		return array_intersect($args, self::getRoles());
	}

	/**
	 * Return true if the user is allowed as editor
	 *
	 * @param array $args
	 * @return boolean
	 */
	public function canEditor($args = []) {
		$args[] = self::ROL_EDITOR;
		return $this->canAdmin($args);
	}

	/**
	 * Return true if the user is allowed as author
	 *
	 * @param array $args
	 * @return boolean
	 */
	public function canAutor($args = []) {
		$args[] = self::ROL_AUTHOR;
		return $this->canEditor($args);
	}

	/**
	 * Return true if the user is allowed as author
	 *
	 * @param array $args
	 * @return boolean
	 */
	public function canColaborador($args = []) {
		$args[] = self::ROL_CONTRIBUTOR;
		return $this->canAutor($args);
	}

	/**
	 * Return true if the user is allowed as subscriber
	 *
	 * @param array $args
	 * @return boolean
	 */
	public function canSuscriptor($args = []) {
		$args[] = self::ROL_SUBSCRIBER;
		return $this->canColaborador($args);
	}

	/**
	 * Return true if is the currentUser
	 *
	 * @return boolean
	 */
	public function isCurrentUser() {
		return ($this->ID == wp_get_current_user()->ID);
	}

	/**
	 * Return true if is the currentUser or admin
	 *
	 * @return boolean
	 */
	public function isCurrentUserOrAdmin() {
		return $this->isCurrentUser() || (wp_get_current_user()->roles[0] == self::ROL_ADMIN);
	}

	/**
	 * Get first Rol
	 *
	 * @return string
	 */
	public function getFirstRol() {
		$Roles = self::getRoles();
		return $Roles[0];
	}

	/**
	 * Devuelve verdadero en caso de tener el rol de Admin
	 *
	 * @return boolean
	 */
	public function isAdmin() {
		return in_array(self::ROL_ADMIN, self::getRoles());
	}

	/**
	 * Devuelve verdadero en caso de tener el rol de Editor
	 *
	 * @return boolean
	 */
	public function isEditor() {
		return in_array(self::ROL_EDITOR, self::getRoles());
	}

	/**
	 * Devuelve verdadero en caso de tener el rol de Autor
	 *
	 * @return boolean
	 */
	public function isAutor() {
		return in_array(self::ROL_AUTHOR, self::getRoles());
	}

	/**
	 * Devuelve verdadero en caso de tener el rol de colaborador
	 *
	 * @return boolean
	 */
	public function isColaborador() {
		return in_array(self::ROL_CONTRIBUTOR, self::getRoles());
	}

	/**
	 * Devuelve verdadero en caso de tener el rol de suscriptor
	 *
	 * @return boolean
	 */
	public function isSuscriptor() {
		return in_array(self::ROL_SUBSCRIBER, self::getRoles());
	}

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
	 * Return a list with the possible sizes for the avatar
	 *
	 * @return array<integer>
	 */
	public function getSizesAvatar() {
		return [
			self::AVATAR_SIZE_ICO,
			self::AVATAR_SIZE_SMALL,
			self::AVATAR_SIZE_DEFAULT,
			self::AVATAR_SIZE_PROFILE
		];
	}

	/**
	 * Set the new avatar
	 *
	 * @param file $newAvatar
	 * @return boolean
	 */
	public function setAvatar($newAvatar = false) {
		return $this->setImage(self::KEY_AVATAR, $newAvatar);
	}

	/**
	 * Return the path from his header
	 *
	 * @return string
	 */
	public function getHeader() {
		return $this->getImage(self::KEY_HEADER, self::HEADER_WIDTH, self::HEADER_HEIGHT);
	}

	/**
	 * Set the new header
	 *
	 * @param file $newHeader
	 * @return boolean
	 */
	public function setHeader($newHeader = false) {
		return $this->setImage(self::KEY_HEADER, $newHeader);
	}

	/**
	 * Return all comment
	 *
	 * @return array<Comment>
	 */
	public function getComments($postId = false) {
		$cArgs = [
			'author__in' => $this->ID,
			'orderby' => 'comment_date_gmt'
		];
		if ($postId) {
			$cArgs['post_id'] = $postId;
		}
		$comments = [ ];
		foreach ( get_comments($cArgs) as $c ) {
			$comments[] = Comment::find($c->comment_ID);
		}
		return $comments;
	}

	/**
	 * Get all posts
	 *
	 * @param integer $max
	 *        	total posts to show
	 * @return array<Post>
	 */
	public function getPosts($limit = false, $offset = false) {
		if (!$limit) {
			$limit = get_option('posts_per_page');
		}
		return Post::getByAuthor($this->ID, $limit, $offset);
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
	 * Return the name and surname from the User.
	 *
	 * If doesn't exists we just return his alias.
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
	 * Return Twitter
	 *
	 * @return string
	 */
	public function getTwitter() {
		return get_user_meta($this->ID, self::KEY_TWITTER, true);
	}

	/**
	 * Return Twitter
	 *
	 * @return string
	 */
	public function getTwitterUrl() {
		return get_user_meta($this->ID, self::KEY_TWITTER_URL, true);
	}

	/**
	 * Set new Twitter
	 *
	 * @param string $value
	 *        	Can be the nickname or the absolute url
	 */
	public function setTwitter($value) {
		$nickname = $url = '';
		if (strlen($value)) {
			if (strpos($value, 'http') !== false) {
				$url = $value;
				$nickname = '@' . substr($value, strrpos($value, '/') + 1);
			} else {
				$nickname = $value;
				if (strpos($value, '@') !== false) {
					$value = substr($value, 1);
				} else {
					$nickname = '@' . $value;
				}
				$url = 'https://twitter.com/' . $value;
			}
		}
		update_user_meta($this->ID, User::KEY_TWITTER, $nickname);
		update_user_meta($this->ID, User::KEY_TWITTER_URL, $url);
	}

	/**
	 * Return Facebook
	 *
	 * @return string
	 */
	public function getFacebook() {
		return get_user_meta($this->ID, self::KEY_FACEBOOK, true);
	}

	/**
	 * Return Facebook url
	 *
	 * @return string
	 */
	public function getFacebookUrl() {
		return get_user_meta($this->ID, self::KEY_FACEBOOK_URL, true);
	}

	/**
	 * Set new Facebook
	 *
	 * @param string $value
	 *        	Can be the nickname or the absolute url
	 */
	public function setFacebook($value) {
		$nickname = $url = '';
		if (strlen($value)) {
			if (strpos($value, 'http') !== false) {
				$url = $value;
				$nickname = substr($value, strrpos($value, '/') + 1);
			} else {
				$nickname = $value;
				$url = 'https://facebook.com/' . $value;
			}
		}
		update_user_meta($this->ID, User::KEY_FACEBOOK, $nickname);
		update_user_meta($this->ID, User::KEY_FACEBOOK_URL, $url);
	}

	/**
	 * Return Google+
	 *
	 * @return string
	 */
	public function getGooglePlus() {
		return get_user_meta($this->ID, self::KEY_GOOGLE_PLUS, true);
	}

	/**
	 * Return Google+ Url
	 *
	 * @return string
	 */
	public function getGooglePlusUrl() {
		return get_user_meta($this->ID, self::KEY_GOOGLE_PLUS_URL, true);
	}

	/**
	 * Set new Twitter
	 *
	 * @param string $value
	 *        	Can be the nickname or the absolute url
	 */
	public function setGooglePlus($value) {
		$nickname = $url = '';
		if (strlen($value)) {
			if (strpos($value, 'http') !== false) {
				$url = $value;
				$nickname = substr($value, strrpos($value, '/') + 1);
			} else {
				$nickname = $value;
				if (strpos($value, '+') !== false) {
					$nickname = substr($value, 1);
				} else {
					$value = '+' . $value;
				}
				$url = 'https://plus.google.com/' . $value;
			}
		}
		update_user_meta($this->ID, User::KEY_GOOGLE_PLUS, $nickname);
		update_user_meta($this->ID, User::KEY_GOOGLE_PLUS_URL, $url);
	}

	/**
	 * Return all roles/Roles
	 *
	 * @return array<string>
	 */
	public function getRoles() {
		$qRolesArr = get_user_meta($this->ID, 'wp_capabilities', true);
		return is_array($qRolesArr) ? array_keys($qRolesArr) : array (
			'non-user'
		);
	}

	/**
	 * Set a rol to User
	 *
	 * @param string $rol
	 */
	public function setRol($rol) {
		if (in_array($rol, self::getAllowedRoles())) {
			$u = new \WP_User($this->ID);
			$u->set_role($rol);
			return true;
		}
		return false;
	}

	/**
	 * Return the instance of the current user, or null if they're not logged
	 *
	 * @return User
	 */
	public static function getCurrent() {
		$user = wp_get_current_user();
		if ($user->ID) {
			return User::find($user->ID);
		}
		return null;
	}

	/**
	 * Return true if the User can see the sidebar.
	 *
	 * @return boolean
	 */
	public function isWithSidebar() {
		return self::WITH_SIDEBAR_DEFAULT;
	}

	/**
	 * Return the description
	 *
	 * @return string
	 */
	public function getDescription() {
		$string = get_the_author_meta('description', $this->ID);
		if (strlen($string)) {
			return $string;
		}
		return false;
	}

	/**
	 * Return language
	 *
	 * @return string
	 */
	public function getLang() {
		return get_user_meta($this->ID, self::KEY_LANGUAGE, true);
	}

	/**
	 * Set new lang
	 *
	 * @param string $value
	 */
	public function setLang($value) {
		update_user_meta($this->ID, User::KEY_LANGUAGE, $value);
	}

	/**
	 * Return the current lang full and translated from the user
	 *
	 * @return string
	 */
	public function getLangTransFull() {
		$langToTrans = ($currentLang = $this->getLang()) ? $currentLang : Utils::getLangBrowser();
		return I18n::transu('lang_' . $langToTrans);
	}

	/**
	 * Return the total of publish posts
	 *
	 * @return integer
	 */
	public function getTotalPosts() {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare('SELECT COUNT(*)
				FROM wp_posts
				WHERE post_author = %d
				AND post_type = %s
				AND post_status = %s', $this->ID, Post::TYPE_POST, Post::STATUS_PUBLISH));
	}
}