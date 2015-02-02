<!DOCTYPE html>
<html>
<head>
	<title>Loader</title>
	<script type="text/javascript">var base_url = '<?php echo base_url();?>';</script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="header" content="Cache-Control: public">
    <?php echo $loader->loader->css('css/bootstrap');?>
    <?php echo $loader->loader->css('css/font-awesome');?>
    <?php echo $loader->loader->css('css/mediaelementplayer');?>
	<?php echo $loader->loader->js('js/jquery');?>
	<?php echo $loader->loader->js('js/ajinx');?>
	<?php echo $loader->loader->js('js/surah');?>
	<?php echo $loader->loader->js('js/bootstrap');?>
	<?php echo $loader->loader->js('js/history');?>
    <?php echo $loader->loader->js('js/infinite');?>
    <?php echo $loader->loader->js('js/pace');?>
    <?php echo $loader->loader->js('js/mediaelement-and-player');?>
<script type="text/javascript">
$(function(){
	$('[data-toggle=tooltip]').tooltip();
	//$(document).pjax('a[data-pjax]','#container');
});
	//was used for pjaxfying the application
	$(document).on('click','a',function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		var title = $(this).data('title');
		//check if url is # or not
		if(url == '#'){
			//do nothing
			//alert('THIS IS HASH');
		}else{
			History.pushState(null,title,url);
			//get the contents of #container and add it to the target action page's #container
			$('#container').load(url+' #container',function(){
				$('#container').html($(this).find('#container').html());
			});
			/*$.get(url,function(r){
				//$('#container').html($(r).children('#container').html());
				console.log($(r).find('#container'));
			});*/
			/* $.ajax({
				url : url+' #container',
				method : 'get',
				cache : 'cache-control:no-cache',
				success : function(result){
					$('#container').replaceWith(result);
				},
				error : function(xhr, textStatus, errorThrown){
					console.log(textStatus);
				}
			}); */
		}
	});
</script>
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="admin-logo" href="<?php echo base_url();?>admin"><img src="" width="40" height="40" alt="My Quran Live" title="My Quran Live"></a>
		</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle text-bold" data-toggle="dropdown"><i class="fa fa-reorder"></i> <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?php echo base_url();?>" data-pjax="true" data-title="Quran Home">Home</a></li>
						<li><a href="<?php echo base_url();?>admin/" data-pjax="true" data-title="Quran Admin">Admin</a></li>
						<li><a href="#" data-pjax="true">Admin</a></li>
					</ul>
				</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<div class="pace"></div>
<div id="container">
