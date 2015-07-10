<?php

namespace Controllers;

use Models\User;

/**
 * Backend Controller
 *
 * @author José María Valera Reales
 */
class BackendController extends BaseController {

	/**
	 * Return the view to change img from User
	 *
	 * @param integer $user_ID
	 */
	public function getRenderProfileImg($keyUserImg, $user_ID = false) {
		if (!$user_ID) {
			global $user_ID;
		}
		$user = User::find($user_ID);
		switch ($keyUserImg) {
			case User::KEY_AVATAR :
				$template = 'backend/user/_img_avatar';
				break;
		}
		return $this->render($template, [
			'user' => $user,
			'KEY_AVATAR' => User::KEY_AVATAR
		]);
	}
}