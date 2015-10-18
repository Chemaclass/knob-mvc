<?php
namespace Widgets;

use Knob\I18n\I18n;
use Knob\Widgets\WidgetBase;

/**
 *
 * @author José María Valera Reales
 */
class LoginWidget extends WidgetBase
{

    /**
     * (non-PHPdoc)
     *
     * @see \Widgets\WidgetBase::widget()
     */
    public function widget($args, $instance)
    {
        /*
         * Put all special URLs
         */
        $instance['logoutUrl'] = wp_logout_url(home_url());
        $instance['loginUrl'] = wp_login_url(home_url());
        $instance['registrationUrl'] = wp_registration_url();
        $instance['lostPasswordUrl'] = wp_lostpassword_url();
        $instance['postsUrl'] = '/wp-admin/edit.php';

        /*
         * And call the widget func from the parent class WidgetBase.
         */
        parent::widget($args, $instance);
    }
}
