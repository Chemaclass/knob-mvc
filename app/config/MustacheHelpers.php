<?php

namespace Config;

use Knob\I18n\I18n;

/**
 * ============================
 * Your Mustache helpers
 * ============================
 * Helpers already implemented on the core:
 *
 * trans, transu, case.lower, case.upper
 * count, moreThan1, toArray, ucfirst
 * date.xmlschema date.string date.format
 *
 *
 * ----------------------------
 * For example:
 * ----------------------------
 * $lower_text = 'lower text to upper'; // var from PHP code
 *
 * {{#case.upper}} lower_text {{/case.uppper}} -> LOWER TEXT TO UPPER
 * Or
 * {{ lower_text | case.uppper}} -> LOWER TEXT TO UPPER
 *
 * Some links:
 *
 * @link https://github.com/bobthecow/mustache.php#usage
 * @link https://github.com/bobthecow/mustache.php/wiki/FILTERS-pragma
 */
return [
	'case' => [
		'lower' => function ($value) {
			return strtolower((string) $value);
		},
		'upper' => function ($value) {
			return strtoupper((string) $value);
		}
	]
];