<?php

namespace Models;

/**
 * User Model
 *
 * @author José María Valera Reales
 */
class User extends ModelBase {
	public static $table = "users";
	
	/*
	 * Const
	 */
	const KEY_FIRST_NAME = 'first_name';
	const KEY_LAST_NAME = 'last_name';
	
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
	 * Return the public URL
	 *
	 * @return string
	 */
	public function getUserUrl() {
		return get_the_author_meta('user_url', $this->ID);
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