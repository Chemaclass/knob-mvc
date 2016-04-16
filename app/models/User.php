<?php
/*
 * This file is part of the Knob-base package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Models;

use Knob\Models\User as KnobUser;

/**
 *
 * @author José María Valera Reales.
 */
class User extends KnobUser
{
    /*
     * Sidebar
     */
    const WITH_SIDEBAR_DEFAULT = true;
    
    /**
     * Return the instance of the current user, or null if they're not logged
     *
     * @return User
     */
    public static function getCurrent()
    {
        $user = wp_get_current_user();
        if ($user->ID) {
            return User::find($user->ID);
        }
    
        return null;
    }
}