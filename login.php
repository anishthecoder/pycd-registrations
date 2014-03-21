<?php
// Log out any user that may have already been logged in.
session_start();
session_destroy();
require_once 'includes/common.php';
?>
<?=Template::printDocType()?>
<html <?=Template::getHtmlAttributes()?>>
<head>
	<?=Template::printStyling()?>
  <?=Template::sassParseCss(BASE_PATH."/css/login.scss");?>
	<?=Template::printJs()?>
	<script type="text/javascript">
		<?php
		$loadData = Controller::loadArray(
									Session::$ID,
									Session::$RENDER_LOGIN_FORM);
		?>
    $(document).ready(function(){
      $('#main').load(
				<?=CONTROLLER?>,
				<?=json_encode($loadData)?>,
				function(){
					$('#header').show();
					$('#main').show();
				});
    });
  </script>
</head>

<body id="loginpage">
  <?=Template::printHeader()?>
	<div id="main"></div>
</body>
</html>