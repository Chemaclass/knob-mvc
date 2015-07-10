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
		$args = [
			'user' => $user
		];
		switch ($keyUserImg) {
			case User::KEY_AVATAR :
				$template = 'backend/user/_img_avatar';
				$args['KEY_AVATAR'] = User::KEY_AVATAR;
				break;
			case User::KEY_HEADER :
				$template = 'backend/user/_img_header';
				$args['KEY_HEADER'] = User::KEY_HEADER;
				$args['HEADER_WIDTH'] = User::HEADER_WIDTH;
				$args['HEADER_HEIGHT'] = User::HEADER_HEIGHT;
				break;
		}
		return $this->render($template, $args);
	}
}