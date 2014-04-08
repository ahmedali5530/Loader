<?php
/**
*********************Array Helper library*******************
*-----includes-----
*-----> Array Controller
*-----> Array Utilities
*-----@author------
*------*ahmedali5530*------
**/

Class Arr extends Loader{

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

}
