<?php
/**
 * Provides various rendering capabilities for the application. Any function
 * that renders Javascript elements will be prefixed with 'js'.
 */
class Render{

  /** Class for any custom scripts that are being rendered. */
  public static $CUSTOM_SCRIPTS_CLASS = 'customScripts';
	public static $ID = 'Render';

  /**
   * Renders a single input text / password field with the associated label.
   *
   * @param String $label
   *        The string that will appear as the label of the field. The field
   *        name and id will be a lowercase version of the label stripped of
   *        spaces and appended with 'Fld'.
   * @param String $type
   *        The type attribute of the HTML input element that needs to be
   *        rendered.
   */
	public static function field($label, $type, $val="", $class=""){
    $fld = Render::labelToField($label);
		?>
			<label for="<?=$fld?>"><?=$label?></label>
			<input name="<?=$fld?>"
             type="<?=$type?>"
             id="<?=$fld?>"
						 class="<?=$class?>"
             value="<?=$val?>"/>
		<?php
	}

  /**
   * Converts any label to a field name/id by converting the label to lowercase,
   * removing all intermediate spaces, and appending the string 'Fld' to the
   * end.
   */
  public static function labelToField($label){
    $fld = str_replace(' ', '', $label);
		$fld = str_replace('.', '', $fld);
    return strtolower($fld).'Fld';
  }

  /**
   * Renders a single hidden field input with the provided field name and value
   * attributes.
   *
   * @param String $fieldname
   * @param String $value
   */
	public static function hiddenField($fieldname, $value){
		?>
		<input type="hidden" name="<?=$fieldname?>" value="<?=$value?>"
					 id="<?=$fieldname?>"/>
		<?php
	}

	/**
	 * Renders a 'back' to main menu link for the submenus.
	 */
	public static function htmlRenderMenuBackLink(){
		?>
		<li onclick="javascript:render(
							'#leftcolumn',
							'Controller',
							'<?=  Controller::$RENDER_MAIN_MENU?>')"
							class="backlink">
			<< back
		</li>
		<?php
	}

	/**
	 * Render a javascript tag binding hover functions for menu items.
	 * Because the menu items now get rendered various times during the app cycle,
	 * and the performance of the :hover is unstable, the .hover class needs to be
	 * manually added using binding to .hover() functions.
	 */
	public static function jsEnableMenuHover(){
		?>
		<script type="text/javascript">
			$('#leftcolumn li').each(function(){
				$(this).hover(function(){$(this).toggleClass('hover')});
			});
		</script>
		<?php
	}

	public static function jsFieldFocus($fieldLbl){
		$fieldId = Render::labelToField($fieldLbl);
    ?>
    <script type="text/javascript"
            class="<?=  Render::$CUSTOM_SCRIPTS_CLASS?>">
			$.unblockUI();
			$("#<?=$fieldId?>").focus();
		</script>
    <?php
	}

	/**
	 * For on-click event specs, helps to programatically take care of the
	 * arguments so one doesn't have to specify the delimiting quotes all the time.
	 *
	 * @param String $target
	 * @param String $controller
	 * @param String $renderAction
	 */
	public static function jsLineRender($target, $controller, $renderAction){
		?>render('<?=$target?>', '<?=$controller?>','<?=$renderAction?>');<?php
	}

  /**
   * Renders a javascript tag with a redirect call to the specified location.
   *
   * @param String $location Location to redirect to with respect to the base.
   */
  public static function jsRedirect($location){
    ?>
    <script type="text/javascript">
			window.location = '<?=$location?>';
		</script>
    <?php
  }


	public static function jsRender($target, $controller, $renderType){
		?>
    <script type="text/javascript"
            class="<?=  Render::$CUSTOM_SCRIPTS_CLASS?>">
		<?=  Render::jsLineRender($target, $controller, $renderType)?>
		</script>
		<?php
	}

  /**
   * Renders a alert.
   * @param String $message
   */
	public static function jsAlert($message){
		?>
    <script type="text/javascript"
            class="<?=  Render::$CUSTOM_SCRIPTS_CLASS?>">
			$.growl.error({message: '<?=  addcslashes($message, "'")?>'});
		</script>
		<?php
	}


	public static function jsInform($message){
		?>
    <script type="text/javascript"
            class="<?=  Render::$CUSTOM_SCRIPTS_CLASS?>">
			$.growl({ title: "", message: '<?=  addcslashes($message, "'")?>' });
		</script>
		<?php
	}



}// close Render
