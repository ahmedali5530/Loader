<!DOCTYPE html>
<pre>
<?php
function __autoload($file){
	require_once('libraries/'.$file.'/'.$file.".php");
}
$page = isset($_GET['page']) ? ((int) $_GET['page']) : 0;
$db = new DB;
$db->table('test');
$get = $db->get_array();
print_r($get);
$table = new Table();
$table->headings(array('test_id','name','age','city'));
$table->add_classes('table-hover table-bordered');
$table->rows(count($get));
$table->cols(4);
$table->data($get);
$draw = $table->draw();
print_r($draw);

//draw multiple tables by initializing the class again
$table2 = new Table();
$table2->add_classes('table-hover table-bordered');
$table2->cols(3);
$table2->rows(7);
$draw = $table2->draw(); 
print_r($draw);
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
		<?php print_r($table->draw());?>
		</div>
	</div>
</body>
</html>
