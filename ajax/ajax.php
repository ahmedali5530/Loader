<?php
/**
*********************Ajax library*******************
*-----includes-----
*-----> Ajax and jQuery Controller
*-----> Ajax calls and jQuery Utilities
*-----> requires jQuery for working...
*-----@author------
*------*ahmedali5530*------
**/

Class Ajax extends Loader{
	
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
?>
