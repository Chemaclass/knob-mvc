<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Config;

use Knob\I18n\I18n;
return [
    'blogAuthor' => 'José María Valera Reales',
    'blogDescription' => ($d = I18n::trans('internal.blog_description')) ? $d : get_bloginfo('description'),
    'blogKeywords' => 'knob, wordpress, framework, mvc, template, mustache, php'
];
