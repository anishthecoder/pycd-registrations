<?php
/**
 * Models all sports events of the system.
 *
 * @author Anish V. Abraham
 */
class SportsEvent {

	public static $ID_LBL = 'Sports Event Id';

  /** Properties mirroring the table columsn. */
	private $id;
	private $name;
	private $date;

	/** Standard getters. */
	public function getId(){return $this->id;}
	public function getName(){return $this->name;}

	//----------------------------------------------------------------------------
	public function addPlayer($player){
		// TODO: Build a query building helper.
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare(
						"INSERT INTO sports_participation " .
						" (sportsEventId, playerId) " .
						" VALUES (:sportsEventId, :playerId)");
		$stmnt->bindParam(':sportsEventId', $this->id, PDO::PARAM_INT);
		$stmnt->bindParam(':playerId', $player->getId(), PDO::PARAM_INT);
		return $stmnt->execute();
	}

	//----------------------------------------------------------------------------
	public function getNonRosterPlayersByChurch($church){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare(
						"SELECT * FROM players ".
						"WHERE churchid=:churchid ".
						"AND id NOT IN (".
							"SELECT playerId FROM sports_participation ".
							"WHERE sportsEventId=:sportsEventId ".
						")".
						"ORDER BY fullName"
						);
		$stmnt->bindParam(':sportsEventId', $this->id, PDO::PARAM_INT);
		$stmnt->bindParam(':churchid', $church->getId(), PDO::PARAM_INT);
		$stmnt->setFetchMode(PDO::FETCH_CLASS, 'Player');
		$stmnt->execute();

		$players = array();
		while ($result = $stmnt->fetch()){
			array_push($players, $result);
		}//close while

		return $players;
	}

	//----------------------------------------------------------------------------
	public function getPlayersByChurch($church){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare(
						"SELECT * FROM players ".
						"WHERE churchid=:churchid ".
						"AND id IN (".
							"SELECT playerId FROM sports_participation ".
							"WHERE sportsEventId=:sportsEventId ".
						")".
						"ORDER BY fullName"
						);
		$stmnt->bindParam(':sportsEventId', $this->id, PDO::PARAM_INT);
		$stmnt->bindParam(':churchid', $church->getId(), PDO::PARAM_INT);
		$stmnt->setFetchMode(PDO::FETCH_CLASS, 'Player');
		$stmnt->execute();

		$players = array();
		while ($result = $stmnt->fetch()){
			array_push($players, $result);
		}//close while

		return $players;
	}//close getPlayersByChurch


	//----------------------------------------------------------------------------
	public function removePlayer($player){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare(
						"DELETE FROM sports_participation ".
						"WHERE playerId=:playerId");
		$stmnt->bindParam(':playerId', $player->getId(), PDO::PARAM_INT);
		return $stmnt->execute();
	}//close removePlayer



	//----------------------------------------------------------------------------
	/**
	 * Returns an array containing all the sports events registered in the
	 * database.
	 */
	public static function getAllSports(){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM ref_sports_events ORDER BY name");
		$stmnt->execute();
		$stmnt->setFetchMode(PDO::FETCH_CLASS, 'SportsEvent');

		$allSports = array();
		while ($result = $stmnt->fetch()){
			array_push($allSports, $result);
		}//close while

		return $allSports;
	}//close getAllSport

	/**
	 * Returns a sports object by the specified id.
	 */
	public static function getById($id){
		$dbcon = Db::getDbConnection();
		$stmnt = $dbcon->prepare("SELECT * FROM ref_sports_events"
						. " WHERE id = :id");
		$stmnt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmnt->execute();

		if ($stmnt->rowCount() === 1){
			$stmnt->setFetchMode(PDO::FETCH_CLASS, 'SportsEvent');
			$result = $stmnt->fetch();
			return $result;
		}
		else {
			return NULL;
		}
	}//close getById



}// close SportsEvents
