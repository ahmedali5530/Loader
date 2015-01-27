<?php
/**
*********************Input library*******************
*-----includes-----
*-----> Input Controller
*-----@author------
*------*ahmedali5530*------
**/

Class Input extends Loader
{
	
	//returns the posted data
	public function post($field = null)
	{
		if(isset($field))
		{
			if(isset($_POST[$field]))
			{
				return $_POST[$field];
			}
		}else
		{
			return $_POST;
		}
	}
	
	//returns the GET data
	public function get($field = null)
	{
		if(isset($field))
		{
			if(isset($_GET[$field]))
			{
				return $_GET[$field];
			}
		}else
		{
			return $_GET;
		}
	}
	
	//returns the REQUEST data
	public function request($field = null)
	{
		if(isset($field))
		{
			if(isset($_REQUEST[$field]))
			{
				return $_REQUEST[$field];
			}
		}else
		{
			return $_REQUEST;
		}
	}
	
	//returns the files data
	public function files($field,$property=null)
	{
		if(isset($_FILES[$field]))
		{
			if(isset($_FILES[$field][$property]))
			{
				return $_FILES[$field][$property];
			}
			return $_FILES[$field];
		}
		else
		{
			return null;
		}
	}
	
	//returns the cookie data
	public function cookie($field)
	{
		return isset($field) ? $_COOKIE[$field ] : null;
	}
	
	//returns the session data
	public function session($field)
	{
		return isset($field) ? $_SESSION[$field ] : null;
	}
}
