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
	 * Devuelve el login
	 *
	 * @return string
	 */
	public function getUserLogin() {
		return stripslashes($this->user_login);
	}
	
	/**
	 * Devuelve el email
	 *
	 * @return string
	 */
	public function getUserEmail() {
		return stripslashes($this->user_email);
	}
	
	/**
	 * Devuelve el nombre público del User (display_name)
	 *
	 * @return string
	 */
	public function getDisplayName() {
		return stripslashes($this->display_name);
	}
	
	/**
	 * Devuelve el nombre del User
	 *
	 * @return string
	 */
	public function getFirstName() {
		return get_user_meta($this->ID, self::KEY_FIRST_NAME, true);
	}
	
	/**
	 * Devuelve os apellidos del User
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
	 * Devuelve la URL de la pantalla de edición del perfil del User
	 *
	 * @return string
	 */
	public function getEditUrl() {
		return admin_url('user-edit.php?user_id=' . $this->ID, 'http');
	}
	
	/**
	 * Devuelve la URL del User
	 *
	 * @return string
	 */
	public function getUserUrl() {
		return get_the_author_meta('user_url', $this->ID);
	}
	
	/**
	 * Devuelve la lista de roles para el usuario
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