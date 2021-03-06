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

use Knob\Libs\Filters as KnobFilters;
use Knob\Libs\Utils;
use Models\User;

/**
 * Filters from Wordpress
 *
 * @author José María Valera Reales
 */
class Filters extends KnobFilters
{
    public function __construct()
    {
        parent::__construct();
        $this->authorRewriteRules();
        $this->getAvatar();
    }

    /**
     * Change the 'author' slug from the URL base (for each author) to the type of User.
     *
     * Remeber: Manually flush your permalink structure to reflect these changes:
     * 1) Settings -> Permalinks -> choose default -> Save
     * 2) Revert the settings to original.
     */
    protected function authorRewriteRules()
    {
        $AUTHOR_TYPE = '%author_type%';

        add_action('init',
            function () use ($AUTHOR_TYPE) {
                global $wp_rewrite;
                $authorLevels = User::getValidTypes();
                // Define the tag and use it in the rewrite rule
                add_rewrite_tag($AUTHOR_TYPE, '(' . implode('|', $authorLevels) . ')');
                $wp_rewrite->author_base = $AUTHOR_TYPE;
            });

        add_filter('author_rewrite_rules',
            function ($author_rewrite_rules) {
                foreach ($author_rewrite_rules as $pattern => $substitution) {
                    if (false === strpos($substitution, 'author_name')) {
                        unset($author_rewrite_rules[$pattern]);
                    }
                }
                return $author_rewrite_rules;
            });

        add_filter('author_link',
            function ($link, $author_id) use ($AUTHOR_TYPE) {
                $user = User::find($author_id);
                if (!$user) {
                    return;
                }
                return str_replace($AUTHOR_TYPE, $user->getType(), $link);
            }, 100, 2);
    }

    /**
     * Override the get_avatar by default from WP
     */
    protected function getAvatar()
    {
        /*
         * We will get the avatar from our models
         */
        add_filter('get_avatar',
            function ($avatar = '', $id_or_email, $size = User::AVATAR_SIZE_DEFAULT, $default = '', $alt = '') {
                if (is_numeric($id_or_email)) {
                    $user_id = (int)$id_or_email;
                } elseif (is_string($id_or_email) && ($user = get_user_by('email', $id_or_email))) {
                    $user_id = $user->ID;
                } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
                    $user_id = (int)$id_or_email->user_id;
                }
                if (!$user = User::find($user_id)) {
                    $user = new User();
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
