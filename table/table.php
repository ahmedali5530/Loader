<?php
/**
*********************Table library*******************
*-----includes-----
*-----> Table
*-----> Makes the table at runtime.
*-----@author------
*------*ahmedali5530*------
**/

Class Table extends Loader
{

	public $rows = 0;
	public $cols = 0;
	public $classes = null;
	public $heading_class = null;
	public $headings = null;
	public $header = null;
	public $data = null;
	
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
	
	public function rows($rows)
	{
		$this->rows = (int)$rows;
		return $this;
	}
	
	public function cols($cols)
	{
		$this->cols = (int)$cols;
		return $this;
	}
	
	public function data($data)
	{
		$this->data = $data;
		return $this;
	}
	
	public function headings($headings,$align='center')
	{
		$this->headings = $headings;
		if($align=='center'){
			$this->heading_class = 'text-center';
		}elseif($align=='left'){
			$this->heading_class = 'text-left';
		}elseif($align=='right'){
			$this->heading_class = 'text-right';
		}elseif($align=='justify'){
			$this->heading_class = 'text-justify';
		}else{
			$this->heading_class = null;
		}
		return $this;
	}
	
	public function header($header)
	{
		$this->header = $header;
	}
	
	public function add_classes($classes)
	{
		$this->classes = $classes;
		return $this;
	}
	
	public function draw()
	{	
		$table = '';
		$table .= '<table class="table '.$this->classes.'">';
		//check if there is a header for table
		if(is_null($this->header)){
			
		}else{
			$table .= '<tr>';
			
				$table .= '<td colspan="'.count($this->headings).'"><h3 class="text-center">'.ucwords(str_replace('_',' ' ,$this->header)).'</h3></td>';
			
			$table .= '</tr>';
		}
		//check if there are headings
		if(is_null($this->headings)){
			
		}else{
			$table .= '<tr>';
			for($h=0;$h<=$this->cols-1;$h++){
				$table .= '<th class="'.$this->heading_class.'">'.ucwords(str_replace('_',' ' ,$this->headings[$h])).'</th>';
			}
			$table .= '</tr>';
		}
		
		if(is_null($this->data)){
			//draw empty table
			for($r=1;$r<=$this->rows;$r++){
				$table .= '<tr>';
				for($c=1;$c<=$this->cols;$c++){
					$table .= '<td>';
					$table .= '';
					$table .= '</td>';
				}
				$table .= '</tr>';
			}
		}else{
			//draw table using supplied data
			for($r=0;$r<=$this->rows-1;$r++){
				$table .= '<tr>';
				for($c=0;$c<=$this->cols-1;$c++){
					$table .= '<td>';
					$table .= $this->data[$r][$this->headings[$c]];
					$table .= '</td>';
				}
				$table .= '</tr>';
			}
		}
		$table .= '</table>';
		
		//resets the vars
		$this->_reset();
		//return table mark up
		return $table;
	}
	
	private function _reset()
	{
		unset($this->cols);
		unset($this->rows);
		unset($this->data);
		unset($this->headings);
		unset($this->header);
		unset($this->heading_class);
		unset($this->classes);
	}

}
