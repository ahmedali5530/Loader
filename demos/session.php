<!DOCTYPE html>
<pre>
<?php
function __autoload($file){
	require_once('libraries/'.$file.'/'.$file.".php");
}
	$session=new CHttpSession;
	$session->open();
	$value1=$session['name1'];  // get session variable 'name1'
	$value2=$session['name2'];  // get session variable 'name2'
	foreach($session as $name=>$value){ // traverse all session variables
		echo $value;
	}
   $session['name3']=$value;  // set session variable 'name3'

?>
</pre>
<html>
<head>
	<title>Loader</title>
	<?php echo $db->css('css/bootstrap');?>
</head>
<body>
	<div class="container">
		<div class="pagination-centered block">
		<?php ?>
		</div>
	</div>
</body>
</html>
