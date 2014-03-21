<?php
/**
 * @author Anish V. Abraham
 */
class Church {

	public static $ID_FLD = 'churchIdFld';
	public static $NAME_LBL = 'church name';
	public static $MEMBERS_LBL = 'members';
	public static $PASTOR_NAME_LBL = 'pastor name';
	public static $PASTOR_EMAIL_LBL = 'pastor email';
	public static $YD_NAME_LBL = 'youth dir. name';
	public static $YD_EMAIL_LBL = 'youth dir. email';

  /** Properties mirroring the table columns. */
  public $id;
  public $name;
	public $nMembers;
	public $pastorName;
	public $pastorEmail;
	public $youthDirectorName;
	public $youthDirectorEmail;

	/** Standard getters. */
	public function getId(){return $this->id;}
  public function getName(){return $this->name;}


	//----------------------------------------------------------------------------
	function __construct() {
	}


	//----------------------------------------------------------------------------
	/**
	 * @return array User
	 */
	public function getUsers(){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM users WHERE churchId=:churchId");
		$stmnt->bindParam(":churchId", $this->id, PDO::PARAM_INT);
		$stmnt->setFetchMode(PDO::FETCH_CLASS, 'User');
		$stmnt->execute();

		$users = array();
		while ($result = $stmnt->fetch()){
			array_push($users, $result);
		}//close while

		return $users;
	}


	//----------------------------------------------------------------------------
  public function save(){
		$dbcon = Db::getDbConnection();
		if (isset($this->id)){
			$stmnt = $dbcon->prepare(
							"UPDATE churches ".
							"SET name=:name, ".
							"nMembers=:nMembers, ".
							"pastorName=:pastorName, ".
							"pastorEmail=:pastorEmail, ".
							"youthDirectorName=:youthDirectorName, ".
							"youthDirectorEmail=:youthDirectorEmail ".
							"WHERE id=:id"
							);
			$stmnt->bindParam(':id', $this->id, PDO::PARAM_INT);
		}
		else {
			$stmnt = $dbcon->prepare(
							"INSERT INTO churches ".
							"( ".
								"name, nMembers, ".
							  "pastorName, pastorEmail, ".
							  "youthDirectorName, youthDirectorEmail".
							") VALUES ".
							"( ".
								":name, :nMembers, ".
								":pastorName, :pastorEmail, ".
								":youthDirectorName, :youthDirectorEmail ".
							")"
							);
		}

		$stmnt->bindParam(':name', $this->name, PDO::PARAM_STR);
		$stmnt->bindParam(':nMembers', $this->nMembers, PDO::PARAM_INT);
		$stmnt->bindParam(':pastorName', $this->pastorName, PDO::PARAM_STR);
		$stmnt->bindParam(':pastorEmail', $this->pastorEmail, PDO::PARAM_STR);
		$stmnt->bindParam(':youthDirectorName', $this->youthDirectorName, PDO::PARAM_STR);
		$stmnt->bindParam(':youthDirectorEmail', $this->youthDirectorEmail, PDO::PARAM_STR);
    $stmnt->execute();

		if (!isset($this->id)){
			$this->id = $dbcon->lastInsertId();
		}
	}



	//----------------------------------------------------------------------------
	public static function getAll(){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM churches ORDER BY name");
		$stmnt->setFetchMode(PDO::FETCH_CLASS, 'Church');
		$stmnt->execute();

		$allChurches = array();
		while ($result = $stmnt->fetch()){
			array_push($allChurches, $result);
		}//close while

		return $allChurches;
	}

  /** Retrieve any church by it's id. */
  public static function getById($id){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM churches WHERE id = :id");
		$stmnt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmnt->execute();

		if ($stmnt->rowCount() === 1){
			$stmnt->setFetchMode(PDO::FETCH_CLASS, 'Church');
			$result = $stmnt->fetch();
			return $result;
		}
		else {
			return NULL;
		}
  }// close getById

}//close Church
