<?php
if(isset($isAjax) && $isAjax == true):
	include $viewDir . $actionFile;
	return;
endif;
?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>	<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>	<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<?php include($viewDir . 'elements/head.php'); ?>
<body>
	<?php include($viewDir . 'elements/header.php'); ?>
	<?php include $viewDir . 'framework/flashmessage.php' ?>
	<div class="container">
		<?php include $viewDir . $actionFile; ?>
	</div>
	<?php include($viewDir . 'elements/footer.php'); ?>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?= $baseUrl; ?>scripts/libs/jquery-1.7.1.min.js"><\/script>')</script>
	<?php include($viewDir . 'framework/javascript.php'); ?>

	<a href="http://github.com/ppi/cfpManager" target="_blank" title="Fork Me On GitHub"><img style="position: fixed; top: 40px; left: 0; border: 0;" src="<?=$baseUrl;?>images/generic/fork-github.png" alt="Fork me on GitHub"></a>

</body>
</html>
