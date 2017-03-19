<?php
namespace Repository;

use Knob\Repository\UserRepository as KnobUserRepository;
use Models\User;

class UserRepository implements KnobUserRepository
{
    /** @var User */
    private static $currentUser = null;

    /**
     * @return User
     */
    public function getCurrent()
    {
        // FIXME! Do we really want here a singleton?
        if ($user_ID = get_current_user_id()) {
            if (null === static::$currentUser) {
                static::$currentUser = User::find($user_ID);
            }
            return static::$currentUser;
        } else {
            static::$currentUser = new User;
        }

        return static::$currentUser;
    }
}