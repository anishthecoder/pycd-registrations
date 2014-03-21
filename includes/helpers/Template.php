<?php
/**
 * Template file to establish the view framework for the remaining application.
 * Also provides various helper functions associated with setting the views.
 *
 * All functions provided by this class will output an HTML DOM element(s).
 */
class Template{

  //----------------------------------------------------------------------------
  // Functions that directly print to the document
  //
	public static function printDocType(){
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<?php
	}

	public static function getHtmlAttributes(){
		return 'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"';
	}

	public static function printStyling(){
		?>
    <link rel="stylesheet" type="text/css" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" href="css/tipTip.css" />
		<link rel="stylesheet" type="text/css" href="lib/jquery/smoke/smoke-dark.css" />
		<link rel="stylesheet" type="text/css" href="lib/jquery/smoke/themes/dark.css" />
		<link rel="stylesheet" type="text/css" href="lib/jquery/growl/jquery.growl.css" />
    <?=Template::sassParseCss(BASE_PATH."/css/main.scss");?>
    <?php
	}

  public static function printHeader(){
    ?>
    <div id="header">
      <h1>
        <span><?=Config::$YEAR?></span>
        Competition Registrations
      </h1>
      <div id="sessionInfo">
        <span><?= Session::isSessionAdmin() ? '[Admin]' : ''?></span>
        <?=(Session::getActiveChurch() != NULL) ?
            Session::getActiveChurch()->getName(): ''?>
        <a href="#"
           onclick="javascript:sendRequest(
                     'Session',
                     '<?=SESSION::$ACTION_LOGOUT?>')">
          (Logout)
        </a>
      </div>
    </div>
    <?php
  }

	public static function printJs(){
		?>
		<script src="http://code.jquery.com/jquery-2.1.0.min.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="lib/jquery/jquery.infieldlabel.min.js" type="text/javascript"></script>
    <script src="lib/jquery/jquery.blockUI.js" type="text/javascript"></script>
    <script src="lib/jquery/jquery.tipTip.minified.js" type="text/javascript"></script>
		<script src="lib/jquery/smoke/smoke.min.js" type="text/javascript"></script>
		<script src="lib/jquery/growl/jquery.growl.js" type="text/javascript"></script>
    <?php include BASE_PATH.'/js/main.js.php' ?>
		<?php
	}

  /**
   * Parses the file indicated by the given filename from SASS/SCSS format to
   * regular CSS and returns the output wrapped in a style tag ready for
   * inclusion in the head of a document
   *
   * @param String $filename
   * @return String A string containing the SCSS/SASS file parsed to CSS
   *          surrounded by <style></style> tags.
   */
  public static function sassParseCss($filename){
    $sass = new SassParser();
    return '<style>'.$sass->toCss($filename).'</style>';
  }


}
