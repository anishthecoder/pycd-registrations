<?php
/**
 * @author Anish V. Abraham
 */
class SportsController {

	public static $ACTION_ADD_NEW_PLAYER = 'addNewPlayer';
	public static $ACTION_ADD_PLAYER = 'addPlayer';
	public static $ACTION_DELETE_PLAYER = 'deletePlayer';
	public static $ACTION_EDIT_PLAYER = 'editPlayer';
	public static $ACTION_REMOVE_PLAYER = 'removePlayer';
	public static $ID = 'SportsController';
  public static $RENDER_EDIT_PLAYER_FORM = 'renderEditPlayerform';
  public static $RENDER_NEW_PLAYER_FORM = 'renderNewPlayerform';
  public static $RENDER_PLAYERLIST_FORM = 'renderPlayersListAndForm';
	public static $RENDER_ROSTER = 'renderRoster';
	public static $RENDER_SUBMENU = 'renderSubMenu';

	//----------------------------------------------------------------------------
	/**
	 * Checks the following:
	 * 1. Valid full name and email
	 * 2. That a player with that full name is not already present in the church.
	 *
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							full name, email, sports event id.
	 *
	 * @return JavaScript
	 */
	public static function addNewPlayer($request){
		$fullname		= Controller::getFieldValue(
										Player::$FULL_NAME_LBL, $request);
		$email			= Controller::getFieldValue(
										Player::$EMAIL_LBL, $request);
		$sportId		= Controller::getFieldValue(
										SportsEvent::$ID_LBL, $request);
		if (!SportsController::validateMinimalPlayerInfo($email, $fullname)){
			return;
		}

		$preExistingPlayer = Player::getByFullName($fullname);
		if ($preExistingPlayer != NULL &&
			 ($preExistingPlayer->getChurch() == Session::getActiveChurch())){
			Render::jsAlert('A player by that name belonging to your church '
							. 'is already in the system.');
			Render::jsFieldFocus(Player::$FULL_NAME_LBL);
			return;
		}

		// If all above conditions have passed, add in the new player.
		$newPlayer	= Player::addPlayer($fullname, $email);
		$sport			= SportsEvent::getById($sportId);

		// Add the new player to the roster.
		if ($sport->addPlayer($newPlayer)){
			ob_start();
			SportsController::renderRosterRow($newPlayer);
			$renderString = str_replace("\n", "\\", addslashes(ob_get_clean()));
			?>
			<script type="text/javascript"  class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
				$('#roster').append('<?=$renderString?>')
				$('#newplayerform')[0].reset();
				// TODO focus back on the full name field.
			</script>
			<?php
		}
		else {
			Render::jsAlert('Error adding player to the roster.');
		}
	}


	//----------------------------------------------------------------------------
	/**
	 * Adds an existing player to a particular sport.
	 *
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							sports event id, player id
	 * @return Javascript
	 */
	public static function addPlayer($request){
		$sportIdFld = Render::labelToField(SportsEvent::$ID_LBL);
		$playerIdFld = Player::$ID_FLD;
		if (!isset($request[$sportIdFld]) ||
				!isset($request[$playerIdFld])){
			return;
		}
		else{
			$sportId	= $request[$sportIdFld];
			$playerId = $request[$playerIdFld];
		}

		$sport = SportsEvent::getById($sportId);
		/* @var $sport SportsEvent */
		$player = Player::getById($playerId);
		/* @var $player Player */
		$sport->addPlayer($player);

		// Add Player to the roster.
		ob_start();
		SportsController::renderRosterRow($player);
		$renderString = str_replace("\n", "\\", addslashes(ob_get_clean()));
		?>
		<script type="text/javascript"  class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			$('#player<?=$playerId?>').remove();
			$('#roster').append('<?=$renderString?>')
		</script>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							player id
	 * @return Javascript
	 */
	public static function deletePlayer($request){
		$playerId = $request[Player::$ID_FLD];
		Player::deletePlayer($playerId);
		?>
		<script type="text/javascript"  class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			$('#player<?=$playerId?>').remove();
		</script>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							email, full name, sport event id, player id
	 * @return Javascript
	 */
  public static function editPlayer($request){
    $email		= Controller::getFieldValue(Player::$EMAIL_LBL, $request);
    $fullname = Controller::getFieldValue(Player::$FULL_NAME_LBL, $request);
    $sportId	= Controller::getFieldValue(SportsEvent::$ID_LBL, $request);
    $id				= $request[Player::$ID_FLD];

		if (!SportsController::validateMinimalPlayerInfo($email, $fullname)){
			return;
		}
    if ($sportId == NULL || $id == NULL){
      return;
    }

    // Validate the new values;
    $player = Player::getById($id);
    /*@var $player Player */
    $player->email		= $email;
    $player->fullName = $fullname;
    $player->save();
		?>
		<script type="text/javascript"  class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			$('#player<?=$id?> .fullname').text('<?=$fullname?>');
		</script>
		<?php
		$jsonDataAddPlayer = array(
				Controller::$ID => SportsController::$ID,
				Controller::$ACTION => SportsController::$RENDER_NEW_PLAYER_FORM,
				Render::labelToField(SportsEvent::$ID_LBL) => $sportId
		);
    ?>
    <script type="text/javascript">
				data = <?=json_encode($jsonDataAddPlayer)?>;
        render('#formsArea', data);
		</script>
    <?php
  }


	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							sports event id, player id
	 * @return Javscript outptstream
	 */
	public static function removePlayer($request){
		$sportIdFld = Render::labelToField(SportsEvent::$ID_LBL);
		$playerIdFld = Player::$ID_FLD;
		if (!isset($request[$sportIdFld]) ||
				!isset($request[$playerIdFld])){
			return;
		}
		else{
			$sportId = $request[$sportIdFld];
			$playerId = $request[$playerIdFld];
		}

		$sport = SportsEvent::getById($sportId);
		/* @var $sport SportsEvent */
		$player = Player::getById($playerId);
		$sport->removePlayer($player);

		// Remove that player from the roster.
		?>
		<script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			$('#player<?=$player->getId()?>').remove();
		</script>
		<?php
		ob_start();
		SportsController::renderPlayerListRow($player);
		$renderString = str_replace("\n", "\\", addslashes(ob_get_clean()));
		?>
		<script type="text/javascript"  class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			$('#playerList').append('<?=$renderString?>')
		</script>
		<?php
	}



	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							sports event id, player id
	 * @return HTML + Javascript Intended for rendering in a target.
	 */
  public static function renderEditPlayerform($request){
    $sportEventId = Controller::getFieldValue(SportsEvent::$ID_LBL, $request);
    $playerId = $request[Player::$ID_FLD];
    $player = Player::getById($playerId);
    /* @var $player Player */
    ?>
		<form id="newplayerform"
					class="inlineLabeled"
					onsubmit="javascript:return false;">
      <h1>Edit Player</h1>
      <p><?php Render::field(Player::$FULL_NAME_LBL, 'text',
                $player->fullName)?></p>
      <p><?php Render::field(Player::$EMAIL_LBL, 'text',
                $player->email)?></p>
			<?php
			Render::hiddenField(Controller::$ID, SportsController::$ID);
			Render::hiddenField(Controller::$ACTION,SportsController::$ACTION_EDIT_PLAYER);
			Render::hiddenField(Render::labelToField(SportsEvent::$ID_LBL), $sportEventId);
			Render::hiddenField(Player::$ID_FLD, $player->id);
			?>
			<p>
				<input type="submit"
							 value="Save Changes"
							 onclick="sendForm(this)"  />
      </p>
			<p>
				<input type="button"
							 value="Cancel"
							 onclick="cancelEdit()"  />
      </p>
    </form>
    <script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
      $('label').inFieldLabels();
    </script>
    <?php
    $jsonData = array(
				Controller::$ID => SportsController::$ID,
				Controller::$ACTION => SportsController::$RENDER_NEW_PLAYER_FORM,
				Render::labelToField(SportsEvent::$ID_LBL) => $sportEventId
    )
    ?>
    <script type="text/javascript">
      function cancelEdit(){
				data = <?=json_encode($jsonData)?>;
        render('#formsArea', data);
      }
    </script>
    <?php
  }



	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							sports event id
	 * @return HTML + Javascript Intended for rendering in a target.
	 */
  public static function renderNewPlayerForm($request){
    $sportEventId = Controller::getFieldValue(SportsEvent::$ID_LBL, $request);
    $sport = SportsEvent::getById($sportEventId);
    ?>
		<form id="newplayerform"
					class="inlineLabeled"
					onsubmit="javascript:return false;">
	    <h1>Add New Player</h1>
			<p><?php Render::field(Player::$FULL_NAME_LBL, 'text')?></p>
			<p><?php Render::field(Player::$EMAIL_LBL, 'text')?></p>
			<?php
			Render::hiddenField(Controller::$ID,SportsController::$ID);
			Render::hiddenField(Controller::$ACTION,SportsController::$ACTION_ADD_NEW_PLAYER);
			Render::hiddenField(Render::labelToField(SportsEvent::$ID_LBL),$sport->getId());
			?>
			<p>
				<input type="submit"
							 value="Add"
							 onclick="javascript:sendForm(this)"  />
      </p>
		</form>
    <script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
      $('label').inFieldLabels();
    </script>
    <?php
  }


	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							sports event id
	 * @return HTML + Javascript Intended for rendering in a target.
	 */
  public static function renderPlayersListAndForm($request){
    ?>
    <h1>Other Existing Players</h1>
		<table id="playerList">
			<thead>
				<th>Full Name</th>
				<th></th>
			</thead>
			<tbody>
			<?php
				$sportEventId = Controller::getFieldValue(
													SportsEvent::$ID_LBL, $request);
				$sport				= SportsEvent::getById($sportEventId);
				$church				= Session::getActiveChurch();
				$nonRosterPlayers = $sport->getNonRosterPlayersByChurch($church);

				foreach ($nonRosterPlayers as $player){
					SportsController::renderPlayerListRow($player);
				}
			?>
			</tbody>
		</table>
    <div id="formsArea">
      <?php SportsController::renderNewPlayerForm($request)?>
    </div>
		<?php

		$jsonDataAddPlayer = array(
				Controller::$ID => SportsController::$ID,
				Controller::$ACTION => SportsController::$ACTION_ADD_PLAYER,
				Render::labelToField(SportsEvent::$ID_LBL) => $sport->getId(),
				Player::$ID_FLD => ''
		);
		?>
    <script type="text/javascript">
			function addPlayerToRoster(playerId){
				data = <?=json_encode($jsonDataAddPlayer)?>;
				data.<?=Player::$ID_FLD?> = playerId;
				sendRequestJson(data);
			}
		</script>
    <?php
  }


	//----------------------------------------------------------------------------
	/**
	 * @param Player $player
	 * @return HTML Streamed to the output buffer.
	 */
	public static function renderPlayerListRow($player){
		?>
		<tr id="player<?=$player->getId()?>"
				onmouseover="$(this).addClass('hover')"
				onmouseout="$(this).removeClass('hover')"
				class="pointer">
			<td class="w85 fullname"
					onclick="addPlayerToRoster(<?=$player->getId()?>)">
						<?=$player->getFullName()?>
			</td>
			<td class="actions w20">
				<span onclick="editPlayer(<?=$player->getId()?>)">EDIT</span>
				<span onclick="deletePlayer(<?=$player->getId()?>)">x</span>
			</td>
		</tr>
		<?php
	}



	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							sports event id
	 * @return HTML + Javascript Intended for rendering in a target.
	 */
	public static function renderRoster($request){
		if (isset($request[Render::labelToField(SportsEvent::$ID_LBL)]) == FALSE){
			return;
		}
		$sportId	= $request[Render::labelToField(SportsEvent::$ID_LBL)];
		$sport		= SportsEvent::getById($sportId);
    $church		= Session::getActiveChurch();
    $players	= $sport->getPlayersByChurch($church);
		?>
		<h1><?=$sport->getName()?> Roster</h1>
		<table id="roster">
			<thead>
				<th>Full Name</th>
				<th></th>
			</thead>
			<tbody>
			<?php
			foreach ($players as $player){
        SportsController::renderRosterRow($player);
      }
			?>
			</tbody>
		</table>
		<?php
			$jsonDataShowPlayerList = array(
					Controller::$ID =>
							SportsController::$ID,
					Controller::$ACTION =>
							SportsController::$RENDER_PLAYERLIST_FORM,
					Render::labelToField(SportsEvent::$ID_LBL) =>
							$sport->getId()
			);
			$jsonDataRemovePlayer = array(
					Controller::$ID =>
							SportsController::$ID,
					Controller::$ACTION =>
							SportsController::$ACTION_REMOVE_PLAYER,
					Render::labelToField(SportsEvent::$ID_LBL) =>
							$sport->getId(),
					Player::$ID_FLD => ''
			);
      $jsonDataEditPlayer = array(
          Controller::$ID =>
							SportsController::$ID,
          Controller::$ACTION =>
							SportsController::$RENDER_EDIT_PLAYER_FORM,
          Render::labelToField(SportsEvent::$ID_LBL) =>
							$sport->getId(),
          Player::$ID_FLD => ''
      );
      $jsonDataDeletePlayer = array(
          Controller::$ID =>
							SportsController::$ID,
          Controller::$ACTION =>
							SportsController::$ACTION_DELETE_PLAYER,
          Render::labelToField(SportsEvent::$ID_LBL) =>
							$sport->getId(),
          Player::$ID_FLD => ''
      )
		?>
    <input type="button" onclick="showPlayerListForm();" value="Add Players">
		<script type="text/javascript">
			$('#<?=Render::labelToField(SportsEvent::$ID_LBL)?>').val('');
			$('#rightcolumn').empty();

			function showPlayerListForm(){
				render('#rightcolumn', <?=json_encode($jsonDataShowPlayerList)?>);
			}
			function removePlayer(playerId){
				data = <?=json_encode($jsonDataRemovePlayer)?>;
				data.<?=Player::$ID_FLD?> = playerId;
				sendRequestJson(data);
			}
			function editPlayer(playerId){
				data = <?=json_encode($jsonDataEditPlayer)?>;
				data.<?=Player::$ID_FLD?> = playerId;
        render('#formsArea', data);
			}
			function deletePlayer(playerId){
				data = <?=json_encode($jsonDataDeletePlayer)?>;
				data.<?=Player::$ID_FLD?> = playerId;

				smoke.confirm("Are you sure you want to \n\
					remove this player from the system?", function(e){
						if (e){
							sendRequestJson(data);
						}else{
						}
					}, {
						ok: "Yes, I'm Sure",
						cancel: "No"
					});
			}

		</script>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @param Player $player
	 * @return HTML Streamed to the output buffer.
	 */
	public static function renderRosterRow($player){
		?>
		<tr id="player<?=$player->getId()?>"
				onmouseover="$(this).addClass('hover')"
				onmouseout="$(this).removeClass('hover')">
			<td class="w95 fullname"><?=$player->getFullName()?></td>
			<td class="actions w10">
				<span onclick="removePlayer(<?=$player->getId()?>)">x</span>
			</td>
		</tr>
		<?php
	}



	//----------------------------------------------------------------------------
	/**
	 * @return HTML+Javascript
	 */
	public static function renderSubMenu($request){
		?>
		<h1>Sports</h1>
		<ul>
			<?php
			$allSports = SportsEvent::getAllSports();
			foreach ($allSports as $sport){
				?>
				<li onclick="showRoster(<?=$sport->getId()?>)">
					<?=$sport->getName()?>
				</li>
				<?php
			}//close foreach
			?>
			<?=Render::htmlRenderMenuBackLink()?>
		</ul>
		<?php
		$context = array(
				Controller::$ID => SportsController::$ID,
				Controller::$ACTION => SportsController::$RENDER_ROSTER,
				Render::labelToField(SportsEvent::$ID_LBL) => ''
		)
		?>
		<script type="text/javascript">
			function showRoster(sportId){
				context = <?=  json_encode($context)?>;
				context.<?=Render::labelToField(SportsEvent::$ID_LBL)?> = sportId;
				render('#middlecolumn', context);
			}
		</script>
		<?=Render::jsEnableMenuHover()?>
		<?php
	}



	//----------------------------------------------------------------------------
	/**
	 *
	 * @param String $email
	 * @param String $fullname
	 * @return boolean + Javascript to the output stream.
	 */
	public static function validateMinimalPlayerInfo($email, $fullname){
		if ($fullname == ""){
			Render::jsAlert('Please enter the players name.');
			Render::jsFieldFocus(Player::$FULL_NAME_LBL);
			return FALSE;
		}
		else if ($email == ""){
			Render::jsAlert('Please enter the players email.');
			Render::jsFieldFocus(Player::$EMAIL_LBL);
			return FALSE;
		}
		else if (!is_fullname_valid($fullname)){
			Render::jsAlert('Name should be in the format '
							. '[FirstName MI. LastName]. Middle initials '
							. 'are optional');
			Render::jsFieldFocus(Player::$FULL_NAME_LBL);
			return FALSE;
		}
		else if (!is_email_valid($email)){
			Render::jsAlert('Invalid email.');
			Render::jsFieldFocus(Player::$EMAIL_LBL);
			return FALSE;
		}
		else {
			return TRUE;
		}
	}//close validateMinimalPlayerInfo

}
