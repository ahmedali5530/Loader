<!DOCTYPE html>
<pre>
<?php
function __autoload($file){
	require_once($file.".php");
}

	$db = new DB();
	$db->table('test');
	if(isset($_POST['name'])){
		$insert = $db->insert($_POST);
		print_r($insert);
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
		<input type="text" name="name" placeholder="enter name"><br/>
		<input type="text" name="age" placeholder="enter age"><br/>
		<input type="text" name="city" placeholder="enter city"><hr/>
		<button type="submit">Submit</button>
		</form>
	</div>
</body>
</html>
