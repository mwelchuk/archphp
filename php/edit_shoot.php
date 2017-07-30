<?PHP


function edit_shoot($db, $u_uid)
{
	$uid = trim($_GET['uid']);
	$action = trim($_GET['action']);
	
	if(check_priv($u_uid, "edit_shoots") < 1){
		no_priv();
		return(0);
	}
	
	if(($uid == '') && ($action != 'new')){
		?>
		<h2>Please provide a shoot</h2>
		<p>It is necessary to provide the name of the shoot you wish to edit.</p>
		<?php
		return (0);
	}

	if($uid != 'new'){
		$query = "SELECT * FROM `shoot` WHERE `sh_uid` LIKE \"" . $uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		$row = mysql_fetch_assoc($result);
	}
	
	if($action == '' || $action == 'edit'){
		edit_shoot_page($uid, $u_uid);
	}
	elseif($action == 'new'){
		edit_shoot_page("new", $u_uid);
	}
	elseif($action == 'confirm'){
		confirm_shoot_page($db, $uid);
	}
	else{
		no_action();
	}
}

function edit_shoot_page($uid, $u_uid)
{
	if($uid != "new"){
		$query = "SELECT * FROM `shoot` WHERE `sh_uid` LIKE \"" . $uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_shoot&amp;uid=<?php echo $uid ?>&amp;action=confirm">
	<h2>Edit Shoot</h2>
	<?php
	if($uid == "new"){
	?>
		<input type="hidden" name="sh_uid" value="new">
	<?php
	}
	else{
	?>
		<input type="hidden" name="sh_uid" value="<?php echo $uid; ?>">
	<?php
	}
	?>
	
	<table cols="2">
		<tr><td>Date</td><td><input name="date" value="<?php echo $row["date"]; ?>" /></td></tr>
		<tr><td>Round</td><td><?php 
		
		//First check to see if an existing session uses the shoot, if so, can't change round type.
		if($uid != "new"){
			// Build Query
			$ch_query = "SELECT `s_uid` FROM `session` WHERE `shoot`='" . $row["round"] . "'";
			// Perform SQL Query
			$ch_result = mysql_query($ch_query) or die('Query failed: ' . mysql_error());
			// Get Val
			if(mysql_num_rows($ch_result) > 0){
				$allow_round = 0;
			}
			else{
				$allow_round = 1;
			}

		}
		else{
			$allow_round = 1;
		}
		
		if($allow_round == 1){
			?><select name="round"><?php
			$r_query = "SELECT `r_uid`,`name` FROM `round` ORDER BY `name`";
			// Perform SQL Query
			$r_result = mysql_query($r_query) or die('Query failed: ' . mysql_error());
			// Get Val
			
			while($r_row = mysql_fetch_assoc($r_result)){
			
				if($row["round"] == $r_row["r_uid"]){
		?>
			<option value="<?php echo $r_row["r_uid"]; ?>" selected ><?php echo $r_row["name"]; ?></option>
		<?php
				}
				else{
		?>
			<option value="<?php echo $r_row["r_uid"]; ?>"><?php echo $r_row["name"]; ?></option>
		<?php
				}
			}
		?></select><?php  
		}
		else{
			$r_query = "SELECT `r_uid`,`name` FROM `round` WHERE `r_uid`='" . $row["round"] . "'";
			// Perform SQL Query
			$r_result = mysql_query($r_query) or die('Query failed: ' . mysql_error());
			// Get Val
			$r_row = mysql_fetch_assoc($r_result);
			echo  "<input type=\"hidden\" name=\"round\" value=\"" . $r_row["r_uid"] . "\" /> " . $r_row['name'] . "    (Scores have already been entered, therefore round type can't be changed)";
		
		}
?>		</td></tr>
		<tr><td>Location</td><td><input name="location" value="<?php echo $row["location"]; ?>" /></td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_shoot_page($db, $name)
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
	$sh_uid =$_POST["sh_uid"];
	$date = $_POST["date"];
	$round = $_POST["round"];
	$location = $_POST["location"];
	
//	echo $sh_uid;
//	echo $date;
//	echo $round;
//	echo $location;
	
	if($sh_uid == "new"){
		
		//$query = "SELECT `sh_uid` FROM `shoot` ORDER BY `sh_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//$sh_uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `shoot` SET ";
		//$query_string = $query_string . "`sh_uid` = '$sh_uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `shoot` SET ";
		$end_string = " WHERE `sh_uid` = '$sh_uid' LIMIT 1";
		
	}
	
	// Check date not blank
	if($date == ""){
		printf("Date field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`date` = '$date' , ";
	}
	

	// Check round name not blank
	if($round == ""){
		printf("Round field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`round` = '$round' , ";
	}
	
	// Check location not blank
	if($location == ""){
		printf("Location field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`location` = '$location'";
	}
	
	if($error == 0){
	
		$query = $action_string . $query_string . $end_string;

		printf("<H3>SQL Query</H3>\n<CODE>" . $query . "</CODE><BR>\n");
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
	<a href="index.php?page=shoots">View Shoots</a>

<?php
}

?> 
