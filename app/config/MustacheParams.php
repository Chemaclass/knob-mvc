<?php

namespace Config;

use Knob\I18n\I18n;

return [
	'blogAuthor' => 'José María Valera Reales',
	'blogDescription' => ($d = I18n::trans('internal.blog_description')) ? $d : get_bloginfo('description'),
	'blogKeywords' => 'knob, wordpress, framework, mvc, template, mustache, php'
];
