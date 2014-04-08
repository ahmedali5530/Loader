<?php
/**
*********************Security Controller library*******************
*-----includes-----
*-----> Security Controller
*-----@author------
*------*ahmedali5530*------
**/

Class Security extends Loader{
	
	protected $encryption_key = 'ahmedali';
	
	public static $instance;
	//constructor
	public function __construct($encryption_key=null)
	{
		parent::__construct();
		if($encryption_key!==null){
		
			$this->encryption_key = $encryption_key;
			
		}
		self::$instance = $this;
		if($this->encryption_key==null)
		{
			exit("Please Specify an encryption key first to use the security class.");
		}
	}
	
	public static function &get_instance()
	{
		return self::$instance;
	}
	
	//do the mysqli_real_escape_string
	public function clean($data)
	{
		$data = filter_var($data, FILTER_SANITIZE_STRING);
		return mysql_real_escape_string(trim($data));
	}

}
