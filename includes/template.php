<?php
/**
 * Template file to establish the framework for the remaining application.
 *
 */
require_once 'common.php';

class Template{


	public static function getDocType(){
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<?php
	}

	public static function beginHtml(){
		?>
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<?php
	}

	public static function getStyling(){
		?>
    <link rel="stylesheet" type="text/css" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" href="css/tipTip.css" />
    <style>
      <?php
      $sass = new SassParser();
      $css = $sass->toCss(BASE_PATH."/css/main.scss");
      ?>
      <?=$css?>
    </style>
		<?php
	}

	public static function getJs(){
		?>
		<script src="http://code.jquery.com/jquery-2.1.0.min.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="lib/jquery/jquery.infieldlabel.min.js" type="text/javascript"></script>
    <script src="lib/jquery/jquery.blockUI.js" type="text/javascript"></script>
    <script src="lib/jquery/jquery.tipTip.minified.js" type="text/javascript"></script>
		<?php
	}
}
