<!DOCTYPE html>
<pre>
<?php
function __autoload($file){
	require_once($file.".php");
}

	$db = new DB();
	$db->table('test');
	$get = $db->get_array();
	$db->table('test');
	$db->cycles(count($get));
	$options = array(
		'multi_id'  => 'test_id',
		'single_id' => array('test_id'=>2),
		'single'    => 'name',
		'multi'     => 'age|city',
	);
	if(isset($_POST['test_id'][0])){
		$update = $db->update($_POST,$options);
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
		<input type="text" name="name" placeholder="enter name" value="<?php echo $get[1]['name'];?>"><br/>
		<?php foreach($get as $key):?>
		<input type="hidden" name="test_id[]" value="<?php echo $key['test_id'];?>">
		<input type="text" name="age[]" placeholder="enter age" value="<?php echo $key['age'];?>"><br/>
		<input type="text" name="city[]" placeholder="enter city" value="<?php echo $key['city'];?>"><hr/>
		<?php endforeach;?>
		<button type="submit">Submit</button>
		</form>
	</div>
</body>
</html>
