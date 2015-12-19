<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ============================
 * Your global config file
 * ============================
 */
return [
    /**
     * ====================
     * Environments
     * ====================
     */
    'env' => [
        'prod' => 'knob.chemaclass.com',
        'dev' => 'knob.chemaclass.dev',
        'loc' => 'knob.chemaclass.local'
    ],

    /**
     * ====================
     * Languages available
     * ====================
     */
    'langAvailable' => [
        'en' => 'english',
        'es' => 'español',
        'de' => 'deutsch'
    ],

    /**
     * ====================
     * Language by default
     * ====================
     */
    'langDefault' => 'en',

    /**
     * ====================
     * Mailer
     * ====================
     */
    'mailer' => [
        'host' => '%mailer_host%',
        'username' => '%mailer_username%',
        'password' => '%mailer_password%'
    ]
];
