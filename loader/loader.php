<?php
/**
*********************Loader library*******************
*-----includes-----
*-----> Loader
*-----> Base Methods and Properties
*-----@author------
*------*ahmedali5530*------
**/
//accesses the root directory of project
defined('BASEPATH') OR
define('BASEPATH',dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR,false);

//accesses the libraries directory
defined('LIBPATH') OR
define('LIBPATH',BASEPATH.'libraries'.DIRECTORY_SEPARATOR,false);

//definition of base directory
defined('BASEDIR') OR
define('BASEDIR','ms',false);

Class Loader{

	//connection variable
	public $con ;
	
	//holds the all messages
	public $message = array();
	
	//holds the current instance
	public static $instance ;
	
	//php extension for file inclusion
	public $ext = ".php";
	
	//holds the all dynamic properties
	public $property = array();
	
	const DB = 'school';
	const HOST = 'localhost';
	const USER = 'root';
	const PASSWORD = '';
	
	
	
	//class constructor
	public function __construct()
	{		
		self::$instance = $this;
	}
	
	//magic __toString()
	public function __toString()
	{
		return 'You cannot use the object of Class "' . __CLASS__ . '" as a string.';
	}

	//gets the current instance
	public static function get_instance()
	{
		return self::$instance;
	}
	
	//loads the 404 document when not getting right file
	public function _404()
	{
		return $this->base_dir()."404".$this->ext;	
	}
	
	//base_url()
	public function base_url()
	{
		return 'http://localhost/'.BASEDIR. '/';
	}
	
	//performs a basic redirect
	public function redirect($location)
	{
		ob_start();
		
		header("Location:".$this->base_url().$location);
		
		ob_end_flush();
		
		exit();
	}
	
	//loads the css file
	public function css($file)
	{
		return '<link href="'.$this->base_url().$file.'.css" type="text/css" rel="stylesheet" media="all" >'."\n";
	}
	
	//loads the js file
	public function js($file)
	{
		return '<script src="'.$this->base_url().$file.'.js" type="text/javascript" ></script>'."\n";
	}
	
	//loads the given file from the directory
	//alias of require_once or include_once
	public function load($filename)
	{
		if(isset($filename) && $filename!=="" && $filename!==null)
		{
			if (file_exists(BASEPATH.$filename.$this->ext))
			{
				// extract variables from the global scope:
				extract($GLOBALS, EXTR_REFS);
				
				ob_start();
				
				include(BASEPATH.$filename.$this->ext);
				
				return ob_get_clean();
			} else
			{
				ob_clean();
				trigger_error("File {$filename} not found.");
			}
		}
		else
		{
			trigger_error( "Please Specify a file name.", E_USER_WARNING );
		}
	}
	
	//returns the array of sub directories in a particular directory
	public function get_subdirectories($directory)
	{
		$dirs = array();
		
		$d = dir($directory);
		
		while(false != ($entry = $d->read()))
		{
		   if(is_dir(implode('/', array($directory, $entry))))
		   {
			  if(($entry != '.') && ($entry != '..'))
			  { 
				 $dirs[] = $entry;
			  }
		   }
		}
		return $dirs;
	}

	public function __destruct()
	{
		//
	}
}
