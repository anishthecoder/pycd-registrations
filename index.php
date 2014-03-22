<?php
require_once 'includes/common.php';
?>
<?=Template::printDocType()?>
<html <?=Template::getHtmlAttributes()?>>
<head>
	<?=Template::printStyling()?>
	<?=Template::printJs()?>
	<script type="text/javascript">
    $(document).ready(function(){
      sendRequest('Controller', '<?=Controller::$ACTION_INITIALIZE?>');
    });
  </script>
</head>

<body>
	<?=Template::printHeader()?>
  <div id="main"></div>
</body>
</html>