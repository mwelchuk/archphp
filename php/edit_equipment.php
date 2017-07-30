<?PHP


function edit_equipment($db, $u_uid)
{
	$eq_uid = trim($_GET['uid']);
	$action = trim($_GET['action']);
	
	if(($eq_uid == '') && ($action != 'new')){
		?>
		<h2>Please provide a class</h2>
		<p>It is necessary to provide the name of the class you wish to edit.</p>
		<?php
		return (0);
	}

	if($eq_uid == 'new'){
		$e_user = trim($_POST['user']);
	}
	else{
		$query = "SELECT user FROM `equipment` WHERE `eq_uid` LIKE \"" . $eq_uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		$e_row = mysql_fetch_assoc($result);
		
		$e_user = $e_row["user"];
	}
	
	if((check_priv($u_uid, "edit_users") < 1) && ($e_user != $u_uid) && ($action != 'new')){
		no_priv();
		return(0);
	}
	
	
	
	if($action == '' || $action == 'edit'){
		edit_equip_page($eq_uid, $u_uid);
	}
	elseif($action == 'new'){
		edit_equip_page("new", $u_uid);
	}
	elseif($action == 'confirm'){
		confirm_equip_page($db, $eq_uid);
	}
	else{
		no_action();
	}
}

function edit_equip_page($eq_uid, $u_uid)
{
	//echo $name;
	if($eq_uid != "new"){
		$query = "SELECT * FROM `equipment` WHERE `eq_uid` LIKE \"" . $eq_uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$e_row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_equip&amp;uid=<?php echo $eq_uid ?>&amp;action=confirm">
	<h2>Edit Equipment</h2>
	<?php
	if($eq_uid != "new"){
	?>
		<input type="hidden" name="eq_uid" value="<?php echo $e_row["eq_uid"]; ?>">
		<input type="hidden" name="user" value="<?php echo $e_row["user"]; ?>">
	<?php
	}
	else{
	?>
		<input type="hidden" name="eq_uid" value="new">
		<input type="hidden" name="user" value="<?php echo "$u_uid"; ?>">
	<?php
	}
	?>
	
	<table cols="2">
		<tr><td><h3>Required Details</h3></td></tr>
		<tr><td>Equipment Name</td><td><input name="e_name" size="50" value="<?php echo $e_row["e_name"]; ?>" /></td></tr>
		<tr><td>Bow Class</td><td><select name="class"><?php 
		
		$query = "SELECT * FROM `class` ORDER BY `c_name`";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		while($c_row = mysql_fetch_assoc($result)){
			if($c_row["c_uid"] == $e_row["class"]){
		?>
			<option value="<?php echo $c_row["c_uid"]; ?>" selected ><?php echo $c_row["c_name"]; ?></option>
		<?php
			}
			else{
		?>
			<option value="<?php echo $c_row["c_uid"]; ?>"><?php echo $c_row["c_name"]; ?></option>
		<?php
			}
		}
		?></select></td></tr>
		<tr><td>Bow</td><td><textarea name="bow" cols="100" rows="3"><?php echo $e_row["bow"]; ?></textarea></td></tr>
		<tr><td>Arrows</td><td><textarea name="arrows" cols="100" rows="3"><?php echo $e_row["arrows"]; ?></textarea></td></tr>
		<tr><td> </td></tr>
		<tr><td><h3>Optional Details</h3></td></tr>
		<tr><td>Sight</td><td><textarea name="sight" cols="100" rows="3"><?php echo $e_row["sight"]; ?></textarea></td></tr>
		<tr><td>Stabilisation</td><td><textarea name="stabilisation" cols="100" rows="3"><?php echo $e_row["stabilisation"]; ?></textarea></td></tr>
		<tr><td>Extras</td><td><textarea name="extras" cols="100" rows="3"><?php echo $e_row["extras"]; ?></textarea></td></tr>
		<tr><td>Description</td><td><textarea name="e_desc" cols="100" rows="3"><?php echo $e_row["e_desc"]; ?></textarea></td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_equip_page($db, $name)
{

	// This is changed to 1 on error
	$error = 0;
	// This string for different string between new and edit
	$action_string = "";
	// String for fields and values to edit
	$query_string = "";
	// String for end of query
	$end_string = "";
	
	// Import Possible Values from POST
	$eq_uid =$_POST["eq_uid"];
	$user = $_POST["user"];
	$e_name = $_POST["e_name"];
	$class = $_POST["class"];
	$bow = $_POST["bow"];
	$arrows = $_POST["arrows"];
	$sight = $_POST["sight"];
	$stabilisation = $_POST["stabilisation"];
	$extras = $_POST["extras"];
	$e_desc = $_POST["e_desc"];
	
//	echo $eq_uid;
//	echo $user;
//	echo $e_name;
//	echo $class;
//	echo $bow;
//	echo $arrows;
//	echo $sight;
//	echo $extras;
//	echo $e_desc;
	
	if($eq_uid == "new"){
		
		//$query = "SELECT `eq_uid` FROM `equipment` ORDER BY `eq_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//$eq_uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `equipment` SET ";
		//$query_string = $query_string . "`eq_uid` = '$eq_uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `equipment` SET ";
		$end_string = " WHERE `eq_uid` = '$eq_uid' LIMIT 1";
		
	}
	
	// Check user not blank
	if($user == ""){
		printf("Username field blank! (How did you do that!)<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`user` = '$user' , ";
	}
	

	// Check equipment name not blank
	if($e_name == ""){
		printf("Equipment name field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`e_name` = '$e_name' , ";
	}
	
	// Check class not blank
	if($class == ""){
		printf("Class field blank! (How did you do that!)<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`class` = '$class' , ";
	}
	
	// Check bow not blank
	if($bow == ""){
		printf("Bow field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`bow` = '$bow' , ";
	}
	
	// Check arrows not blank
	if($arrows == ""){
		printf("Arrows field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`arrows` = '$arrows' , ";
	}
	
	$query_string = $query_string . "`sight` = '$sight' , ";
	$query_string = $query_string . "`stabilisation` = '$stabilisation' , ";
	$query_string = $query_string . "`extras` = '$extras' , ";
	$query_string = $query_string . "`e_desc` = '$e_desc'";
	
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
	<a href="index.php?page=view_equip&uid=<?php echo $eq_uid; ?>">View Equipment</a>

<?php
}

?> 
