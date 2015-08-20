<?php

namespace Libs;

use Controllers\BackendController;
use Models\User;

/**
 * Actions for Wordpress
 *
 * @author José María Valera Reales
 */
class Actions {

	/**
	 * Put scripts into the admin view
	 */
	public static function adminPrintScripts() {
		add_action('admin_print_scripts', function () {
			wp_enqueue_script('jquery-plugin', COMPONENTS_DIR . '/jquery/jquery.min.js');
			wp_enqueue_script('bootstrap-plugin', COMPONENTS_DIR . '/bootstrap/js/bootstrap.min.js');
			wp_enqueue_script('main', PUBLIC_DIR . '/js/main.js');
		});
	}

	/**
	 * Put styles into the admin view.
	 */
	public static function adminPrintStyles() {
		add_action('admin_print_styles', function () {
			// wp_enqueue_style('knob-bootstrap', COMPONENTS_DIR . '/bootstrap/css/bootstrap.css'); // conflicts with WP
			wp_enqueue_style('knob-font-awesome', COMPONENTS_DIR . '/font-awesome/css/font-awesome.min.css');
			wp_enqueue_style('knob-main', PUBLIC_DIR . '/css/main.css');
		});
	}

	/**
	 * Load the styles, headerurl and headertitle in the login section.
	 */
	public static function loginView() {
		add_action('login_enqueue_scripts', function () {
			wp_enqueue_style('main', PUBLIC_DIR . '/css/main.css');
		});

		add_filter('login_headerurl', function () {
			return home_url();
		});
		add_filter('login_headertitle', function () {
			return BLOG_TITLE;
		});
	}

	/**
	 * Delete the WP logo from the admin bar
	 */
	public static function wpBeforeAdminBarRender() {
		add_action('wp_before_admin_bar_render', function () {
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu('wp-logo');
		});
	}

	/**
	 * Add img avatar and header to user profile
	 */
	public static function userProfileAddImgAvatarAndHeader() {
		/*
		 * We need it if we can activate the img into the forms
		 */
		add_action('user_edit_form_tag', function () {
			echo 'enctype="multipart/form-data"';
		});

		$profileAddImg = function ($user) {
			$controller = new BackendController();
			echo $controller->getRenderProfileImg(User::KEY_AVATAR, $user->ID);
			echo $controller->getRenderProfileImg(User::KEY_HEADER, $user->ID);
		};
		add_action('show_user_profile', $profileAddImg);
		add_action('edit_user_profile', $profileAddImg);
		/*
		 * Add the avatar to user profile
		 */
		$updateImg = function ($user_ID, $keyUserImg) {
			try {
				// 1st check if the user has the enought permission and the key exists on the FILES
				if (current_user_can('edit_user', $user_ID) && isset($_FILES[$keyUserImg])) {
					// Later check if the file have a defined name
					$img = $_FILES[$keyUserImg];
					if ($img['name']) {
						$user = User::find($user_ID);
						switch ($keyUserImg) {
							case User::KEY_AVATAR :
								$user->setAvatar($img);
								break;
							case User::KEY_HEADER :
								$user->setHeader($img);
								break;
						}
					}
				}
			} catch ( \Exception $e ) {
				// Add the error message to the WP notifications
				add_action('user_profile_update_errors', function ($errors) use($e, $keyUserImg) {
					$errors->add($keyUserImg, $e->getMessage());
				});
			}
		};

		$updateImgAvatar = function ($user_ID) use($updateImg) {
			$updateImg($user_ID, User::KEY_AVATAR);
		};
		$updateImgHeader = function ($user_ID) use($updateImg) {
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
	public static function userProfileAddSocialNetworks() {
		$addSocialNetworks = function ($user) {
			$c = new BackendController();
			echo $c->getRenderSocialNetworks($user->ID);
		};
		add_action('show_user_profile', $addSocialNetworks);
		add_action('edit_user_profile', $addSocialNetworks);

		$updateSocialNetworks = function ($user_ID) {
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
	public static function userProfileAddLanguage() {
		$addLang = function ($user) {
			$c = new BackendController();
			echo $c->getRenderLanguage($user->ID);
		};
		add_action('show_user_profile', $addLang);
		add_action('edit_user_profile', $addLang);

		$updateLang = function ($user_ID) {
			if (current_user_can('edit_user', $user_ID)) {
				$user = User::find($user_ID);
				$user->setLang($_POST[User::KEY_LANGUAGE]);
			}
		};
		add_action('personal_options_update', $updateLang);
		add_action('edit_user_profile_update', $updateLang);
	}

	/**
	 * Add widgetsInit
	 */
	public static function widgetsInit() {
		/*
		 * List with your active widgets.
		 *
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
				'id' => Template::SIDEBAR_RIGHT,
				'name' => 'Sidebar right top',
				'classBeforeWidget' => 'sidebar-right-top',
				'beforeWidget' => '<div class="widget sidebar-right">',
				'afterWidget' => '</div>',
				'beforeTitle' => '<span class="title">',
				'afterTitle' => '</span>'
			],
			[
				'id' => Template::FOOTER
			]
		];

		foreach ( $activeWidgets as $w ) {
			add_action('widgets_init', function () use($w) {
				if (isset($w['id'])) {
					$name = isset($w['name']) ? $w['name'] : ucfirst(str_replace('_', ' ', $w['id']));
					if (isset($w['beforeWidget'])) {
						$beforeWidget = $w['beforeWidget'];
					} else {
						$classBeforeWidget = isset($w['classBeforeWidget']) ? $w['classBeforeWidget'] : str_replace('_', '-', $w['id']);
						$beforeWidget = '<div class="widget ' . $classBeforeWidget . '">';
					}
					$afterWidget = isset($w['afterWidget']) ? $w['afterWidget'] : '</div>';
					$beforeTitle = isset($w['beforeTitle']) ? $w['beforeTitle'] : '<span class="title">';
					$afterTitle = isset($w['afterTitle']) ? $w['afterTitle'] : '</span>';

					register_sidebar([
						'id' => $w['id'],
						'name' => $name,
						'before_widget' => $beforeWidget,
						'after_widget' => $afterWidget,
						'before_title' => $beforeTitle,
						'after_title' => $afterTitle
					]);
				}
			});
		}
	}
}
