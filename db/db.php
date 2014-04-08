<?php
/**
*********************Database Controller library*******************
*-----includes-----
*-----> DB Controller
*-----> other DB utilities
*-----@author------
*------*ahmedali5530*------
**/

Class DB extends Loader{
	
	//holds the select fields of the table
	protected $select ;

	//holds the where fields of the table
	protected $where ;

	//holds the table name
	protected $table ;

	//holds the current query string
	protected $query = '';
	
	//holds the current query result
	protected $result = '';
	
	//limit
	protected $limit;
	
	//offset
	protected $offset;
	
	//order by Such as ASC or DESC
	protected $order_by;
	
	//order by mode
	protected $order_by_mode;
	
	//group by
	protected $group_by;
	
	//holds the cycles for a multi_insert or multi_update query
	protected $cycles ;

	//holds the number of rows returned
	public $num_rows = 0;

	//holds the simple array result returned by database
	public $get_array = array();

	//holds the object result returned by database
	public $get_object = array();
	
	var $security;
	
	/**
	* Regular methods starts here-------------------------------------------------------------------------
	**/
	
	//constructor
	public function __construct($host=null,$user=null,$pw=null,$db=null)
	{
		if($host==null && $user==null && $pw==null && $db==null){
			$this->con = new Mysqli(self::HOST,self::USER,self::PASSWORD,self::DB);
		}else{
			$this->con = new Mysqli($host,$user,$pw,$db);
		}
		if ($this->con->connect_errno) {
			die("Failed to connect to Server: (" . $this->con->connect_errno . ") " . $this->con->connect_error);
		}
		$this->con->set_charset('utf8');
		
		self::$instance = $this;
		$this->load('Libraries/security/Security');
		$this->security = new Security();
	}
	
	public static function get_instance()
	{
		return self::$instance;
	}
	

	//resets the variables
	public function reset()
	{
		unset($this->select);
		unset($this->table);
		unset($this->query);
		unset($this->where);
		unset($this->cycles);
		unset($this->get_array);
		unset($this->get_object);
	}

	//prepares the select fields
	public function select($fields)
	{
		if(is_array($fields))
		{
			foreach($fields as $field)
			{
				$this->select[] = $field;
			}
		}else
		{
			$this->select[] = $fields;
		}
		return $this;
	}

	//prepares the where properties
	public function where($properties , $values = null)
	{
		if(is_array($properties))
		{
			foreach($properties as $keys=>$values)
			{
				$this->where[$keys] = $this->security->clean($values);
			}
		}
		else
		{
			$this->where[$properties] = $this->security->clean($values);
		}
		return $this;
	}

	//prepares the table name
	public function table($table_name)
	{
		$this->table = $this->security->clean($table_name);
		return $this;
	}
	
	//sets the properties for offset and limit for fetching record from database
	public function limit($offset,$limit=null)
	{
		$this->offset = $offset;
		$this->limit = $limit;
		return $this;
	}
	
	//sets the order by clause
	public function order_by($order_by_field,$order_by_mode)
	{
		$this->order_by = $order_by_field;
		$this->order_by_mode = $order_by_mode;
		return $this;
	}
	
	//sets the group by class
	public function group_by($field)
	{
		$this->group_by = $field;
		return $this;
	}
	
	//sets the values for how much cycles will be called for multi_update and multi_insert methods
	public function cycles($cycles=0)
	{
		$this->cycles = $cycles;
		return $this;
	}

	//prepares the get query all units included in this query
	protected function get()
	{
		$this->query = "SELECT";
		
		//check if select fields are present
		if(isset($this->select))
		{
			$this->query .= " `" . implode("`,`",$this->select) . "` ";
		}
		else
		{	
			//otherwise standard * is used
			$this->query .= " * ";	
		}

		$this->query .= "FROM `" . $this->table . "`";
		
		//check for where properties
		if(isset($this->where))
		{
			$this->query .= " WHERE";
			
			foreach($this->where as $keys=>$values)
			{
				$this->query .= " `" . $this->security->clean($keys) ."` = '". $this->security->clean($values) . "' AND";
			}
			
			//removes the extra 'and' from the end of query
			$this->query = rtrim($this->query," AND");
			
		}
		
		//removes the extra "," from the end if only OFFSET is applied
		$this->query = rtrim($this->query,",");
		
		//check for group by
		if($this->group_by)
		{
			$this->query .= " GROUP BY `" .$this->group_by . "`";
		}
		
		//check for order_by
		if($this->order_by)
		{
			$this->query .= " ORDER BY `" .$this->order_by . "` " . $this->order_by_mode;
		}
		
		//check for limits
		if(isset($this->offset) && $this->offset!=="" || isset($this->limit))
		{
			$this->query .= " LIMIT ".$this->offset.",".$this->limit;
		}
		
		
		//echo $this->query;
		//transfers the result to the result property
		$this->result = $this->con->query($this->query);
		
		//sets the num_rows
		$this->num_rows = $this->result->num_rows;
		
		//some debugging
		if($this->con->errno)
		{
			$this->debug();
		}
		
		//resets all the values hold by current vars
		$this->reset();
		
	}

	//returns the num rows returned by the last select query.
	public function num_rows()
	{
		return $this->num_rows;
	}

	//returns the affected rows from a insert, update or delete query
	public function affected_rows()
	{
		return $this->con->affected_rows;
	}

	//returns the last inserted id from an insert query
	public function insert_id()
	{
		return $this->con->insert_id;
	}

	//sends the returned data as array
	public function get_array()
	{
		$this->get();

		$result = $this->result;
		
		if($this->num_rows()>0)
		{
			while($fetch_array = $result->fetch_assoc())
			{
				$this->get_array[] = $fetch_array;
			}
		}
		else
		{
			$this->get_array = null;	
		}
		
		//frees the memory
		$this->result->free();
		
		//returns the result set as an array
		return $this->get_array;

	}

	//sends the returned data as object
	public function get_object()
	{
		
		$this->get();
		
		$result = $this->result;
		
		if($this->num_rows()>0)
		{
			while($fetch_object = $result->fetch_object())
			{
				$this->get_object[] = $fetch_object;
			}
		}
		else
		{
			$this->get_object = null;	
		}
		
		//frees the memory
		$this->result->free();
		
		//returns the result set as an object
		return $this->get_object;
		
	}

	//inserts the record into the database
	public function insert($data,$options=null)
	{
		if(isset($this->cycles))
		{
			return $this->multi_insert($data,$options);
		}
		else
		{
			return $this->single_insert($data);
		}
	}
	
	//simple insert is for inserting 1 row only in db
	protected function single_insert($data)
	{
		$this->query = "INSERT INTO " . $this->table;
			
		//do clean all values before insert
		foreach($data as $keys=>$values)
		{
			$data[$keys] = $this->security->clean($values);
		}
		
		$keys = implode("`,`",array_keys($data));
		
		$values = implode("','",array_values($data));
		
		$this->query .= "(`" . $keys . "`) VALUES ('" . $values . "')";

		$this->result = $this->con->query($this->query);
		
		//debug
		if($this->con->errno)
		{
			$this->debug();
		}
		
		if($this->affected_rows()>0)
		{
			//resets the all values holded by current variables
			$this->reset();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//inserts the multi records into the database according to the arrays given in the insert data
	protected function multi_insert($data,$options = array())
	{
		//checks if single is present or not
		if(isset($options['single']))
		{
			$singles = str_replace("|",",",$options['single']);
		}
		else
		{
			$singles = null;
		}
		
		//a simple check if multi is present or not
		$multiples = isset($options['multi']) ? str_replace("|",",",$options['multi']) : null;
		
		//starts the query
		$this->query = "INSERT INTO `" . $this->table ."` (";
		
		//enters the table fields for query
		$this->query .= ltrim(''.$singles . "," . $multiples . ") VALUES ",",");
		
		//initialize the multi and single value's arrays
		$multi = array();
		$single = array();
		
		$multi = explode(",",$multiples);
		$single = explode(",",$singles);
		
		//prepares the rest of the query 
		for($i=0;$i<=$this->cycles-1;$i++)
		{
			$this->query .= "(";
			
			//write the single values
			if($singles==!null)
			{
				foreach($single as $key=>$val)
				{
					$this->query .= "'" . $this->security->clean($data[$val]) . "',";
				}
			}
			else
			{
				$this->query .= "";
			}
			
			//writes the multi values
			foreach($multi as $key=>$val)
			{
				$this->query .= "'". $this->security->clean($data[$val][$i])."',";
			}
			
			$this->query .= "),";
		}
		
		//remove extra comma from the end of the query
		$this->query = rtrim($this->query,",");
		
		//removes the extra commas from the values brackets
		$this->query = $this->remove_commas($this->query);
		
		//finally performs the mysqli_query;
		$this->result = $this->con->query($this->query);
		
		//debug
		if($this->con->errno)
		{
			$this->debug();
		}
		
		//checks if affected rows present then return true else return false
		if($this->affected_rows()>0)
		{
			//resets the all values holded by current variables
			$this->reset();
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	//updates the record
	public function update($data,$options=null)
	{
		if(isset($this->cycles))
		{
			return $this->multi_update($data,$options);
		}
		else
		{
			return $this->single_update($data);
		}
	}
	
	//updates the single record from db
	protected function single_update($data)
	{
		$this->query = "UPDATE " . $this->table . " SET ";
		
		//do clean all values before insert
		foreach($data as $keys=>$values)
		{
			$this->query .= "`" . $keys . "` = '" . $this->security->clean($values) . "' , ";
		}
		$this->query = rtrim($this->query," , ");
		
		$this->query .= " WHERE";
		
		foreach($this->where as $keys=>$values)
		{
			$this->query .= " `" . $this->security->clean($keys) ."` = '". $this->security->clean($values) . "' AND";	
		}
		
		$this->query = rtrim($this->query," AND");
		
		$this->result = $this->con->query($this->query);
		
		//debug
		if($this->con->errno)
		{
			$this->debug();
		}
		
		if($this->affected_rows()>0)
		{
			//resets the all values Holden by current variables
			$this->reset();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//update multiple records from database currently in progress
	protected function multi_update($data,$options)
	{
		/**********update for singles************/
		if($options['single']==null)
		{
			//do nothing
		}
		else
		{
			$single_feilds = explode("|",$options['single']);
			$num_single_feilds = count($single_feilds);
			
			$this->query .= "UPDATE " . $this->table . " SET ";
			
			//do clean all values before insert
			for($i=0;$i<=$num_single_feilds-1;$i++)
			{
				$this->query .= "`" . $single_feilds[$i] . "` = '" . $this->security->clean($data[$single_feilds[$i]]) . "' , ";	
			}
			
			$this->query = rtrim($this->query," , ");
			
			$this->query .= " WHERE";
			
			foreach($options['single_id'] as $keys=>$values)
			{
				$this->query .= " `" . $this->security->clean($keys) ."` = '". $this->security->clean($values) . "' AND";
			}
				
			$this->query = rtrim($this->query," AND");
			
			$this->query .= ";";
		}
		
		/**********update for singles end************/
		
		/**********update for multiples************/
		
		foreach($data[$options['multi_id']] as $key=>$id)
		{
			$ids[] = $this->security->clean($id);
		}
		
		$ids = array_values($ids);
		
		$ids = implode(',',$ids);
		/**
		*make separate query of singles and separate query for every multi 
		*
		**/
		$multi_feilds = explode("|",$options['multi']);
		$num_multi_feilds = count($multi_feilds);
		
		for($i=0;$i<=$num_multi_feilds-1;$i++)
		{
			$this->query .= "UPDATE `".$this->table."` SET `".$multi_feilds[$i]."` = CASE `".$options['multi_id']."` ";
			
			for ($a=0;$a<=$this->cycles-1;$a++)
			{
				$this->query .= "WHEN '".$this->security->clean($data[$options['multi_id']][$a])."' THEN '".$this->security->clean($data[$multi_feilds[$i]][$a])."' ";
			}
			$this->query .= "END WHERE `".$options['multi_id']."` IN (".$ids.");";
		}
		
		//performs the final query and transfers the results to the result property
		$this->result = $this->con->multi_query($this->query);
		
		//debug
		if($this->con->errno)
		{
			$this->debug();
		}

		if($this->affected_rows()>0)
		{
			//resets the all values Holden by current variables
			$this->reset();
			return true;
		}else{
			return false;
		}
	}
	
	//deletes the records or empties the table
	public function delete()
	{
		if(isset($this->where))
		{
			$this->query = "DELETE FROM " . $this->table . " WHERE ";
			
			foreach($this->where as $keys=>$values)
			{
				$this->query .= " `" . $this->security->clean($keys) ."` = '". $this->security->clean($values) . "' AND";
				$this->query = rtrim($this->query," AND");
			}
		}
		else
		{
			$this->query = "TRUNCATE TABLE " . $this->table;
		}
		
		$this->result = $this->con->query($this->query);
		
		if($this->affected_rows()>0)
		{
			//resets the all values Holden by current variables
			$this->reset();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//removes the extra commas from query
	private function remove_commas($query)
	{
		$temp = explode(")",$query);
		
		$first = $temp[0].")";
		
		unset($temp[0]);
		
		$q = '';
		
		foreach($temp as $key)
		{
			$q .= substr($key,0,-1).")";
		}
		return substr($first.$q,0,-1);
	}
	
	//debug
	private function debug()
	{
		echo '<pre>';
		echo "<div class=\"\" style=\"border:1px solid #cccccc;margin:5px;color:#f00;font-family:corbel;padding:5px;\">";
		
			echo "<div style=\"background:#f00;color:#fff;clear:both;padding:5px;\">";
				echo "<span style=\"font-size:20px;font-weight:bold;\">Error</span>";
			echo "</div>";
			
			echo "<span>You have an error in your SQL near </span>\n";
			
			echo "<strong style=\"color:#000;\">".$this->query."</strong>\n";
			
			echo "<span>".$this->con->error."</span>";
		echo "</div>";
		echo '</pre>';
		exit();
	}
	
	//magic __destruct()
	public function __destruct()
	{
		$this->con->close();
	}
}
