<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-12-13 14:23:02, compiled from ../../app/web/template/admin_index.tpl.html */ ?>
<!DOCTYPE html>
<html lang="en">
<body>

		<?php require( TEMPLATE_PATH . 'header.tpl.html'); ?>

		<?php include('../../app/data/template_c/nav.tpl.php'); ?>
		

		<div class="container-fluid">
			<div class="row-fluid">
				
			<?php require( TEMPLATE_PATH . 'leftmenu.tpl.html'); ?>
			
			<noscript>
				<div class="alert alert-block span10">
					<h4 class="alert-heading">Warning!</h4>
					<p>你需要开启<a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a>来使用这个网站.</p>
				</div>
			</noscript>
			
				<div id="content" class="span10">
					<!-- content starts -->
	    			<?php
					    	$main_file = TEMPLATE_PATH . $action.'.tpl.html';
						include( $main_file );
					?>
					
					

					<!-- content ends -->
				</div><!--/#content.span10-->
			</div><!--/fluid-row-->
				
			<hr>

			<div class="modal hide fade" id="myModal">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Settings</h3>
				</div>
				<div class="modal-body">
					<p>Here settings can be configured...</p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Close</a>
					<a href="#" class="btn btn-primary">Save changes</a>
				</div>
			</div>
		<?php require( TEMPLATE_PATH . 'foot.tpl.html'); ?>
		
	</div><!--/.fluid-container-->
<?php require( TEMPLATE_PATH . 'footjs.tpl.html'); ?>
	<?php if($js) foreach($js as $v){?>
		<script src="<?=$v?>"></script>
	<?php }?>		
</body>
</html>