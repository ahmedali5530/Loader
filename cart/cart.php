<?php
/**
*********************String Controller library*******************
*-----includes-----
*-----> String Controller
*-----> String Utilities
*-----@author------
*------*ahmedali5530*------
**/

Class Cart extends Loader implements ArrayAccess,Countable{
	
	public static $instance;
	
	public $items;
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
	
	//required by ArrayAccess Interface to set the values as array
	public function offsetSet($offset, $value)
	{
        if (is_null($offset)) {
            $_SESSION[] = $value;
        } else {
            $_SESSION[$offset] = $value;
        }
    }
	
	//required by ArrayAccess Interface to check the values of array
    public function offsetExists($offset)
	{
        return isset($_SESSION[$offset]);
    }
	
	//required by ArrayAccess Interface to unset the values of array
    public function offsetUnset($offset)
	{
        unset($_SESSION[$offset]);
    }
	
	//required by ArrayAccess Interface to get the values as array
    public function offsetGet($offset)
	{
        return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
    }
	
	//required by Countable interface
	public function count()
	{
		return count($_SESSION);
	}
	
	//getters and setters
	public function __set($key,$val)
	{
		$this->offsetSet($key,$val);
		return $this;
	}
	
	public function __get($key)
	{
		$this->offsetGet($key);
		return $this;
	}
	
	//regular methods
	//add products to cart
	public function add_to_cart($item,$data)
	{
		$this->offsetSet($item,$data);
		return $this;
	}
	
	//remove from cart
	public function remove_from_cart($item)
	{
		$this->offsetUnset($item);
		return $this;
	}
	
	//display cart
	public function display_cart()
	{
		return $_SESSION;
	}
	
	//list products
	public function list_cart()
	{
		return array_keys($_SESSION);
	}
	
	//list product
	public function list_item($item)
	{
		return $this->offsetGet($item);
	}
	
	//products
	public function list_items()
	{
		return array_values($this->display_cart());
	}
	
	//empties the cart
	public function empty_cart()
	{
		unset($_SESSION);
	}
	
	

}
