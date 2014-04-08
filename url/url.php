<?php
/**
*********************URL Controller library*******************
*-----includes-----
*-----> URL Controller
*-----> URL Utilities
*-----@author------
*------*ahmedali5530*------
**/

Class URL extends Loader{

	public static $instance;
	
	public $uri_segments = array();
	
	public $get_query;
	//constructor
	public function __construct()
	{
		parent::__construct();
		//$this->re_write_url();
		self::$instance = $this;
	}
	
	public static function get_instance()
	{
		return self::$instance;
	}
	
	public function get_uri_segment($segment=0)
	{
		return isset($this->uri_segments[$segment])?$this->uri_segments[$segment]:null;
	}
	
	public function get_uri_segments()
	{
		return $this->uri_segments;
	}
	
	public function prepare_get_query($keys,$values)
	{
		return $this->re_write_url($keys,$values);
	}
	
	public function get_url()
	{
		$page_name = end(explode('/',$_SERVER['SCRIPT_NAME']));
		return $this->base_url().$page_name.'?'.$this->get_query;
		//print_r($_SERVER);
	}
	
	public function re_write_url($keys,$values,$encoding='rawurldecode')
	{
		if(is_array($keys))
		{
			if(is_array($values))
			{
				if(count($values)==count($keys))
				{
					for($i=0;$i<=count($keys)-1;$i++)
					{
						if($encoding == 'rawurldecode')
						{
							$this->get_query[$keys[$i]] = rawurldecode($values[$i]);
							$this->uri_segments[$i] = $values[$i];
							$this->uri_segments[$keys[$i]] = $values[$values];
						}else
						{
							$this->get_query[$keys[$i]] = urldecode($values[$i]);
						}
					}
				}else
				{
					throw new Exception('Values in Both arrays did not match each other.');
				}
			}else
			{
				throw new Exception('Values is not a valid array.');
			}
		}else
		{
			$this->get_query[$keys] = $values;
		}
		
		return http_build_query($this->get_query);
	}

}
