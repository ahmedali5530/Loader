<!DOCTYPE html>
<pre>
<?php
function __autoload($file){
	require_once($file.".php");
}

	$db = new DB();
	$db->table('test');
	$db->where('test_id',6);
	$get = $db->get_array();
	
	$db->table('test');
	$db->where('test_id',6);
	if(isset($_POST['name'])){
		$update = $db->update($_POST);
		print_r($update);
	}

?>
</pre>
<html>
<head>
	<title>Loader</title>
</head>
<body>
	<div class="container">
		<form method="post" enctype="multipart/form-data">
		<input type="text" name="name" placeholder="enter name" value="<?php echo $get[0]['name']?>"><br/>
		<input type="text" name="age" placeholder="enter age" value="<?php echo $get[0]['age']?>"><br/>
		<input type="text" name="city" placeholder="enter city" value="<?php echo $get[0]['city']?>"><hr/>
		<button type="submit">Submit</button>
		</form>
	</div>
</body>
</html>
