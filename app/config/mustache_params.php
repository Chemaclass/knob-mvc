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
 * Your Mustache params
 * ============================
 *
 * @see knob-base/src/config/mustache_params.php -> Parent file
 */

return [
    'ajaxUrl' => '/ajax',

    'blogAuthor' => 'José María Valera Reales',
    'blogDescription' => ($description = $i18n->trans('internal.blog_description'))
        ? $description : get_bloginfo('description'),
    'blogKeywords' => 'knob, wordpress, framework, mvc, template, mustache, php',
];
