<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Libs;

use Controllers\BackendController;
use Models\User;
use Knob\I18n\I18n;
use Knob\Libs\Actions as KnobActions;

/**
 * Actions for Wordpress
 *
 * @author José María Valera Reales
 */
class Actions extends KnobActions
{

    /**
     * Setup the actions
     *
     * @see KnobActions::setup()
     */
    public static function setup()
    {
        parent::setup();
        static::registerNavMenus();
        static::userProfileAddImgAvatarAndHeader();
        static::userProfileAddSocialNetworks();
        static::userProfileAddLanguage();
        static::widgetsInit();
    }

    /**
     * Register Sidebar using register_sidebar from WP.
     *
     * @see KnobActions::widgetsInit($activeWidgets)
     */
    public static function widgetsInit($activeWidgets = [])
    {
        /*
         * List with your active widgets.
         * 'id': His id. We'll use it later for get it and put in his correct place.
         * 'name': Sidebar name. Optional
         * 'classBeforeWidget': Class for 'beforeWidget'. Optional
         * 'beforeWidget': HTML to place before every widge. Optional
         * 'afterWidget': HTML to place after every widget. Optional
         * 'beforeTitle': HTML to place before every title. Optional
         * 'afterTitle': HTML to place after every title. Optional
         */
        $activeWidgets = [
            [
                'id' => Widgets::$widgetsRight,
                'name' => 'Widgets right',
                'classBeforeWidget' => 'sidebar-right',
                'beforeWidget' => '<div class="widget sidebar">',
                'afterWidget' => '</div>',
                'beforeTitle' => '<span class="title">',
                'afterTitle' => '</span>'
            ],
            [
                'id' => Widgets::$widgetsFooter
            ]
        ] + $activeWidgets;

        parent::widgetsInit($activeWidgets);
    }

    /**
     * Register Nav Menus.
     *
     * @see http://codex.wordpress.org/Navigation_Menus
     */
    public static function registerNavMenus()
    {
        add_action('init', function ()
        {
            foreach (Menu::getMenusActive() as $menu) {
                $menus[$menu] = I18n::transu($menu);
            }
            register_nav_menus($menus);
        });
    }

    /**
     * Add img avatar and header to user profile
     */
    public static function userProfileAddImgAvatarAndHeader()
    {
        /*
         * We need it if we can activate the img into the forms
         */
        add_action('user_edit_form_tag', function ()
        {
            echo 'enctype="multipart/form-data"';
        });
        
        /** @var WP_User $user */
        $profileAddImg = function ($user)
        {
            $controller = new BackendController();
            echo $controller->getRenderProfileImg(User::KEY_AVATAR, $user->ID);
            echo $controller->getRenderProfileImg(User::KEY_HEADER, $user->ID);
        };
        add_action('show_user_profile', $profileAddImg);
        add_action('edit_user_profile', $profileAddImg);
        /*
         * Add the avatar to user profile
         */
        $updateImg = function ($user_ID, $keyUserImg)
        {
            try {
                // 1st check if the user has the enought permission and the key exists on the FILES
                if (current_user_can('edit_user', $user_ID) && isset($_FILES[$keyUserImg])) {
                    // Later check if the file have a defined name
                    $img = $_FILES[$keyUserImg];
                    if ($img['name']) {
                        $user = User::find($user_ID);
                        switch ($keyUserImg) {
                            case User::KEY_AVATAR:
                                $user->setAvatar($img);
                                break;
                            case User::KEY_HEADER:
                                $user->setHeader($img);
                                break;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Add the error message to the WP notifications
                add_action('user_profile_update_errors',
                    function ($errors) use($e, $keyUserImg)
                    {
                        $errors->add($keyUserImg, $e->getMessage());
                    });
            }
        };

        $updateImgAvatar = function ($user_ID) use($updateImg)
        {
            $updateImg($user_ID, User::KEY_AVATAR);
        };
        $updateImgHeader = function ($user_ID) use($updateImg)
        {
            $updateImg($user_ID, User::KEY_HEADER);
        };

        add_action('personal_options_update', $updateImgAvatar);
        add_action('edit_user_profile_update', $updateImgAvatar);
        add_action('personal_options_update', $updateImgHeader);
        add_action('edit_user_profile_update', $updateImgHeader);
    }

    /**
     * Add Social networks to user
     */
    public static function userProfileAddSocialNetworks()
    {
        /** @var WP_User $user */
        $addSocialNetworks = function ($user)
        {
            $c = new BackendController();
            echo $c->getRenderSocialNetworks($user->ID);
        };
        add_action('show_user_profile', $addSocialNetworks);
        add_action('edit_user_profile', $addSocialNetworks);

        $updateSocialNetworks = function ($user_ID)
        {
            if (current_user_can('edit_user', $user_ID)) {
                $user = User::find($user_ID);
                $user->setTwitter($_POST[User::KEY_TWITTER]);
                $user->setFacebook($_POST[User::KEY_FACEBOOK]);
                $user->setGooglePlus($_POST[User::KEY_GOOGLE_PLUS]);
            }
        };
        add_action('personal_options_update', $updateSocialNetworks);
        add_action('edit_user_profile_update', $updateSocialNetworks);
    }

    /**
     * Add language to user profile
     */
    public static function userProfileAddLanguage()
    {
        /** @var WP_User $user */
        $addLang = function ($user)
        {
            $c = new BackendController();
            echo $c->getRenderLanguage($user->ID);
        };
        add_action('show_user_profile', $addLang);
        add_action('edit_user_profile', $addLang);

        $updateLang = function ($user_ID)
        {
            if (current_user_can('edit_user', $user_ID)) {
                $user = User::find($user_ID);
                $user->setLang($_POST[User::KEY_LANGUAGE]);
            }
        };
        add_action('personal_options_update', $updateLang);
        add_action('edit_user_profile_update', $updateLang);
    }
}
