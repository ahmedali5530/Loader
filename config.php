<?php

//defines the environment for project
/**
*development = display all PHP errors, good for testing environment...
*production = hide all PHP errors.
**/
defined('ENVIRONMENT') OR
define('ENVIRONMENT','development');

if(ENVIRONMENT == 'development'){
	error_reporting(E_ALL);
}else{
	error_reporting(0);
}

//define autoloader global array
$autoload = array();

//define loader global object
$loader = new stdClass();

//define the default file extension for loading files.
defined('ext') OR
define('ext','.php',false);
/***
*
*Defines the Root Directory of Application
*
**/
defined('BASEPATH') OR
define('BASEPATH',dirname(__FILE__).DIRECTORY_SEPARATOR,false);

/***
*
*Defines the Libraries Directory of Application
*
**/
defined('LIBPATH') OR
define('LIBPATH',BASEPATH.'libraries'.DIRECTORY_SEPARATOR,false);

/***
*
*Defines the Access Host Directory of Application
*change the http://localhost/ to http://yoursite.com
**/
defined('HOST') OR
define('HOST','http://localhost/',false);

/***
*
*Defines the Base Directory of Application
* if Your application is online use '' in the place of "loader/" empty string here.
**/
defined('BASEDIR') OR
define('BASEDIR','loader_1_0_1/',false);

/***
*
*Defines the Version of Application
* Write your application version.
**/
defined('APP_VERSION') OR
define('APP_VERSION','1.0',false);

/*
	Define session variables prefix
*/
defined('SESSION_PREFIX') OR
define('SESSION_PREFIX','loader',false);

/*
	Define session variables prefix
*/
defined('COOKIE_PREFIX') OR
define('COOKIE_PREFIX','loader',false);

/*
	define auto loader classes/libraries
	i.e $autoload['classes'] = array('db','form');
	or $autoload['classes'][] = 'carbon';
*/
$autoload['classes'] = array('loader','db','session','cookie','carbon','encryption');

/*
	define auto loader functions
	i.e $autoload['functions'] = array('functions_file');
	or $autoload['functions'][] = 'functions_file';
*/
$autoload['functions'] = array('common');

/*
	make an array of default messages
*/
$messages = array(
	'0'=>'Operation cancelled.',
	'1'=>'Operation was Successful.',
	'2'=>'Updated Successfully.',
	'3'=>'Deleted Successfully.',
	'4'=>'Object do not exists.',
	'5'=>'Operation Cancelled due to an error.',
);
/***
*
*Sets the Default Timezone of Application
*
**/
date_default_timezone_set('Asia/Karachi');

/***
*
*Defines the base URL of Application
*
**/
function base_url()
{
	return HOST.BASEDIR;
}

/***
* Define the Database connection Constants
**/
//Database name
define('DB_NAME' , 'quran');

//Database Host probaly localhost
define('DB_HOST' , 'localhost');

//Database Username
define('DB_USER' , 'root');

//Database password
define('DB_PASSWORD' , '');

//Database Prefix. If do not know about this please leave this blank
define('DB_PREFIX' , 'mql_');
	
/***
*
*Defines the Root Directory of Application
*
**/
function load_libs($file){
	if($file === 'self'){
		//do nothing this is a BUG...
	}else{
		require_once(LIBPATH . strtolower($file).'/'.strtolower($file).ext);
	}
}
spl_autoload_register('load_libs');

//check if autoloader classes have been defined
if(!empty($autoload) && !empty($autoload['classes'])){
	//do a loop to load all autoloader classes
	foreach($autoload['classes'] as $class){
		//now create the new instances of all autoloader classes
		$loader->$class = new $class;
	}
}

//check if autoloader functions have been defined
if(!empty($autoload) && !empty($autoload['functions'])){
	//do a loop to load all autoloader functions
	foreach($autoload['functions'] as $func){
		//now create the new instances of all autoloader functions
		$loader->loader->load('libraries/'.$func.'/'.$func);
	}
}
