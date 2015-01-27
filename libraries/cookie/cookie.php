<?php
/**
*********************Cookies Handler library*******************
*-----includes-----
*-----> Cookie
*-----> Cookies Handling
*-----@author------
*------*ahmedali5530*------
**/
class Cookie extends Loader
{
	//public properties
	public $expire_time = 0;
	public $path = '';
	public $domain = '';
	public $secure = false;
	public $httponly = false;
	private $encrypt = '';
	
	public function __construct()
	{
		//public constructor
		$this->load('libraries/encryption/encryption');
		$this->encrypt = new Encryption('loader_cookies_key');
	}
	
	//set global expiry time for setter
	public function set_expiry_time($time)
	{
		$this->expire_time = $time;
		return $this;
	}
	
	//set global path for setter
	public function set_path($path)
	{
		$this->path = $path;
		return $this;
	}
	
	//set the domain name for setter
	public function set_domain($domain)
	{
		$this->domain = $domain;
		return $this;
	}
	
	//set secure status for setter
	public function set_secure($secure)
	{
		$this->secure = $secure;
		return $this;
	}
	
	//set httponly status for setter
	public function set_httponly($httponly)
	{
		$this->httponly = $httponly;
		return $this;
	}
	
	function __set($cookie_name,$cookie_value)
	{
		$cookie_value = $this->encrypt->encrypt(serialize($cookie_value));
		
		setcookie($cookie_name,$cookie_value,time()+86400,$this->path,$this->domain,$this->secure,$this->httponly);
	}
	
	function __get($cookie_name)
	{
		return isset($_COOKIE[$cookie_name]) ? unserialize($this->encrypt->decrypt($_COOKIE[$cookie_name])) : null ;
	}
	
	function __unset($cookie_name)
	{
		if(isset($_COOKIE[$cookie_name])){
			setcookie($cookie_name,'',time()-86400);
			return true;
		}else{
			return false;
		}
	}
	
	public function set_cookie($cookie_name,$cookie_value,$expire_time = 0,$path = '',$domain = '',$secure = false,$httponly = false)
	{
		$cookie_value = $this->encrypt->encrypt(serialize($cookie_value));
		setcookie($cookie_name,$cookie_value,$expire_time,$path,$domain,$secure,$httponly);
		return $this;
	}
	
	public function get_cookie($cookie_name)
	{
		return isset($_COOKIE[$cookie_name]) ? unserialize($this->encrypt->decrypt($_COOKIE[$cookie_name])) : null ;
	}
	
	public function delete_cookie($cookie_name)
	{
		//unset($_COOKIE[$cookie_name]);
		if(isset($_COOKIE[$cookie_name])){
			unset($_COOKIE[$cookie_name]);
			setcookie($cookie_name,'',time()-86400);
			return true;
		}else{
			return false;
		}
	}
	
	public function getAll()
	{
		return $_COOKIE;
	}
	
	public function destroy($cookie_name = null)
	{
		if($cookie_name == null)
		{
			//destroy the entire cookies
			unset($_COOKIE);
			return true;
		}
		else
		{
			//isset($_COOKIE[$cookie_name]) ? unset($COOKIE[$cookie_name]) : false ;
			if(isset($_COOKIE[$cookie_name])){
				unset($_COOKIE[$cookie_name]);
				return true;
			}else{
				return false;
			}
		}
		return true;
	}
	
}
