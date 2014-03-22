<?php

/**
 * Simple wrapper / helper class to give other parts of the application access
 * to the run-time configuration specified in the config.ini file.
 *
 * @author Anish V. Abraham
 */
class Config {

  /** The array that will store the result of parsing the INI file. */
	public static $config = NULL;

  /** Pre-loaded configuration items. */
  public static $YEAR;

  /** Get the value specified for a certain key in the INI file. */
	public static function get($key){
		if (Config::$config === \NULL){
			throw new Exception('Configuration file is not loaded');
		}
		if (isset(Config::$config[$key])){
			return Config::$config[$key];
		}
		else{
			throw new Exception('Variable ' . $key .
              ' does not exist in configuration file');
		}
	}// close get()

}// close Config

// Initialize the config array.
Config::$config = parse_ini_file(BASE_PATH.'/config.ini');
Config::$YEAR = Config::get('year');