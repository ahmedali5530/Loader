<?php
/**
*********************Upload library*******************
*-----includes-----
*-----> Upload
*-----> form uploads
*-----@author------
*------*ahmedali5530*------
**/

Class Uploader extends Loader{

	//allowed types
	protected $types = array(
		'1'  => 'image/jpeg',
		'2'  => 'image/jpg',
		'3'  => 'image/png' ,
		'4'  => 'image/gif' ,
		'5'  => 'gif' ,
		'6'  => 'jpeg' ,
		'7'  => 'jpg' ,
		'8'  => 'png' ,
	);
	
	//holds the type specified by user
	public $type;
	
	//checks if user selected random name option
	public $rand = false;
	
	//holds the size of uploading size
	public $size = 2000000 ;
	
	//holds the path of uploaded file
	public $path = "images/";
	
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
	
	//uploads the file
	public function upload($file,$options=null)
	{
		//validations.
		//validations..
		//validations...
		//:p
		if(isset($_FILES[$file]['name']))
		{
			if(is_uploaded_file($_FILES[$file]['tmp_name']))
			{
				//initialize messages
				$this->message['file_upload'][$file]['error'] = null;
				//$this->message['file_upload'][$file]['success'] = array();
				
				if(is_array($options) && isset($options['type']))
				{
					$this->type = $options['type'];	
				}
				
				if(is_array($options) && isset($options['size']))
				{
					$this->size = $options['size'];	
				}
				
				if(is_array($options) && isset($options['path']))
				{	
					$this->path = $options['path'];	
				}
				
				if(is_array($options) && isset($options['random_name']))
				{	
					$this->rand = true;
				}
				
				if(isset($this->type) && $this->type !=="")
				{
					if($this->type == $_FILES[$file]['type'])
					{		
						$this->message['file_upload'][$file]['success'][] = 'type matched!';
					}
					else
					{
						$this->message['file_upload'][$file]['error'][] = $_FILES[$file]['type'] . ' .This file type is not allowed.';
					}
					
				}
				else
				{
					if(in_array($_FILES[$file]['type'],$this->types)){
						
						$this->message['file_upload'][$file]['success'][] = 'type matched!';
					}
					else
					{
						$this->message['file_upload'][$file]['error'][] = $_FILES[$file]['type'] . ' .This file type is not allowed.';
					}
				}
				
				if($_FILES[$file]['size'] <= $this->size)
				{
					$this->message['file_upload'][$file]['success'][] = 'Size is OK!';	
				}
				else
				{
					$this->message['file_upload'][$file]['error'][] = 'Size is larger than allowed.';
				}
				
				if(count($this->message['file_upload'][$file]['error'])==0)
				{
					$tmp_name = $_FILES[$file]['tmp_name'];
					
					$path = BASEPATH.$this->path;
					
					if(file_exists($path))
					{
						//do nothing
					}
					else
					{
						//create the folder instead
						mkdir($path);
					}
					
					$name = $this->rand==true ? $this->random_name($_FILES[$file]['name']) : $_FILES[$file]['name'].strtolower(pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION));
					
					//upload file here
					if(move_uploaded_file($tmp_name,$path.$name))
					{
						//returns the name, type, size, path, url in an array.
						return array(
						
							'name'	=> $name,
							'type'	=> $_FILES[$file]['type'],
							'size'	=> $_FILES[$file]['size'],
							'path'	=> $path,
							'URL' 	=> base_url().$this->path.$name,
						);
						
					}
					else
					{
						$this->message['file_upload'][$file]['error'][] = "File Not Uploaded.";
					}
				
				}
				else
				{
					return false;
				}
				
			}
			else
			{
				$this->message['file_upload'][$file]['error'][] = "No File is selected!";
			}
		}
	}
	
	//sends back error messages to user for each uploading item
	public function show_message($element)
	{
		if(isset($this->message['file_upload'][$element]['error']))
		{
			if(!empty($this->message['file_upload'][$element]['error']))
			{
				$error = '<ul>';

				foreach($this->message['file_upload'][$element]['error'] as $error)
				{
					$error .= '<li>' . $error . '</li>';
				}
				
				$error .= '</ul>';
				return $error;
			}
			else
			{
				$error = '<ul>';
				
					$error .= '<li>';
					
					$error .= $this->message['file_upload'][$element]['error'];
					
					$error .= '</li>';
					
				$error .= '</ul>';
				return $error;
			}
			
		}
		else
		{
			return;
		}
	
	}
	
	//generates a random name for uploaded file
	public function random_name($file)
	{
		return md5($file.'_'.substr(uniqid(),-4)).'_'.substr(uniqid(),-4).'.'.strtolower(pathinfo($file, PATHINFO_EXTENSION));
	}

}
