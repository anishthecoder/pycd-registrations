<?php

/**
 * @author Anish V. Abraham
 *
 */
class AdminController{

	public static $ACTION_ADD_CHURCH = "addChurch";
	public static $ACTION_EDIT_CHURCH = "editChurch";
	public static $ID = 'AdminController';
	public static $RENDER_ADD_CHURCH_FORM = 'renderAddChurchForm';
	public static $RENDER_CHURCH_DETAILS = 'renderChurchDetails';
	public static $RENDER_MEMBER_CHURCH_LIST = 'renderMemberChurchList';
	public static $RENDER_SUBMENU = 'renderSubmenu';
	public static $RENDER_USER_DETAILS = 'renderUserDetails';
	public static $RENDER_USER_LIST = 'renderUserList';



	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							church name, members,
	 *							pastor name, pastor email,
	 *							youth director name, youth director email
	 * @return Javascript
	 */
	public static function addChurch($request){
		$churchCheck = AdminController::areChurchFieldsValid($request);
		if ($churchCheck == FALSE){
			return;
		}

		$church = $churchCheck;
		/*@var $church Church*/
		$church->save();

		?>
		<script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			adminShowMemberChurchesList();
		</script>
		<?php
	}

	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							church name, members,
	 *							pastor name, pastor email,
	 *							youth director name, youth director email
	 * @return Javascript + bool/Church object
	 */
	public static function areChurchFieldsValid($request){
		$name = Controller::getFieldValue(Church::$NAME_LBL, $request);
		$members = Controller::getFieldValue(Church::$MEMBERS_LBL, $request);
		$pastorName = Controller::getFieldValue(Church::$PASTOR_NAME_LBL, $request);
		$pastorEmail = Controller::getFieldValue(Church::$PASTOR_EMAIL_LBL, $request);
		$ydName = Controller::getFieldValue(Church::$YD_NAME_LBL, $request);
		$ydEmail = Controller::getFieldValue(Church::$YD_EMAIL_LBL, $request);

		// Check mandatory fields
		if ($name == ""){
			Render::jsAlert('A church name is required.');
			Render::jsFieldFocus(Church::$NAME_LBL);
			return FALSE;
		}
		else if (!is_string_pure($name)){
			Render::jsAlert('Enter a valid church name.');
			Render::jsFieldFocus(Church::$NAME_LBL);
			return FALSE;
		}
		else if ($members == ""){
			Render::jsAlert('Enter approximate church membership');
			Render::jsFieldFocus(Church::$MEMBERS_LBL);
			return FALSE;
		}
		else if (preg_match("/^[0-9]+$/", $members) != 1){
			Render::jsAlert('Enter a valid whole number');
			Render::jsFieldFocus(Church::$MEMBERS_LBL);
			return FALSE;
		}
		else if ($pastorName == ""){
			Render::jsAlert("Who is your pastor?");
			Render::jsFieldFocus(Church::$PASTOR_NAME_LBL);
			return FALSE;
		}
		else if (!is_fullname_valid($pastorName)){
			Render::jsAlert("Enter a valid full name.");
			Render::jsFieldFocus(Church::$PASTOR_NAME_LBL);
			return FALSE;
		}
		else if ($pastorEmail == ""){
			Render::jsAlert("What is the pastor's email?");
			Render::jsFieldFocus(Church::$PASTOR_EMAIL_LBL);
			return FALSE;
		}
		else if (!is_email_valid($pastorEmail)){
			Render::jsAlert("Enter a valid email address.");
			Render::jsFieldFocus(Church::$PASTOR_EMAIL_LBL);
			return FALSE;
		}
		else if ($ydName == ""){
			Render::jsAlert("Who is your youth director?");
			Render::jsFieldFocus(Church::$YD_NAME_LBL);
			return FALSE;
		}
		else if (!is_fullname_valid($ydName)){
			Render::jsAlert("Enter a valid full name.");
			Render::jsFieldFocus(Church::$YD_NAME_LBL);
			return;
		}
		else if ($ydEmail == ""){
			Render::jsAlert("What is the youth director's email?");
			Render::jsFieldFocus(Church::$YD_EMAIL_LBL);
			return FALSE;
		}
		else if (!is_email_valid($ydEmail)){
			Render::jsAlert("Enter a valid email address.");
			Render::jsFieldFocus(Church::$YD_EMAIL_LBL);
			return FALSE;
		}
		else {
			$church = new Church();
			$church->name = ucwords(strtolower($name));
			$church->nMembers = $members;
			$church->pastorName = ucwords(strtolower($pastorName));
			$church->pastorEmail = $pastorEmail;
			$church->youthDirectorName = ucwords(strtolower($ydName));
			$church->youthDirectorEmail = $ydEmail;
			return $church;
		}

	}



	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							church id, church name, members,
	 *							pastor name, pastor email,
	 *							youth director name, youth director email
	 * @return Javascript
	 */
	public static function editChurch($request){
		$churchCheck = AdminController::areChurchFieldsValid($request);
		if ($churchCheck == FALSE){
			return;
		}

		// All validation completed, save to database.
		$churchId = $request[Church::$ID_FLD];
		$church = $churchCheck;
		$church->id = $churchId;
		/*@var $church Church*/
		$church->save();

		// Update any displayed names.
		?>
		<script type="text/javascript"  class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			$('.church<?=$church->id?>name').text('<?=$church->name?>');
			showChurchDetails(<?=$church->id?>)
		</script>
		<?php

		Render::jsInform('Changes saved.');
	}



	//----------------------------------------------------------------------------
	/**
	 * @return HTML + Javascript
	 */
	public static function renderAddChurchForm($request){
		?>
		<h1>(New Member Church)</h1>
		<form id="newChurchForm"
					class="inlineLabeled churchForms"
					onsubmit="javascript:return false;">
			<?php
			$church = new Church();
			AdminController::renderChurchFormFields($church);
			Render::hiddenField(Controller::$ID, AdminController::$ID);
			Render::hiddenField(Controller::$ACTION,AdminController::$ACTION_ADD_CHURCH);
			?>
		</form>
    <script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
      $('label').inFieldLabels();
    </script>
		<?php
		Render::jsFieldFocus(Church::$NAME_LBL);
	}


	//----------------------------------------------------------------------------
	/**
	 * @param array $request.
	 *							Required keys (based on labels):
	 *							church id
	 * @return HTML + Javascript
	 */
	public static function renderChurchDetails($request){
		if (!isset($request[Church::$ID_FLD])) return;
		$churchId = $request[Church::$ID_FLD];
		$church = Church::getById($churchId);
		/* @var $church Church */
		?>
		<h1 class="church<?=$church->id?>name"><?=$church->name?></h1>
		<form id="editChurchDetailsForm"
					class="inlineLabeled churchForms"
					onsubmit="javascript:return false;">
			<h2>Edit Details</h2>
			<?php
			AdminController::renderChurchFormFields($church);
			Render::hiddenField(Controller::$ID, AdminController::$ID);
			Render::hiddenField(Controller::$ACTION,AdminController::$ACTION_EDIT_CHURCH);
			Render::hiddenField(Church::$ID_FLD, $church->id);
			?>
		</form>
    <script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
      $('label').inFieldLabels();
    </script>

		<h2>Users</h2>
		<table>
			<thead>
				<td class="w40">Username</td>
				<td class="w40">Email</td>
				<td class="w10">Admin?</td>
			</thead>
			<?php
			$users = $church->getUsers();
			if (count($users) == 0){
				?>
				<tr>
					<td colspan="3">No users for this church<br />
							Use the "USERS" option on the left
							to add them.
					</td>
				</tr>
				<?php
			}
			else {
				foreach ($users as $user){
					/* @var $user User */
					?>
					<tr>
						<td><?=$user->username?></td>
						<td><?=$user->email?></td>
						<td><?= $user->isAdmin ? 'YES' : 'NO'?></td>
					</tr>
					<?php
				}
			}
			?>
		</table>


		<h2>Options</h2>
		<table>
			<tr>
				<td><input type="button" value="View As" onclick="viewas()" /></td>
				<td>View the registrations logged in as <?=$church->name?>.</td>
		</table>
		<?php
			$jsonDataViewas = array(
				Controller::$ID => Session::$ID,
				Controller::$ACTION => Session::$ACTION_VIEW_AS,
				Church::$ID_FLD => $church->id
			)
		?>
		<script type="text/javascript">
			function viewas(){
				data = <?=json_encode($jsonDataViewas)?>;
				sendRequestJson(data);
			}
		</script>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @param int $churchId The id of the church that is to be preselected
	 * @param String $name Name of the HTML select element.
	 * @return HTML
	 */
	public static function renderChurchDropDown($churchId, $name){
		?>
		<select name="<?=$name?>" id="<?=$name?>">
			<option value="0"></option>
			<?php
			$allChurches = Church::getAll();
			foreach($allChurches as $church){
				/*@var $church Church*/
				?>
				<option value="<?=$church->id?>"
					<?=$church->id == $churchId ? 'selected' : '' ?>>
					<?=$church->name?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
	}



	//----------------------------------------------------------------------------
	/**
	 * @param Church $church
	 * @return HTML
	 */
	public static function renderChurchFormFields($church){
		?>
			<p style="width:70%; margin-right: 26px;">
				<?php Render::field(
							Church::$NAME_LBL,
							'text',
							$church->name)?>
			</p>
			<p style="width:14%;">
				<?php Render::field(
							Church::$MEMBERS_LBL,
							'text',
							$church->nMembers)?>
			</p>
			<p style="width:42%; margin-right: 25px">
				<?php Render::field(
							Church::$PASTOR_NAME_LBL,
							'text',
							$church->pastorName)?>
			</p>
			<p style="width:42%; margin-right: 25px">
				<?php Render::field(
							Church::$PASTOR_EMAIL_LBL,
							'text',
							$church->pastorEmail)?>
			</p>
			<p style="width:42%; margin-right: 25px">
				<?php Render::field(
							Church::$YD_NAME_LBL,
							'text',
							$church->youthDirectorName)?>
			</p>
			<p style="width:42%; margin-right: 25px">
				<?php Render::field(
							Church::$YD_EMAIL_LBL,
							'text',
							$church->youthDirectorEmail)?>
			</p>
			<p>
				<input type="submit"
							 value="Save Changes"
							 onclick="sendForm(this)"  />
      </p>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @return HTML + Javascript
	 */
	public static function renderMemberChurchList(){
		?>
		<h1>Member Churches</h1>
		<table>
			<thead>
				<td>Name</td>
				<td></td>
			</thead>
			<tbody>
				<?php
				$allChurches = Church::getAll();
				foreach($allChurches as $church){
					AdminController::renderMemberChurchRow($church);
				}
				?>
			</tbody>
		</table>
    <input type="button" onclick="showAddChurchForm();" value="Add a Church">
		<?php
			$jsonShowAddChurchForm = array(
				Controller::$ID => AdminController::$ID,
				Controller::$ACTION => AdminController::$RENDER_ADD_CHURCH_FORM,
			);
			$jsonShowChurchDetails = array(
				Controller::$ID => AdminController::$ID,
				Controller::$ACTION => AdminController::$RENDER_CHURCH_DETAILS,
				Church::$ID_FLD => ''
			);
		?>
		<script type="text/javascript">
			function showAddChurchForm(){
				data = <?=json_encode($jsonShowAddChurchForm)?>;
        render('#rightcolumn', data);
			}
			function showChurchDetails(churchId){
				data = <?=json_encode($jsonShowChurchDetails)?>;
				data.<?=Church::$ID_FLD?> = churchId;
        render('#rightcolumn', data);
			}
		</script>
		<script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			$('#rightcolumn').empty();
		</script>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @param Church $church
	 * @return HTML
	 */
	public static function renderMemberChurchRow($church){
		?>
		<tr	onclick="showChurchDetails(<?=$church->id?>)"
				onmouseover="$(this).addClass('hover')"
				onmouseout="$(this).removeClass('hover')"
				class="pointer"
			>
			<td class="w100 church<?=$church->id?>name">
				<?=$church->name?>
			</td>
			<td class="actions w10">&nbsp</td>
		</t>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @return HTML + Javascript
	 */
	public static function renderSubmenu(){
		?>
		<h1>Admin</h1>
		<ul>
			<li onclick="adminShowMemberChurchesList()">
				Manage Churches
			</li>
			<li onclick="adminShowUserList()">
				Manage Users
			</li>
			<?=Render::htmlRenderMenuBackLink()?>
		</ul>
		<?=Render::jsEnableMenuHover()?>
		<script type="text/javascript">
			function adminShowMemberChurchesList(){
				<?=Render::jsLineRender('#middlecolumn',
							AdminController::$ID,
							AdminController::$RENDER_MEMBER_CHURCH_LIST)?>
			}

			function adminShowUserList(){
				<?=Render::jsLineRender('#middlecolumn',
							AdminController::$ID,
							AdminController::$RENDER_USER_LIST)?>
			}
		</script>
		<script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
			$('#rightcolumn').empty();
		</script>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @return HTML + Javascript
	 */
	public static function renderUserDetails($request){
		$username = $request[User::$USERNAME_FLD];
		$user = User::getByUsername($username);
		?>
		<h1><?=$username?></h1>
		<form id="editUserDetails"
					class="inlineLabeled"
					onsubmit="javascript:return false;">
			<h2>Edit Details</h2>
			<?php
			AdminController::renderUserFormFields($user);
			?>
		</form>
    <script type="text/javascript" class="<?=Render::$CUSTOM_SCRIPTS_CLASS?>">
      $('label').inFieldLabels();
    </script>
		<?php

	}

	//----------------------------------------------------------------------------
	/**
	 * @param User $user
	 * @return HTML
	 */
	public static function renderUserFormFields($user){
		?>
			<p style="width:42%; margin-right: 25px">
				<?php Render::field(
							User::$USERNAME_LBL,
							'text',
							$user->username)?>
			</p>
			<p style="width:42%; margin-right: 25px">
				<?php Render::field(
							User::$EMAIL_LBL,
							'text',
							$user->email)?>
			</p>
			<p style="width:42%; margin-right: 25px">
				<?php Render::field(
							User::$PASSWORD_LBL,
							'password',
							'')?>
			</p>
			<p style="width:42%; margin-right: 25px">
				<?php Render::field(
							User::$CONFIRM_PASSWORD_LBL,
							'password',
							'')?>
			</p>
			<p style="width:75%; margin-right: 3px">
				<?php AdminController::renderChurchDropDown(
								$user->churchId, User::$CHURCH_FLD)?>
			</p>
			<p style="width:20%">
				<select name="<?=User::$ADMIN_FLD?>">
					<option value="0" <?=$user->isAdmin ? '' : 'selected' ?>>NO</option>
					<option value="1" <?=$user->isAdmin ? 'selected' : '' ?>>YES</option>
				</select>
			</p>
		<?php
	}


	//----------------------------------------------------------------------------
	/**
	 * @return HTML + Javascript
	 */
	public static function renderUserList($request){
		?>
		<h1>Users</h1>
		<?php
		// Prepare to display all users.
		$users = User::getAll();
		?>
		<table>
			<thead>
				<tr>
					<td class="w20">Username</td>
					<td class="w70">Church</td>
					<td class="w10">Admin</td>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($users as $user){
					/* @var $user User */
					$church = $user->getChurch();
					/* @var $church Church */
					if ($church === NULL)
						$churchName = '';
					else
						$churchName = $church->name;
					?>
					<tr onclick="showUserDetails('<?=$user->username?>')"
					onmouseover="$(this).addClass('hover')"
					onmouseout="$(this).removeClass('hover')"
					class="pointer">
						<td><?=$user->username?></td>
						<td><?=$churchName?></td>
						<td><?=$user->isAdmin ? 'YES' : 'NO'?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
    <input type="button" onclick="showNewUserForm();" value="Add a user">
		<?php
		$jsonShowUserDetails = array(
			Controller::$ID => AdminController::$ID,
			Controller::$ACTION => AdminController::$RENDER_USER_DETAILS,
			User::$USERNAME_FLD => ''
		);
		?>
		<script type="text/javascript">
			function showUserDetails(username){
				data = <?=json_encode($jsonShowUserDetails)?>;
				data.<?=User::$USERNAME_FLD?> = username;
        render('#rightcolumn', data);
			}
		</script>
		<?php
	}


}//close AdminController
