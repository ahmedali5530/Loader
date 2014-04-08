<?php
/**
*********************String Controller library*******************
*-----includes-----
*-----> String Controller
*-----> String Utilities
*-----@author------
*------*ahmedali5530*------
**/

Class Str extends Loader{
	
	public static $instance;
	//constructor
	public function __construct()
	{
		parent::__construct();
		self::$instance = $this;
	}
	
	public static function get_instance()
	{
		return self::$instance;
	}
	
	public function trim($string)
	{
		return rtrim(trim($string)," ");
	}

}
