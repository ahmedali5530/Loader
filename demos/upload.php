<!DOCTYPE html>
<pre>
<?php
function __autoload($file){
	require_once('../Libraries/'.$file.'/'.$file.".php");
}

	$upload = new Upload();
	//optional parameter can be passed here
	$options = array(
		'path' => '../sdfs/',
		'type' => 'text/plain',
		'size' => 200000,
		'random_name' => true,
	);
	if(isset($_POST['name'])){
		//$options is optional you can upload file without passing this parameter
		$a = $upload->upload('city',$options);
		print_r($a);
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
		<?php echo $upload->show_message('city');?>
		<input type="file" name="city"><hr/>
		<button type="submit">Submit</button>
		</form>
	</div>
</body>
</html>
