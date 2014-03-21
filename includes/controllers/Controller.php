<?php
/**
 * Main general purporse controller. The applications attempts to follow the MVC
 * pattern. This class serves as the main controller for the application,
 * providing the definitions and patterns for all other controllers to follow.
 *
 * Each controller will have a set of public static keywords defined in the
 * beginning. These keywords will be used by the various Views to indicate what
 * actions they are requestion of the controllers. Some of the keywords will be
 * keys to an array, while others will be values. See the documentation of each
 * keyword for details.
 *
 * All controllers will have an ID keyword that will be the keyword identifying
 * that controller itself, either for use as a key or a value.
 *
 * @author Anish
 */
class Controller {

  public static $ID = 'controller';
  public static $ACTION = 'action';
	public static $ACTION_GET_OBJECT = 'getObject';
  public static $ACTION_INITIALIZE = 'initialize';
  public static $ACTION_RENDER = 'render';
	public static $EVENT_QUEUE = 'eventQueue';
  public static $RENDER_TYPE = 'renderType';
	public static $RENDER_MAIN_LAYOUT = 'renderMainLayout';
	public static $RENDER_MAIN_MENU = 'renderMainMenu';
	public static $RENDER_SUBMENU_ADMIN = 'renderSubmenuAdmin';
	public static $RENDER_SUBMENU_SPORTS = 'renderSubmenuSports';
	public static $RENDER_SUBMENU_TALENT = 'renderSubmenuTalentCompetitions';

  /**
   * Given a field label, return the value of the field from the request array.
   *
   * @param String $label Label of the field to get the value for.
   * @param array $request The $_REQUEST array.
   * @return The whitespace-trimmed value of the field if it is set, else NULL.
   */
  public static function getFieldValue($label, $request){
    $fld = Render::labelToField($label);
    if (isset($request[$fld])) {
      return trim($request[$fld]);
    }
    else {
      return NULL;
    }
  }


	//----------------------------------------------------------------------------
	/**
	 * @param array $request Must contain two keys:
	 * $request['function'] - The string containing the fully resolved function
	 *												name.
	 * $request['arguments']- A JSON decodable string containing all the arguments
	 *												to be passed to the function.
	 */
	public static function getObject($request){
		if (!isset($request['function']) ||
				!isset($request['arguments'])){
			return;
		}
		else {
			$function = $request['function'];
			$arguments = $request['arguments'];
			if (!is_array($arguments)){
				$arguments = array($arguments);
			}
			//$obj = call_user_func_array($function, $arguments);
			$obj = call_user_func_array(array('Player', 'getById'), array(21));
			echo json_encode((array) $obj);
			return;
		}
	}


	//----------------------------------------------------------------------------
  /**
   * Check the session, and if it's active, set the initial layout of the
   * app interface.
   */
  public static function initialize($request){
    if (!Session::isSessionActive()){
      Render::jsRedirect('login.php');
      return;
    }
		?>
		<script type="text/javascript">
			$('#header').show();
			$('#main').show();
			<?=Render::jsLineRender('#main',
							Controller::$ID,
							Controller::$RENDER_MAIN_LAYOUT)?>
		</script>
		<?php
  }//close initialize



	//----------------------------------------------------------------------------
	/**
	 * Returns an array that is ready for JSON encoding with the correct keys
	 * associated with the correct values to perform the requested type of
	 * rendering by the specified controller.
	 *
	 *
	 * @param String $controller
	 * @param String $renderType
	 */
	public static function loadArray($controller, $renderType){
		return array(
			Controller::$ID => $controller,
			Controller::$ACTION => Session::$RENDER_LOGIN_FORM
		);
	}


	//----------------------------------------------------------------------------
	public static function renderMainLayout(){
		?>
		<div id="leftcolumn" class="columns">
			Loading...
		</div>
		<div id="middlecolumn" class="columns">
			<div class="placeholder">Select an item from the menu.</div>
		</div>
		<div id="rightcolumn" class="columns">
		</div>
		<?=  Render::jsRender('#leftcolumn',
						Controller::$ID,
						Controller::$RENDER_MAIN_MENU)?>
		<?php
	}


	//----------------------------------------------------------------------------
	public static function renderMainMenu(){
		?>
		<h1>Main Menu</h1>
		<ul>
			<li onclick="<?=Render::jsLineRender('#leftcolumn',
							SportsController::$ID,
							SportsController::$RENDER_SUBMENU)?>">
				Sports
			</li>
			<?php
			// TODO: Change to its own controller.
			?>
			<li onclick="javascript:render(
								'#leftcolumn',
								'Controller',
								'<?=  Controller::$RENDER_SUBMENU_TALENT?>')">
				Talent Competitions
			</li>
			<?php if (Session::isSessionAdmin()) {?>
			<?php
			// TODO: Change to its own controller.
			?>
			<li onclick="<?=Render::jsLineRender('#leftcolumn',
							AdminController::$ID,
							AdminController::$RENDER_SUBMENU)?>">
				Admin
			</li>
			<?php }?>
		</ul>
		<?=Render::jsEnableMenuHover()?>
		<?php
	}

	//----------------------------------------------------------------------------
	public static function renderSubmenuAdmin($request){
		?>
		<h1>Admin</h1>
		<ul>
			<?=Render::htmlRenderMenuBackLink()?>
		</ul>
		<?=Render::jsEnableMenuHover()?>
		<?php
	}



	//----------------------------------------------------------------------------
	public static function renderSubmenuTalentCompetitions($request){
		?>
		<h1>Talent Competitions</h1>
		<ul>
			<li>Coming Soon...</li>
			<?=Render::htmlRenderMenuBackLink()?>
		</ul>
		<?=Render::jsEnableMenuHover()?>
		<?php
	}

}//close Controller
