<?php

/*
 * Handles app controlls related to the session, user authentication, etc.
 */
class Session {

  public static $ACTION_CHANGE_PASSWORD = 'changePassword';
  public static $ACTION_CHECK_LOGIN = 'checkLogin';
  public static $ACTION_LOGOUT = 'logout';
  public static $ACTION_RESET_PASSWORD = 'resetPassword';
	public static $ACTION_VIEW_AS = 'viewAs';
	public static $ID = 'Session';
  public static $RENDER_CHANGE_PASSWORD_FORM = 'renderChangePasswordForm';
  public static $RENDER_LOGIN_FORM = 'renderLoginForm';
  public static $RENDER_PASSWORD_RESET_FORM = 'renderPasswordResetForm';

  /**
   * Key of the $_SESSION variable where the User object
   * correspnding to the current logged in user will be stored.
   */
  public static $SESSION_USER = 'user';

  /**
   * Key of the $_SESSION variable where the Church object
   * corresponding to the currently logged in user will be stored.
   */
  public static $SESSION_CHURCH = 'church';

	//----------------------------------------------------------------------------
  /**
   * Check provided login credentials against the users database.
   * @param array $request The $_REQUEST variable passed on from
   * processRequest()
   */
	public static function checkLogin($request){
		$username = Controller::getFieldValue(User::$USERNAME_LBL, $request);
		$password = Controller::getFieldValue(User::$PASSWORD_LBL, $request);

    // Check that both values are specified.
		if ($username == "" OR
        $password == ""){
			Render::jsAlert('Both username & password are required!');
			Render::jsFieldFocus(User::$USERNAME_LBL);
			return;
		}

    // Check for a valid username.
		$user = User::getByUsername($username);
		if ($user == NULL){
			Render::jsAlert('Invalid username!');
			return;
		}

    if ($user->isPasswordCorrect($password)){
      $_SESSION[Session::$SESSION_USER] = (object) $user;
      $_SESSION[Session::$SESSION_CHURCH] =
          Church::getById($user->getChurchId());
      Render::jsRedirect('index.php');
    }
    else {
			Render::jsAlert('Invalid password!');
			return;
    }

	}// close checkLogin


  /**
   * Checks whether the current session has a user registered. If yes, returns
   * TRUE; otherwise returns FALSE.
	 *
	 * @return boolean
   */
  public static function isSessionActive(){
    return isset($_SESSION[Session::$SESSION_USER]);
  }

	/**
	 * Checks whether the currently logged in user is an admin or not.
	 *
	 * @return boolean
	 */
	public static function isSessionAdmin(){
    if (isset($_SESSION[Session::$SESSION_USER])){
      $user = $_SESSION[Session::$SESSION_USER];
      return $user->getIsAdmin();
    }
    else{
      return FALSE;
    }
	}


	//----------------------------------------------------------------------------
  public static function changePassword($request){
    //TODO: Complete implementation
		Render::jsAlert('Coming soon');
  }

	//----------------------------------------------------------------------------
  public static function getActiveChurch(){
    if (Session::isSessionActive()){
      return $_SESSION[Session::$SESSION_CHURCH];
    }
    else {
      return NULL;
    }
  }

	//----------------------------------------------------------------------------
  public static function logout($request){
    session_destroy();
    Render::jsRedirect('login.php');
  }


	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							church id
	 * @return Javascript
	 */
	public static function viewAs($request){
		if (!Session::isSessionAdmin()) return;
		$churchId = $request[Church::$ID_FLD];
		$church = Church::getById($churchId);
		$_SESSION[Session::$SESSION_CHURCH] = $church;
		Render::jsRedirect('index.php');
	}



  /**
   * Handles any rendering of HTML elements related to the session.
   * @param array $request The $_REQUEST variable passed on from the main
   * controller
   */
	public static function render($request){
		if (!isset($request[Controller::$RENDER_TYPE])){
			return;
		}
    call_user_func('Session::'.Controller::$ACTION_RENDER
                              .$request[Controller::$RENDER_TYPE], $request);
  }



	//----------------------------------------------------------------------------
  /**
   * Renders the main login form for the application.
   */
  public static function renderLoginForm(){
    ?>
    <form id="loginform"
          class="loginforms"
          onsubmit="return false;">
      <p><?php Render::field(User::$USERNAME_LBL, 'text')?></p>
      <p><?php Render::field(User::$PASSWORD_LBL, 'password')?></p>
      <p>
        <input type="submit"
               value="Login"
               onclick="sendForm(this)"  />
        <?php	Render::hiddenField(Controller::$ID, Session::$ID); ?>
        <?php	Render::hiddenField(Controller::$ACTION,
                                    Session::$ACTION_CHECK_LOGIN); ?>
        <a href="#"
           onclick="<?=Render::jsLineRender('#main',
							Session::$ID,
							Session::$RENDER_PASSWORD_RESET_FORM)?>">
          Forgot password?</a>
        <a href="#"
           onclick="<?=Render::jsLineRender('#main',
							Session::$ID,
							Session::$RENDER_CHANGE_PASSWORD_FORM)?>">
          Change password?</a>
      </p>
    </form>
    <script>$("label").inFieldLabels(); </script>
    <?= Render::jsFieldFocus(User::$USERNAME_LBL)?>
    <?php
	}// close renderLoginForm

  /**
   * Renders the password reset form.
   */
  public static function renderPasswordResetForm(){
    ?>
    <form id="passwordresetform"
          class="loginforms"
          onsubmit="javascript:return false;">
      <p><?php Render::field(User::$USERNAME_LBL, 'text')?></p>
      <p><?php Render::field(User::$EMAIL_LBL, 'text')?></p>
      <p>
        <input type="submit"
               value="Reset Password"
               onclick="javascript:sendFormRequest('passwordresetform')"  />
        <?php	Render::hiddenField(Controller::$ID, 'Session'); ?>
        <?php	Render::hiddenField(Controller::$ACTION,
                                    Session::$ACTION_RESET_PASSWORD); ?>
        <a href="#"
           onclick="javascript:render(
                     '#main',
                     'Session',
                     '<?=SESSION::$RENDER_LOGIN_FORM?>')">
          Sign in?</a>
      </p>
    </form>
    <script>$("label").inFieldLabels(); </script>
    <script>$("#<?=Render::labelToField(User::$USERNAME_LBL)?>").focus(); </script>
    <?php
  }//close renderPasswordResetForm

  /**
   * Renders the password change form.
   */
  public static function renderChangePasswordForm(){
    ?>
    <form id="passwordchangeform"
          class="loginforms"
          onsubmit="javascript:return false;">
      <p><?php Render::field(User::$USERNAME_LBL, 'text')?></p>
      <p><?php Render::field(User::$OLD_PASSWORD_LBL, 'password')?></p>
      <p><?php Render::field(User::$NEW_PASSWORD_LBL, 'password')?></p>
      <p><?php Render::field(User::$CONFIRM_PASSWORD_LBL, 'password')?></p>
      <p>
        <input type="submit"
               value="Change Password"
               onclick="javascript:sendFormRequest('passwordchangeform')"  />
        <input type="hidden" name="requiredAction" value="passwordChange" />
        <?php	Render::hiddenField(Controller::$ID, 'Session'); ?>
        <?php	Render::hiddenField(Controller::$ACTION,
                                    Session::$ACTION_CHANGE_PASSWORD); ?>
        <a href="#"
           onclick="javascript:render(
                     '#main',
                     'Session',
                     '<?=SESSION::$RENDER_LOGIN_FORM?>')">
          Sign in?</a>
      </p>
    </form>
    <script>$("label").inFieldLabels(); </script>
    <script>$("#<?=Render::labelToField(User::$USERNAME_LBL)?>").focus(); </script>
    <?php
  }//close renderChangePasswordForm


  public static function resetPassword($request){
    //TODO: Complete implementation.
		Render::jsAlert('Coming soon');
  }

}//close Session