<!DOCTYPE html>
<pre>
<?php
function __autoload($file){
	require_once('libraries/'.$file.".php");
}
$page = isset($_GET['page']) ? ((int) $_GET['page']) : 0;
$db = new DB;
$db->table('test');
$db->limit($page*5,5);
$get = $db->get_array();
print_r($get);

//configuration of pagination is little bit confusing,
//first make the new instance of Pagination class
//set the Current page number the setCurrent() method
//then set the Records per page for KHANA PURI, it should be less than the limit applied in the get query
//after that set the total number of records by setTotal() method
//finally call the create_links() to render pagination
$pagination = new Pagination();
$pagination->setCurrent($page);
//should be less than limit given in the get query;
$pagination->setRPP(1);
$pagination->setTotal(count($get));


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
		<?php echo $pagination->create_links();?>
		</div>
	</div>
</body>
</html>
