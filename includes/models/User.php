<?php
/**
 * Encapsulates users of the system.
 *
 * @author Anish V. Abraham
 */
class User {

  /**
   * Keywords that indicate various properties of the class for consistent use
   * across the rest of the application.
   */
	public static $ADMIN_FLD = 'userAdminFld';
	public static $CHURCH_FLD = 'userChurchIdFld';
  public static $CONFIRM_PASSWORD_LBL = 'confirm password';
  public static $EMAIL_LBL = 'email';
  public static $NEW_PASSWORD_LBL = 'new password';
  public static $OLD_PASSWORD_LBL = 'old password';
  public static $PASSWORD_LBL = 'password';
	public static $USERNAME_FLD = 'usernameFld';
  public static $USERNAME_LBL = 'username';

  /** Properties mirroring the table columns. */
	public $username;
	public $email;
	public $passwordHash;
	public $churchId;
	public $isAdmin;

  /**
   * Checks whether the MD5 hash of the provided password matches the one stored
   * in the database.
   *
   * @param String $password Provided password.
   */
  public function isPasswordCorrect($password){
    if ($this->passwordHash == md5($password)){
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

	/** Standard getters. */
  public function getChurchId(){return $this->churchId;}
	public function getEmail(){return $this->email;}
	public function getUsername(){return $this->username;}
	public function getIsAdmin(){return $this->isAdmin;}

	public function getChurch(){
		if (isset($this->churchId))
			return Church::getById ($this->churchId);
		else
			return NULL;
	}


	//----------------------------------------------------------------------------
	/**
	 * @return array
	 */
	public static function getAll(){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM users ORDER BY username");
		$stmnt->setFetchMode(PDO::FETCH_CLASS, 'User');
		$stmnt->execute();

		$users = array();
		while ($result = $stmnt->fetch()){
			array_push($users, $result);
		}//close while

		return $users;
	}


  /**
   * Attempts to load a user from the database by the given username.
   * @param String $username
   * @return The user corresponding to the provided username if it is found in
   *          the database. NULL otherwise.
   */
	public static function getByUsername($username){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM users WHERE username = :username");
		$stmnt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmnt->execute();

		if ($stmnt->rowCount() === 1){
			$stmnt->setFetchMode(PDO::FETCH_CLASS, 'User');
			$result = $stmnt->fetch();
			return $result;
		}
		else {
			return NULL;
		}
	}// close getByUsername

}// close User
