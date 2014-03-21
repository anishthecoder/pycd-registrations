<?php
/**
 * @author Anish V. Abraham
 */
class Player {

	/** Name of the table corresponding to the players list in the DB. */
	public static $DB_TABLE = 'players';
	public static $DB_CHURCHID = 'churchid';
	public static $CLASSNAME = 'Player';

	/** Labels for the fields corresponding to properties of the object. */
  public static $EMAIL_LBL = 'email';
  public static $FULL_NAME_LBL = 'full name';

	/** Other labels for request processing. */
	public static $ID_FLD = 'playerId';

  /** Properties mirroring the table columns. */
  public $id;
  public $fullName;
  public $email;
  public $address1;
  public $address2;
  public $city;
  public $state;
  public $zip;
  public $churchid;
  public $membershipConfirmed;

  /** Standard Getters */
	public function getChurch(){return Church::getById($this->churchid);}
	public function getChurchId(){return $this->churchid;}
  public function getFullName(){return $this->fullName;}
	public function getId(){return $this->id;}


	//----------------------------------------------------------------------------
  public function save(){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare(
            "UPDATE players ".
            "SET fullName=:fullName, ".
            "email=:email, ".
            "address1=:address1, ".
            "address2=:address2, ".
            "city=:city, ".
            "state=:state, ".
            "zip=:zip, ".
            "membershipConfirmed=:membershipConfirmed ".
            "WHERE id=:id"
            );
		$stmnt->bindParam(':id', $this->id, PDO::PARAM_INT);
    $stmnt->bindParam(':fullName', $this->fullName, PDO::PARAM_STR);
    $stmnt->bindParam(':email', $this->email, PDO::PARAM_STR);
    $stmnt->bindParam(':address1', $this->address1, PDO::PARAM_STR);
    $stmnt->bindParam(':address2', $this->address2, PDO::PARAM_STR);
    $stmnt->bindParam(':city', $this->city, PDO::PARAM_STR);
    $stmnt->bindParam(':state', $this->state, PDO::PARAM_STR);
    $stmnt->bindParam(':zip', $this->zip, PDO::PARAM_STR);
    $stmnt->bindParam(':membershipConfirmed',
              $this->membershipConfirmed, PDO::PARAM_BOOL);
    $stmnt->execute();
  }

	//----------------------------------------------------------------------------
	/**
	 * Attempts to add a new player
	 * @param String $fullName
	 * @param String $email
	 * @return null
	 */
	public static function addPlayer($fullName, $email){
		$fullName = ucwords($fullName);
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("INSERT INTO players "
						. "(fullName, email, churchid) VALUES "
						. "(:fullName, :email, :churchid)");
		$stmnt->bindParam(':fullName', $fullName, PDO::PARAM_STR);
		$stmnt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmnt->bindParam(':churchid', Session::getActiveChurch()->getId(), PDO::PARAM_INT);
		if (!$stmnt->execute()){
			return NULL;
		}
		else {
			$player = Player::getByFullName($fullName);
			return $player;
		}

		// TODO add the new player to the roster for the current event.
	}


	//----------------------------------------------------------------------------
	public static function deletePlayer($playerId){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("DELETE FROM players WHERE id = :id");
		$stmnt->bindParam(':id', $playerId, PDO::PARAM_INT);
		$stmnt->execute();

		$stmnt = $dbcon->prepare("DELETE FROM sports_participation "
						. "WHERE playerId = :playerId");
		$stmnt->bindParam(':playerId', $playerId, PDO::PARAM_INT);
		$stmnt->execute();
	}


	//----------------------------------------------------------------------------
	public static function getByFullName($fullName){
		$fullName = ucwords($fullName);
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM players WHERE fullName = :fullName");
		$stmnt->bindParam(':fullName', $fullName, PDO::PARAM_STR);
		$stmnt->execute();

		if ($stmnt->rowCount() === 1){
			$stmnt->setFetchMode(PDO::FETCH_CLASS, 'Player');
			$result = $stmnt->fetch();
			return $result;
		}
		else {
			return NULL;
		}
	}//close getByFullName()


	//----------------------------------------------------------------------------
	public static function getById($id){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM players WHERE id = :id");
		$stmnt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmnt->execute();

		if ($stmnt->rowCount() === 1){
			$stmnt->setFetchMode(PDO::FETCH_CLASS, 'Player');
			$result = $stmnt->fetch();
			return $result;
		}
		else {
			return NULL;
		}
	}

	//TODO: Collapse these seperate methods into one.

}
