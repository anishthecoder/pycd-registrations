<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('includes/common.php');

?>
<?=Template::printDocType()?>
<html <?=Template::getHtmlAttributes()?>>
<head>
		<link rel="stylesheet" type="text/css" href="lib/jquery/smoke/smoke-dark.css" />
		<script src="http://code.jquery.com/jquery-2.1.0.min.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
		<script src="lib/jquery/smoke/smoke.min.js" type="text/javascript"></script>
		<script type="text/javascript">
    $(document).ready(function(){
      smoke.alert('done');
    });
  </script>
</head>

<body>
  <div id="main"></div>
</body>
</html>