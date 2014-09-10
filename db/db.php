<?php
/**
*********************Database Controller library*******************
*-----includes-----
*-----> DB Controller
*-----> other DB utilities
*-----@author------
*------*ahmedali5530*------
*------ version 2.0--------
**/

Class DB extends Loader{
	
	var $where = array();
	var $select = array();
	var $aggregate = array();
	var $table = array();
	var $joins = array();
	var $like = array();
	var $offset = false;
	var	$limit = null;
	var	$order_by = false;
	var	$order_by_mode = false;
	var	$group_by = false;
	var	$having = false;
	var	$cycles = false;
	var $num_rows = '';
	var $insert_id = '';
	var $result = array();
	var $get_array = array();
	var $get_object = array();
	var $query = '';
	
	/**
	* Regular methods starts here-------------------------------------------------------------------------
	**/
	
	//constructor
	public function __construct($host=null,$user=null,$pw=null,$db=null)
	{
		if($host==null && $user==null && $pw==null && $db==null){
			$this->con = new Mysqli(self::DB_HOST,self::DB_USER,self::DB_PASSWORD,self::DB_NAME);
		}else{
			$this->con = new Mysqli($host,$user,$pw,$db);
		}
		if ($this->con->connect_errno) {
			die("Failed to connect to Server: (" . $this->con->connect_errno . ") " . $this->con->connect_error);
		}
		$this->con->set_charset('utf8');
		
		self::$instance = $this;
	}
	
	public static function get_instance()
	{
		return self::$instance;
	}
	

	//resets the selection/write variables
	public function reset()
	{
		$this->where = array();
		$this->select = array();
		$this->aggregate = array();
		$this->table = array();
		$this->joins = array();
		$this->like = array();
		$this->get_array = array();
		$this->get_object = array();
		$this->offset = false;
		$this->limit = null;
		$this->order_by = false;
		$this->order_by_mode = false;
		$this->group_by = false;
		$this->having = false;
	}
	
	//function can be DISTINCT, MIN, MAX, SUM, COUNT
	public function select_aggregate($function='DISTINCT',$field='*',$alias=null)
	{
		$stmt = $function.'(`'.$this->check_alias($field).'`)';
		if($alias !== null)
		{
			$stmt .= ' as `'.$alias.'`';
		}
		$this->aggregate[] = $stmt;
		return $this;
	}

	//prepares the select fields
	public function select($fields)
	{
		if(is_array($fields))
		{
			foreach($fields as $field)
			{
				$this->select[] = $this->check_alias($field);
			}
		}else
		{
			if(strpos($fields,','))
			{
				//it contains more than on select field
				//explode the string and convert it to array
				foreach(explode(',',$fields) as $field)
				{
					$this->select[] = $this->check_alias($field);
				}
			}else{
				$this->select[] = $this->check_alias($fields);
			}
		}
		return $this;
	}

	//prepares the where properties
	//supported types are AND and OR
	//has support for conditional operators i.e. =,<,>,<=,>=,!=
	//added support for comparisons between fields
	public function where($properties , $values = null,$operator = '=',$type = 'AND',$val_quotes = "'")
	{
		if(is_array($properties))
		{
			foreach($properties as $key=>$val)
			{
				if(isset($this->where) && count($this->where)>0)
				{
					$this->where[] = "".$type." `".$this->clean($this->check_alias($key))."`".$operator.$val_quotes.$this->clean($val).$val_quotes." ";
				}
				else
				{
					$this->where[] = "`".$this->clean($this->check_alias($key))."`".$operator.$val_quotes.$this->clean($val).$val_quotes." ";
				}
			}
		}
		else
		{
			if(isset($this->where) && count($this->where)>0)
			{
				$this->where[] = "".$type." `".$this->clean($this->check_alias($properties))."`".$operator.$val_quotes.$this->clean($values).$val_quotes." ";
			}
			else
			{
				$this->where[] = "`".$this->clean($this->check_alias($properties))."`".$operator.$val_quotes.$this->clean($values).$val_quotes." ";
			}
		}
		return $this;
	}
	
	//make the query string for like operator
	//supported position are before(%a), after(a%), both(%a%) and none(a)
	public function like($properties , $values = null,$position='both',$type='AND')
	{
		$this->like = array();
		if(is_array($properties))
		{
			foreach($properties as $key=>$val)
			{
				$this->like[] = $this->prepare_like_positions($key,$val,$position,$type);
			}
		}
		else
		{
			$this->like[] = $this->prepare_like_positions($properties,$values,$position,$type);
		}
		return $this;
	}
	
	private function prepare_like_positions($key,$val,$position='both',$type='AND')
	{
		if(isset($this->like) && count($this->like)>0)
		{
			if($position=='both')
			{
				return $type." `".$this->clean($this->check_alias($key))."` LIKE '%".$this->clean($val)."%' ";
			}elseif($position=='before')
			{
				return $type." `".$this->clean($this->check_alias($key))."` LIKE '%".$this->clean($val)."' ";
			}elseif($position=='after')
			{
				return $type." `".$this->clean($this->check_alias($key))."` LIKE '".$this->clean($val)."%' ";
			}else
			{
				return $type." `".$this->clean($this->check_alias($key))."` LIKE '".$this->clean($val)."' ";
			}
		}
		else
		{
			if($position=='both')
			{
				return "`".$this->clean($this->check_alias($key))."` LIKE '%".$this->clean($val)."%' ";
			}elseif($position=='before')
			{
				return "`".$this->clean($this->check_alias($key))."` LIKE '%".$this->clean($val)."' ";
			}elseif($position=='after')
			{
				return "`".$this->clean($this->check_alias($key))."` LIKE '".$this->clean($val)."%' ";
			}else
			{
				return "`".$this->clean($this->check_alias($key))."` LIKE '".$this->clean($val)."' ";
			}
		}
	}

	//prepares the table name
	public function table($table_name)
	{
		if(isset($this->table) && count($this->table)>0)
		{
			$this->table[] = ", `".$this->clean($this->check_alias(SELF::DB_PREFIX.$table_name))."`";
		}
		else
		{
			$this->table[] = "`".$this->clean($this->check_alias(SELF::DB_PREFIX.$table_name))."`";
		}
		return $this;
	}
	
	//alias of table function
	public function from($table_name)
	{
		if(isset($this->table) && count($this->table)>0)
		{
			$this->table[] = ", `".$this->clean($this->check_alias(SELF::DB_PREFIX.$table_name))."`";
		}
		else
		{
			$this->table[] = "`".$this->clean($this->check_alias(SELF::DB_PREFIX.$table_name))."`";
		}
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
	public function order_by($order_by_field,$order_by_mode='ASC')
	{
		$this->order_by = $this->check_alias($order_by_field);
		$this->order_by_mode = $order_by_mode;
		return $this;
	}
	
	//sets the group by class
	public function group_by($field)
	{
		$this->group_by = $this->check_alias($field);
		return $this;
	}
	
	//defines the join
	//supported join types are
	// left, right, inner,cross
	//syntax
	//join('t1 as ab','ab.school_id=t2.school_id','JOIN TYPE')
	public function join($table_name,$condition,$join_type='INNER')
	{
		$this->joins[] = $join_type.' JOIN `'.$this->check_alias(SELF::DB_PREFIX.$table_name).'` ON `'.SELF::DB_PREFIX.str_replace('=','`=`'.SELF::DB_PREFIX.'',str_replace('.','`.`',$condition)).'` ';
		return $this;
	}
	
	//sets the having condition
	public function having($having_condition)
	{
		$this->having = $having_condition;
		return $this;
	}
	
	//sets the values for how much cycles will be called for multi_update and multi_insert methods
	public function cycles($cycles=0)
	{
		$this->cycles = $cycles;
		return $this;
	}

	//prepares the get query all units included in this query
	/*
	-	orientation of select query
	-	// Write the "select" portion of the query
	-	// Write the "FROM" portion of the query
	-	// Write the "JOIN" portion of the query
	-	// Write the "WHERE" portion of the query
	-	// Write the "LIKE" portion of the query
	-	// Write the "GROUP BY" portion of the query
	-	// Write the "HAVING" portion of the query
	-	// Write the "ORDER BY" portion of the query
	-	// Write the "LIMIT" portion of the query
	*/
	public function get()
	{
		$this->query = "SELECT";
		
		//check for aggregate functions
		if(!empty($this->aggregate))
		{
			$this->query .= ' '.implode(' ,',$this->aggregate);
			$this->query .= ',';
		}
		//check if select fields are present
		
		if(!empty($this->select))
		{
			$this->query .= " `" . implode("`,`",$this->select) . "`\n";
		}
		else
		{
			$this->query = rtrim($this->query,",");
			if(!empty($this->aggregate))
			{
				
			}else{
				//otherwise standard * is used
				$this->query .= " * ";	
			}
		}

		$this->query .= "FROM (" . implode('',$this->table) . ")\n";
		
		//process joins section here
		
		if(!empty($this->joins))
		{
			$this->query .= implode("\n",$this->joins);
		}
		
		//check for where properties
		if(!empty($this->where))
		{
			$this->query .= " WHERE ";
			
			$this->query .= implode("\n",$this->where);
		}
		
		//check for like properties
		if(!empty($this->like) && !empty($this->where))
		{
			$this->query .= " AND ";
			
			$this->query .= implode("\n",$this->like);
		}
		elseif(!empty($this->like))
		{
			$this->query .= " WHERE ";
			
			$this->query .= implode("\n",$this->like);
		}
		
		//check for group by
		if($this->group_by !== false)
		{
			$this->query .= "GROUP BY `" .$this->group_by . "` \n";
		}
		
		//check for group by
		if($this->having !== false)
		{
			$this->query .= "HAVING " .$this->having . " \n";
		}
		
		//check for order_by
		if($this->order_by !==false)
		{
			$this->query .= "ORDER BY `" .$this->order_by . "` " . $this->order_by_mode;
		}
		
		//check for limits
		if($this->offset !==false)
		{
			$this->query .= " LIMIT ".$this->offset;
		}
		if($this->limit !==null)
		{
			$this->query .= ",".$this->limit;
		}
		
		//echo $this->query;
		//transfers the result to the result property
		$this->result = $this->con->query($this->query);
		
		//sets the num_rows
		$this->num_rows = $this->result->num_rows;
		
		//some debugging
		if($this->con->errno)
		{
			return $this->debug();
		}
		//return the result set
		
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
	
		if($this->num_rows()>0)
		{
			while($fetch_array = $this->result->fetch_assoc())
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

		if($this->num_rows()>0)
		{
			while($fetch_object = $this->result->fetch_object())
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
		
		return $this->get_object;
	}
	
	//alias of get_row_object($offset)
	public function get_row($offset=0)
	{
		return $this->get_row_object($offset);
	}
	
	//gets the single row from database
	public function get_row_array($offset = 0)
	{
		$this->get();
		
		$result = $this->result;
		
		if($this->num_rows()>0)
		{
			while($fetch_assoc = $result->fetch_assoc())
			{
				$this->get_array[] = $fetch_assoc;
			}
			
			if(array_key_exists($offset,$this->get_array))
			{
				//frees the memory
				$this->result->free();
			
				return $this->get_array[$offset];
			}
		}
		else
		{
			$this->get_array = null;	
		}
		
		
		$offset = 0;
		$this->result->free();
	
		return $this->get_array[$offset];
	}
	
	//gets the single row from database
	public function get_row_object($offset = 0)
	{
		$this->get();
		
		$result = $this->result;
		
		if($this->num_rows()>0)
		{
			while($fetch_object = $result->fetch_object())
			{
				$this->get_object[] = $fetch_object;
			}
			
			if(array_key_exists($offset,$this->get_object))
			{
				//frees the memory
				$this->result->free();
				
				return $this->get_object[$offset];
			}
		}
		else
		{
			$this->get_object = null;	
		}
		
		
		$offset = 0;
		$this->result->free();
		
		return $this->get_object[$offset];
	}
	
	//counts the result
	//alias of num_rows()
	public function get_count()
	{
		$this->get();
		
		return $this->num_rows();
	}
	
	//counts the result
	//alias of num_rows()
	public function count_all()
	{
		$this->get();
		
		return $this->num_rows();
	}
		
	//checks either a value is unique of not
	public function unique()
	{
		//$this->result;
		if($this->get_array()==null)
		{
			return true;
		}else
		{
			return false;
		}
	}

	//inserts the record into the database
	public function insert($data,$options=null)
	{
		if(isset($this->cycles) AND $this->cycles !==false)
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
		$this->query = "INSERT INTO " . implode('',$this->table) . " ";
			
		//do clean all values before insert
		foreach($data as $keys=>$values)
		{
			$data[$keys] = $this->clean($values);
		}
		
		$keys = implode("`,`",array_keys($data));
		
		$values = implode("','",array_values($data));
		
		$this->query .= "(`" . $keys . "`) VALUES ('" . $values . "')";
		
		//echo $this->query;
		$this->result = $this->con->query($this->query);
		
		//debug
		if($this->con->errno)
		{
			return $this->debug();
		}
		
		//resets the all values holded by current variables
		$this->reset();
		
		if($this->affected_rows()>0)
		{
			
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
		$this->query = "INSERT INTO " . implode('',$this->table) ." (";
		
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
					$this->query .= "'" . $this->clean($data[$val]) . "',";
				}
			}
			else
			{
				$this->query .= "";
			}
			
			//writes the multi values
			foreach($multi as $key=>$val)
			{
				$this->query .= "'". $this->clean($data[$val][$i])."',";
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
			return $this->debug();
		}
		
		//resets the all values holded by current variables
		$this->reset();
			
		//checks if affected rows present then return true else return false
		if($this->affected_rows()>0)
		{
			
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
		if(isset($this->cycles) AND $this->cycles !==false)
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
		$this->query = "UPDATE " . implode('',$this->table) . " SET ";
		
		//do clean all values before insert
		foreach($data as $keys=>$values)
		{
			$this->query .= "`" . $keys . "` = '" . $this->clean($values) . "' , ";
		}
		$this->query = rtrim($this->query," , ");
		
		//check for where properties
		if(!empty($this->where))
		{
			$this->query .= " WHERE ";
			
			$this->query .= implode(' ',$this->where);
		}
		
		//echo $this->query;
		
		$this->result = $this->con->query($this->query);
		
		//debug
		if($this->con->errno)
		{
			return $this->debug();
		}
		
		//resets the all values Holden by current variables
		$this->reset();
		
		if($this->affected_rows()>0)
		{
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//update multiple records from database
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
			
			$this->query .= "UPDATE " . implode('',$this->table) . " SET ";
			
			//do clean all values before insert
			for($i=0;$i<=$num_single_feilds-1;$i++)
			{
				$this->query .= "`" . $single_feilds[$i] . "` = '" . $this->clean($data[$single_feilds[$i]]) . "' , ";	
			}
			
			$this->query = rtrim($this->query," , ");
			
			$this->query .= " WHERE";
			
			foreach($options['single_id'] as $keys=>$values)
			{
				$this->query .= " `" . $this->clean($keys) ."` = '". $this->clean($values) . "' AND";
			}
				
			$this->query = rtrim($this->query," AND");
			
			$this->query .= ";";
		}
		
		/**********update for singles end************/
		
		/**********update for multiples**************/
		
		foreach($data[$options['multi_id']] as $key=>$id)
		{
			$ids[] = $this->clean($id);
		}
		
		$ids = array_values($ids);
		
		$ids = implode(',',$ids);
		/**
		*make separate query of singles and separate query for every multi 
		**/
		$multi_feilds = explode("|",$options['multi']);
		$num_multi_feilds = count($multi_feilds);
		
		for($i=0;$i<=$num_multi_feilds-1;$i++)
		{
			$this->query .= "UPDATE ".implode('',$this->table)." SET `".$multi_feilds[$i]."` = CASE `".$options['multi_id']."` ";
			
			for ($a=0;$a<=$this->cycles-1;$a++)
			{
				$this->query .= "WHEN '".$this->clean($data[$options['multi_id']][$a])."' THEN '".$this->clean($data[$multi_feilds[$i]][$a])."' ";
			}
			$this->query .= "END WHERE `".$options['multi_id']."` IN (".$ids.");";
		}
		
		//performs the final query and transfers the results to the result property
		$this->result = $this->con->multi_query($this->query);
		
		//debug
		if($this->con->errno)
		{
			return $this->debug();
		}
		
		//resets the all values Holden by current variables
		$this->reset();

		if($this->affected_rows()>0)
		{	
			return true;
		}else{
			return false;
		}
	}
	
	//deletes the records or empties the table
	public function delete()
	{
		if(!empty($this->where))
		{
			$this->query = "DELETE FROM " . implode('',$this->table) . " WHERE ";
			
			//check for where properties
			
			$this->query .= implode(' ',$this->where);
		}
		else
		{
			$this->query = "DELETE FROM " . implode('',$this->table);
		}
		
		//check for like properties
		if(!empty($this->like) && !empty($this->where))
		{
			$this->query .= " AND ";
			
			$this->query .= implode(' ',$this->like);
		}
		elseif(!empty($this->like))
		{
			$this->query .= " WHERE ";
			
			$this->query .= implode(' ',$this->like);
		}
		
		//check for order_by
		if($this->order_by !==false)
		{
			$this->query .= "ORDER BY `" .$this->order_by . "` " . $this->order_by_mode;
		}
		
		//check for limits
		if($this->offset !==false)
		{
			$this->query .= " LIMIT ".$this->offset;
		}

		//return $this->query;
		$this->result = $this->con->query($this->query);
		
		//debug
		if($this->con->errno)
		{
			return $this->debug();
		}
		
		//resets the all values Holden by current variables
		$this->reset();
			
		if($this->affected_rows()>0)
		{	
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//deletes the records or empties the table
	public function truncate()
	{
		$this->query = "TRUNCATE " . implode('',$this->table);
			
		$this->result = $this->con->query($this->query);
		
		//debug
		if($this->con->errno)
		{
			return $this->debug();
		}
		
		//resets the all values Holden by current variables
		$this->reset();
		
		if($this->affected_rows()>0)
		{	
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
	
	//check whether there is a dot in the fields or not
	//dot(.) relates to table.field 
	private function check_alias($key)
	{
		if(strpos($key,'.'))
		{
			$key = str_replace('.','`.`',SELF::DB_PREFIX.$key);
		}
		
		if(strpos($key,'as'))
		{
			$key = str_replace(' as ','` as `',$key);
		}
		
		return $key;
	}
	
	//do the mysqli_real_escape_string
	public function clean($data)
	{
		$data = filter_var($data, FILTER_SANITIZE_STRING);
		return $this->con->real_escape_string(trim($data));
	}
	
	//debug
	private function debug()
	{
		if(ENVIRONMENT == 'development'){
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
		}else{
			echo 'A System Error Occured';
		}
	}
	
	//magic __destruct()
	public function __destruct()
	{
		$this->con->close();
	}
}
