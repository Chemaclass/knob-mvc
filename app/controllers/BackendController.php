<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Controllers;

use Knob\Controllers\BaseController;
use Knob\Models\User;
use Knob\I18n\I18n;

/**
 * Backend Controller
 *
 * @author José María Valera Reales
 */
class BackendController extends BaseController
{

    /**
     * Return the view to change img from User
     *
     * @param integer $user_ID
     */
    public function getRenderProfileImg($keyUserImg, $user_ID = false)
    {
        if (!$user_ID) {
            global $user_ID;
        }
        $user = User::find($user_ID);
        $args = [
            'user' => $user
        ];
        switch ($keyUserImg) {
            case User::KEY_AVATAR:
                $template = 'backend/user/_img_avatar';
                $args['KEY_AVATAR'] = User::KEY_AVATAR;
                break;
            case User::KEY_HEADER:
                $template = 'backend/user/_img_header';
                $args['KEY_HEADER'] = User::KEY_HEADER;
                $args['HEADER_WIDTH'] = User::HEADER_WIDTH;
                $args['HEADER_HEIGHT'] = User::HEADER_HEIGHT;
                break;
        }
        return $this->render($template, $args);
    }

    /**
     * Return the view to change social networks like tw, fb...
     *
     * @param string $user_ID
     */
    public function getRenderSocialNetworks($user_ID = false)
    {
        if (!$user_ID) {
            global $user_ID;
        }
        $user = User::find($user_ID);
        $args = [
            'user' => $user,
            'KEY_TWITTER' => User::KEY_TWITTER,
            'KEY_FACEBOOK' => User::KEY_FACEBOOK,
            'KEY_GOOGLE_PLUS' => User::KEY_GOOGLE_PLUS
        ];
        return $this->render('backend/user/_social_networks', $args);
    }

    /**
     *
     * @param string $user_ID
     */
    public function getRenderLanguage($user_ID = false)
    {
        if (!$user_ID) {
            global $user_ID;
        }
        $user = User::find($user_ID);
        // Format the list
        $userLang = $user->getLang();
        foreach (I18n::getAllLangAvailable() as $t) {
            $languages[] = [
                'value' => $t,
                'text' => I18n::transu('lang_' . $t),
                'selected' => ($userLang == $t)
            ];
        }
        $args = [
            'user' => $user,
            'KEY_LANGUAGE' => User::KEY_LANGUAGE,
            'languages' => $languages
        ];
        return $this->render('backend/user/_lang', $args);
    }
}