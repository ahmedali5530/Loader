<?php
/**
*********************Form control library*******************
*-----includes-----
*-----> Form
*-----> form validation rules
*-----> other form utilities
*-----@author------
*------*ahmedali5530*------
**/

class Form extends Loader{
	
	// checks if validation runs true of false
	// initialized as false
	public $validation = false;
	
	/**
	* Regular methods starts from here-------------------------------------------------------------------------
	**/
	
	
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
	
	//sets the values of form on the basis of POST or GET data.
	public function set_value($field)
	{
		if(isset($_REQUEST[$field]))
		{
			return $_REQUEST[$field];
		}
		else
		{
			return;
		}
	}
	
	//sets the validation rules
	//do the validation process...
	public function set_rules($field,$nice_name,$rules)
	{
		$this->_execute_validation($field,$nice_name,$rules);
		return $this;
	}
	
	private function _execute_validation($field,$nice_name,$rules)
	{
		if(in_array("req",$rules))
		{
			$this->required($field,$nice_name);
		}
		
		if(in_array("num",$rules))
		{
			$this->number($field,$nice_name);
		}
		
		if(array_key_exists("min",$rules))
		{
			$this->min($field,$rules['min'],$nice_name);
		}
		
		if(array_key_exists("max",$rules))
		{
			$this->max($field,$rules['max'],$nice_name);
		}
		
		if(array_key_exists("equal",$rules))
		{
			$this->equal($field,$rules['equal'],$nice_name);
		}
		
		if(in_array("email",$rules))
		{
			$this->email($field,$nice_name);
		}
		
		if(in_array("alpha",$rules))
		{
			$this->alpha($field,$nice_name);
		}
		
		if(in_array("alpha_num",$rules))
		{
			$this->alpha_numeric($field,$nice_name);
		}
		
		if(in_array("alpha_dash",$rules))
		{
			$this->alpha_dash($field,$nice_name);
		}
		
		if(in_array("decimal",$rules))
		{
			$this->decimal($field,$nice_name);
		}
		
		if(array_key_exists("greater_than",$rules))
		{
			$this->greater_than($field,$rules['greater_than'],$nice_name);
		}
		
		if(array_key_exists("less_than",$rules))
		{
			$this->less_than($field,$rules['less_than'],$nice_name);
		}
		
		if(in_array("base64",$rules))
		{
			$this->valid_base64($field,$nice_name);
		}
	}
	
	//sets the validation states
	public function validate()
	{
		if(isset($this->message['validation']['error']))
		{
			$this->validation=false;
		}
		else
		{
			$this->validation=true;	
		}
	
	}
	
	//displays the errors as whole
	public function validation_errors()
	{
		if(isset($this->message['validation']['error']))
		{
			//uses bootstrap.css classes to styles error messages
			echo '<ul class="list-unstyled">';
			if(is_array($this->message['validation']['error']))
			{
				foreach($this->message['validation']['error'] as $errors)
				{
					foreach($errors as $error)
					{
						echo '<li class="text-danger">' . $error . '</li>';
					}
				}
			}
			else
			{
				echo '<li class="text-danger">';
				
				echo $this->message['validation']['error'];
				
				echo '</li>';
			}
			echo '</ul>';
		}
	}
	//shows the single message for each single form element
	public function show_message($element)
	{
		$element = ucfirst($element);
		if(isset($this->message['validation']['error'][$element]))
		{
			if(is_array($this->message['validation']['error'][$element]))
			{
				echo '<ul>';

				foreach($this->message['validation']['error'][$element] as $error)
				{
					echo '<li>' . $error . '</li>';
				}
				echo '</ul>';
			}
			else
			{
				echo '<ul>';
				
					echo '<li>';
					
					echo $this->message['validation']['error'][$element];
					
					echo '</li>';
					
				echo '</ul>';
			}
		}
		else
		{
			return;	
		}
	}
	
	//validation functions
	//checks the if field is filled or not
	public function required($field,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if($_POST[$field]!=="" && $_POST[$field]!==null)
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}
			else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is Required.";
			}
		}
	}
	
	//checks if the field is number or not
	public function number($field,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(is_numeric($_POST[$field]))
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}
			else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($finice_nameeld) . "</strong> is Not a Number.";
			}
		}
	}
	
	//checks the value is minimum to the specified
	public function min($field,$value,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(strlen($_POST[$field])>=$value)
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> length is Less than specified.";
			}
		}
	}
	
	public function max($field,$value,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(strlen($_POST[$field])<=$value)
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> length is Greater than specified.";
			}
		}
	}
	
	public function equal($field,$value,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(strlen($_POST[$field])==$value)
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> length is not matching with specified.";
			}
		}
	}
	
	public function email($field,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $_POST[$field]))
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is not a valid Email address.";
			}
		}
	}
	
	public function alpha($field,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(preg_match("/^([a-z])+$/i", $_POST[$field]))
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is not a valid Alpha Value.";
			}
		}
	}
	
	public function alpha_numeric($field,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(preg_match("/^([a-z0-9])+$/i", $_POST[$field]))
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is not a valid Alpha Numeric Value.";
			}
		}
	}
	
	public function alpha_dash($field,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(preg_match("/^([-a-z0-9_-])+$/i", $_POST[$field]))
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is not a valid Alpha Dash Value.";
			}
		}
	}
	
	public function decimal($field,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $_POST[$field]))
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is not a valid Alpha Dash Value.";
			}
		}
	}
	
	public function greater_than($field,$nice_name, $value)
	{
		if(isset($_POST[$field]))
		{
			if($_POST[$field]>$value)
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is Less than specified.";
			}
		}
	}
	
	public function less_than($field,$nice_name, $value)
	{
		if(isset($_POST[$field]))
		{
			if($_POST[$field]<$value)
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is Greater than specified.";
			}
		}
	}
	
	public function valid_base64($field,$nice_name)
	{
		if(isset($_POST[$field]))
		{
			if(preg_match('/[^a-zA-Z0-9\/\+=]/', $_POST[$field]))
			{
				$this->message['validation']['success'][ucfirst($field)][] = "";
			}else
			{
				$this->message['validation']['error'][ucfirst($field)][] = "<strong>" . ucfirst($nice_name) . "</strong> is not a valid Base64 Value.";
			}
		}
	}
	
}
/**
*********************Form control library*******************
*-----includes-----
*-----> form validation rules
*-----> other form utilities
*-----@author------
*------*ahmedali5530*------
*-----@file--------
*------form.php----
**/
