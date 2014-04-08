<!DOCTYPE html>
<pre>
<?php
function __autoload($file){
	require_once($file.".php");
}

	$db = new DB();
	$db->select('test_id|name');
	//you can pass select params like this
	//$db->select(array('age','city'));
	$db->table('test');
	$db->where('test_id',8);
	//you can pass where params like this
	//$db->where(array('test_id'=>8));
	//note: use only one method at a time for 1 query.
	//returns the results as an array
	$get_array = $db->get_array();
	print_r($get_array);
	//returns the results as an object
	$db->table('test');
	$db->where('test_id',8);
	$get_object = $db->get_object();
	print_r($get_object);

?>
</pre>
<html>
<head>
	<title>Loader</title>
</head>
<body>
	<div class="container">
	
		
		
	</div>
</body>
</html>
