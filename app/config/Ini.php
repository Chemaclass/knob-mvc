<?php

namespace Config;

use ConfigBase\Ini as IniBase;

/**
 * Ini Class
 *
 * @author José María Valera Reales
 *
 */
class Ini {

	/**
	 * Setup
	 */
	public static function setup() {
		error_log("Config/Ini::setup()");
		IniBase::setup();
	}
}