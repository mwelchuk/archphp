<?PHP

/*
 * This is the edit/add user functionality.
 *
 * This script should allow current users to be edited and new users to be 
 * added, since adding users is kinda a subset of editing or vs-vs.
 *
 * The edit_user() function - which is the function that should be called externally
 * controls the add/edit process, which takes place in 2 steps. 
 * 
 * First the information is displayed so the user can edit the fields. This is 
 * accomplised by the edit_page() function.
 * 
 * A second pass through the edit functionality is required to check the fields and
 * either ask the user to edit some of the fields ro to confirm that the operation
 * has been successful. This is accomplished by the confirm_page() function.
 *
*/
function edit_user($db, $u_uid)
{
	$name = trim($_GET['name']);
	$action = trim($_GET['action']);
	
	if($name == ''){
		$name = $u_uid;
	}
	
	//See if user allowed to edit other users
	$edit_user = check_priv($u_uid, "edit_users");
	
	//See if user allowed to edit privileges
	$edit_priv = check_priv($u_uid, "edit_privileges");
	
	if(($edit_user < 1) && ($u_uid != $name)){
		no_priv();
		return(0);
	}
	
	if($action == '' || $action == 'edit'){
		edit_user_page($name, $edit_user, $edit_priv);
	}
	elseif($action == 'new'){
		if($edit_user < 1){
			no_priv();
		}
		else{
			edit_user_page("new", $edit_user, $edit_priv);
		}
	}
	elseif($action == 'confirm'){
		confirm_user_page($db, $u_uid, $name, $edit_user, $edit_priv);
	}
	elseif($action == 'delete'){
		if($edit_user < 1){
			no_priv();
		}elseif($name == $u_uid){
		?>
		<h2>Error!</h2>
		<p>You cannot delete yourself!</p>
		<?php
		}
		else{
			delete_user_page($name);
		}
	}
	elseif($action == 'really_delete'){
		if($edit_user < 1){
			no_priv();
		}
		else{
			really_delete_user_page($name);
		}
	}
	else{
		no_action();
	}
}

function edit_user_page($name, $edit_user, $edit_priv)
{

	if($name != "new"){
		$query = "SELECT * FROM `user` WHERE `u_uid` LIKE \"" . $name . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_user&amp;name=<?php echo $name ?>&amp;action=confirm">
	<h2>Edit User</h2>
	<input type="hidden" name="u_uid" value="<?php echo $name; ?>">
	<table>
	<tr><td><h3>Basic Details</h3></td></tr>
	<tr><td>First Name</td><td><input name="f_name" value="<?php echo $row["f_name"]; ?>" /></td></tr>
	<tr><td>Surname Name</td><td><input name="s_name" value="<?php echo $row["s_name"]; ?>" /></td></tr>
	<tr><td>Description</td><td><textarea name="description" cols="100" rows="5"><?php echo $row["description"]; ?></textarea></td></tr>
	<tr><td>Created</td><td><?php echo $row["created"]; ?></td></tr>
	<tr><td>Last Logged in</td><td><?php echo $row["last_log"]; ?></td></tr>
	<tr><td><h3>Account Details</h3></td></tr>
	<tr><td>Username Name</td><td><input name="u_name" value="<?php echo $row["u_name"]; ?>" /></td></tr>
	<tr><td>New Password</td><td><input type="password" name="password1" value="" />(leave blank if you do not wish to change your password)</td></tr>
	<tr><td>New Password</td><td><input type="password" name="password2" value="" /></td></tr>
	<tr><td><h3>Privileges</h3></td></tr>
<?PHP
	$query = "SELECT * FROM `priv_level` ORDER BY `p_uid`";
	// Perform SQL Query
	$priv_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	if(($edit_priv < 1) && ($new != 1)){
		?>
		<tr><td colspan="2">Note: You do not have sufficient privileges to set privileges!</td></tr>
		<?php
	}
	// Get Val
	while($priv_row = mysql_fetch_array($priv_result, MYSQL_NUM)){
		if($edit_priv < 1){
			if($new == 1){
				?>
				<tr><td colspan="2">You do not have sufficient privileges to set privileges! Defaults will be set.</td></tr>
				<?php
			}
			else{
				if((($priv_row[0]+1)-1)&(($row["privilege_level"]+1)-1)){
					printf("<tr><td>" . $priv_row[1] . "</td><td>yes</td></tr>");
				}else{
					printf("<tr><td>" . $priv_row[1] . "</td><td>no</td></tr>");
				}
			}
		}else{
			if((($priv_row[0]+1)-1)&(($row["privilege_level"]+1)-1)){
				printf("<tr><td><input type=\"checkbox\" name=\"p_". $priv_row[1]. "\" value=\"1\" checked>". $priv_row[1] . "</td></tr>");
			}else{
				printf("<tr><td><input type=\"checkbox\" name=\"p_". $priv_row[1]. "\" value=\"1\">". $priv_row[1] . "</td></tr>");
			}
		}
	}
?>
	
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_user_page($db, $current_u_uid, $name, $edit_user, $edit_priv)
{

	// This is changed to 1 on error
	$error = 0;
	// Mark if new
	$new = 0;
	// This string for different string between new and edit
	$action_string = "";
	// String for fields and values to edit
	$query_string = "";
	// String for end of query
	$end_string = "";
	
	// Import Possible Values from POST
	$u_uid = $_POST["u_uid"];
	$u_name = $_POST["u_name"];
	$f_name = $_POST["f_name"];
	$s_name = $_POST["s_name"];
	$description = $_POST["description"];
	$password1 = $_POST["password1"];
	$password2 = $_POST["password2"];
	
	
	// Display Possible Values from POST
	/*
	printf("u_uid = \"" . $_POST["u_uid"] . "\"<br />\n");
	printf("u_name = \"" . $_POST["u_name"] . "\"<br />\n");
	printf("f_name = \"" . $_POST["f_name"] . "\"<br />\n");
	printf("s_name = \"" . $_POST["s_name"] . "\"<br />\n");
	printf("description = \"" . $_POST["description"] . "\"<br />\n");
	printf("old_password = \"" . $_POST["old_password"] . "\"<br />\n");
	printf("password1 = \"" . $_POST["password1"] . "\"<br />\n");
	printf("password2 = \"" . $_POST["password2"] . "\"<br />\n");
	*/
	
	
	//Check to see if new user
	if($u_uid == "new"){
		//New User - Get unused u_uid
		//$query = "SELECT `u_uid` FROM `user` ORDER BY `u_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//Add one to current highest U_UID
		//$u_uid = $row[0] + 1;
		
		// Declare new user
		$new = 1;
		
		//$query_string = $query_string . "`u_uid` = '$u_uid' , ";
		//New User - Need insert action_string
		$action_string = "INSERT INTO `user` SET ";
		//New User - Need created date
		$query_string = $query_string . "`created` = '" . date ("Y-m-d") . "' , ";
	
	}
	else{
		//Existing User - New Update string
		$action_string = "UPDATE `user` SET ";
		//And end String
		$end_string = " WHERE `u_uid` = '$u_uid' LIMIT 1";
	}
	
	// Check username not blank and not same as existing...
	$query = "SELECT `u_uid` FROM `user` WHERE `u_name` LIKE \"" . $u_name . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	//get result
	$row = mysql_fetch_array($result, MYSQL_NUM);
	if($u_name == ""){
		printf("Username field blank!<br />\n");
		$error = 1;
	} 
	elseif(($row[0] != "") && ($row[0] != $u_uid)){
		printf("Error: Name already exists<br />\n");
		$error = 1;
	}
	else{
		$query_string = $query_string . "`u_name` = '$u_name' , ";
	}
	
	// Check Important Fields not blank
	if(($f_name == "") || ($s_name == "")){
		printf("Important field blank!<br />\n");
		$error = 1;
		
	}else{
		$query_string = $query_string . "`f_name` = '$f_name' , `s_name` = '$s_name'";
	}
	
	
	// See if current user can edit privileges and work out privilege level if necessary
	if($edit_priv > 0){
		
		$query = "SELECT * FROM `priv_level` ORDER BY `p_uid`";
		// Perform SQL Query
		$priv_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		while($priv_row = mysql_fetch_array($priv_result, MYSQL_NUM)){
			$privilege_level += $_POST["p_$priv_row[1]"] * $priv_row[0];
			//printf($priv_row[1]. " = \"" . $_POST["p_" . $priv_row[1]] * $priv_row[0] . "\"<br />\n");
		}
		//printf("privilege_level = \"" . $privilege_level . "\"<br />\n");
		$query_string = $query_string . " , `privilege_level` = '$privilege_level'";
	}
	//If new user and now privileges to edit privileges, set to default
	elseif($new == 1){
	
		$query_string = $query_string . " , `privilege_level` = '1'";
	
	}
	
	
	$query_string = $query_string . " , `description` = '$description'";
	
	
	// Password required if new
	if(($password1 == "") && ($new == 1)){
		printf("Password Required!<br />\n");
		$error = 1;

	}
	
	// Check if changing
	if($password1 != ""){
		if($password1 != $password2){
			//Mis-matched Passwords
			printf("New Passwords not the same!<br />\n");
			$error = 1;
		}
		else{
			//Passwords OK!
			//printf("Passwords OK<br />\n");
			$query_string = $query_string . " , `password` = '" . md5($password1) . "'";
			//If current user change password saved in session!
			if($current_u_uid == $u_uid){
				$_SESSION["password"] = $password1;
			}
		}
	}

	if($error == 0){
	
		$query = $action_string . $query_string . $end_string;

		//printf("<H3>SQL Query</H3>\n<CODE>" . $query . "</CODE><BR>\n");
		mysql_query($query,$db) or die("Invalid query:" . mysql_error());

?>
	<h2>You have been successful!</h2>
<?php
//	<p>Below is the SQL command that was used:</p>
//<?php	
//	echo $action_string; 
//	echo $query_string;
//	echo $end_string;
//	echo "<br />\n";
	
	}
	else{
?>
	<h2>Error!</h2>
	<p>There is an error in your input, please press back and try again!</p>
<?php	
	}

?>
	<a href="index.php?page=users">User List</a>

<?php
}

function delete_user_page($name){
?>
	<h2>Delete User</h2>
	<p>Are you sure you wish to do this?</p>
	<p>Deleting the user will remove all user information, scores and equipment information from the database. This is quite a nasty think to do.</p> 
	<p><b>Please note:</b> It might be worth leaving old users on so that historic records can be maintained.</p>

	<table><tr>
	<td>
		<form method="post" action="index.php?page=edit_user&amp;name=<?php echo $name ?>&amp;action=really_delete">
			<input type="hidden" name="name" value="<?php echo $row["name"]; ?>">
			<input type="submit" value="Yes - I have read what is written above and understand" />
		</form>
	</td>
	<td>
		<form method="get" action="index.php">
			<input type="hidden" name="page" value="main" />
			<input type="submit" value="NO - Cancel" />
		</form>
	</td>
	</tr></table>
<?php
}

function really_delete_user_page($name){
	
	//Delete all references from Equipment Table
	$query = "DELETE FROM `equipment` WHERE `user` LIKE '$name'";
	// Perform SQL Query
	mysql_query($query) or die('Query failed: ' . mysql_error());
	
	//Delete all references from User Table
	$query = "DELETE FROM `user` WHERE `u_uid` LIKE '$name'";
	// Perform SQL Query
	mysql_query($query) or die('Query failed: ' . mysql_error());

	//Find all session IDs
	$query = "SELECT `s_uid` FROM `session` WHERE `user` LIKE '$name'";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	//Loop for each ID
	while($row = mysql_fetch_assoc($result)){
		//Delete all references from End Table
		$query = "DELETE FROM `end` WHERE `session` LIKE '" . $row['s_uid'] . "'";
		// Perform SQL Query
		mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	
	//Delete all references from Session Table
	$query = "DELETE FROM `session` WHERE `user` LIKE '$name'";
	// Perform SQL Query
	mysql_query($query) or die('Query failed: ' . mysql_error());
	
?>
	<h2>User Deleted</h2>
	<p>It's done now. No going back.</p>
	<a href="index.php?page=users">User List</a>
<?php
}

?>