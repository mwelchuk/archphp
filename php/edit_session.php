<?PHP


function edit_session($db, $u_uid)
{
	$uid = trim($_GET['uid']);
	$action = trim($_GET['action']);
	
	if((($uid == '') && ($action != 'new'))){
		?>
		<h2>Please provide a session</h2>
		<p>It is necessary to provide the name of the session you wish to edit.</p>
		<?php
		return (0);
	}

	if($action == 'new'){
		$user = $u_uid;
	}
	elseif(($action == 'score') || ($action == 'confirm')){
		$user = trim($_POST['user']);
	}
	else{
		$query = "SELECT `user` FROM `session` WHERE `s_uid` LIKE \"" . $uid . "\"";
		// Perform SQL Query
		$s_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		$s_row = mysql_fetch_assoc($s_result);
		
		$user = $s_row["user"];
	}
	
	if((check_priv($u_uid, "edit_users") < 1) && ($user != $u_uid)){
		no_priv();
		return(0);
	}
	
	
	
	if($action == '' || $action == 'edit'){
		edit_session_page($uid, $u_uid);
	}
	elseif($action == 'new'){
		edit_session_page("new", $u_uid);
	}
	elseif($action == 'score'){
		edit_score_page($uid, $u_uid);
	}
	elseif($action == 'confirm'){
		confirm_session_page($db, $uid);
	}
	else{
		no_action();
	}
}

function edit_session_page($uid, $u_uid)
{
	//echo $name;
	if($uid != "new"){
		$query = "SELECT * FROM `session` WHERE `s_uid` LIKE \"" . $uid . "\"";
		// Perform SQL Query
		$s_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$s_row = mysql_fetch_assoc($s_result);
	}
?>
	<form method="post" action="index.php?page=edit_session&amp;uid=<?php echo $uid ?>&amp;action=score">
	<h2>Edit Score</h2>
<?php
	if($uid != "new"){
		$user = $s_row["user"];
?>
		<input type="hidden" name="s_uid" value="<?php echo $s_row["s_uid"]; ?>">
		<input type="hidden" name="user" value="<?php echo $s_row["user"]; ?>">
	<?php
	}
	else{
		$user = $u_uid;
?>
		<input type="hidden" name="s_uid" value="new">
		<input type="hidden" name="user" value="<?php echo $u_uid; ?>">
<?php
	}
?>
<?php 
	// Build query
	$e_query = "SELECT `eq_uid`, `e_name` FROM `equipment` WHERE `user` LIKE '" . $user . "' ORDER BY `e_name`";
	// Perform SQL Query
	$e_result = mysql_query($e_query) or die('Query failed: ' . mysql_error());
	// Check to see equipment defined
	if(mysql_num_rows($e_result) < 1){
?>
	<h3>You currently do not have any equipment defined!</h3>
	<p>It is necessary to have at least one set of equipment associated with you before adding scores to the database. Please <a href="index.php?page=edit_equip&amp;action=new">add</a> some before continuing.</p>
<?php
	}
	else{
?>
	<h3>Details</h3>
	<table cols="2">
		<tr>
			<td>Equipment</td>
			<td>
				<select name="equipment">
					<option value="">--Select Equipment--</option>
		<?php
		while($e_row = mysql_fetch_assoc($e_result)){
			if($s_row["equipment"] == $e_row["eq_uid"]){
		?>
			<option value="<?php echo $e_row["eq_uid"]; ?>" selected ><?php echo $e_row["e_name"]; ?></option>
		<?php
			}
			else{
		?>
			<option value="<?php echo $e_row["eq_uid"]; ?>"><?php echo $e_row["e_name"]; ?></option>
		<?php
			}
		}
		?></select></td></tr>
		
		<tr><td>Shoot</td><td>
		<?php
		if($uid == "new"){
		?>
		<select name="shoot"><?php 
			
			$sh_query = "SELECT * FROM `shoot` ORDER BY `date`";
			// Perform SQL Query
			$sh_result = mysql_query($sh_query) or die('Query failed: ' . mysql_error());
			// Get Val
		?>
			<option value="">--Select Shoot--</option>
		<?php
			while($sh_row = mysql_fetch_assoc($sh_result)){
				
				$r_query = "SELECT `name` FROM `round` WHERE `r_uid` LIKE " . $sh_row["round"];
				//echo $r_query;
				// Perform SQL Query
				$r_result = mysql_query($r_query) or die('Query failed: ' . mysql_error());
				// Get Val
				$r_row = mysql_fetch_assoc($r_result);
				
				if($sh_row["sh_uid"] == $s_row["shoot"]){
		?>
			<option value="<?php echo $sh_row["sh_uid"]; ?>" selected ><?php echo $sh_row["date"] . " : " . $r_row["name"] . " - " . $sh_row["location"]; ?></option>
		<?php
				}
				else{
		?>
			<option value="<?php echo $sh_row["sh_uid"]; ?>"><?php echo $sh_row["date"] . " : " . $r_row["name"] . " - " . $sh_row["location"]; ?></option>
		<?php
				}
			}
		?></select>
		<?php
		}
		else{
			$sh_query = "SELECT * FROM `shoot` WHERE `sh_uid` LIKE '" . $s_row["shoot"] . "'";
			// Perform SQL Query
			$sh_result = mysql_query($sh_query) or die('Query failed: ' . mysql_error());
			// Get Val
			$sh_row = mysql_fetch_assoc($sh_result);
			
			$r_query = "SELECT `name` FROM `round` WHERE `r_uid` LIKE " . $sh_row["round"];
			// Perform SQL Query
			$r_result = mysql_query($r_query) or die('Query failed: ' . mysql_error());
			// Get Val
			$r_row = mysql_fetch_assoc($r_result);
			
			echo $sh_row["date"] . " : " . $r_row["name"] . " - " . $sh_row["location"] . "\n";
			echo "<input type=\"hidden\" name=\"shoot\" value=\"" . $sh_row["sh_uid"] . "\">\n";
		}
		?>
		</td></tr>
	</table>
	
	<input type="submit" value="Continue"/>
<?php
	}
?>
	</form>
<?php

}

function edit_score_page($uid, $u_uid)
{
	$s_uid = $_POST["s_uid"];
	$user = $_POST["user"];
	$equipment = $_POST["equipment"];
	$shoot = $_POST["shoot"];
	
	$eq_query = "SELECT `e_name` FROM `equipment` WHERE `eq_uid` LIKE " . $equipment;
	// Perform SQL Query
	$eq_result = mysql_query($eq_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$eq_row = mysql_fetch_assoc($eq_result);
	
	$sh_query = "SELECT `date`, `round` FROM `shoot` WHERE `sh_uid` LIKE " . $shoot;
	// Perform SQL Query
	$sh_result = mysql_query($sh_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$sh_row = mysql_fetch_assoc($sh_result);
	
	$r_query = "SELECT `name`,`scoring` FROM `round` WHERE `r_uid` LIKE " . $sh_row['round'];
	// Perform SQL Query
	$r_result = mysql_query($r_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$r_row = mysql_fetch_assoc($r_result);
	
?>
	<form method="post" action="index.php?page=edit_session&amp;uid=<?php echo $uid ?>&amp;action=confirm">
	<h3>Score</h3>
	<p>Please enter your score data below, one arrow per field. Totals will be calculated automatically.</p> 
	<p>Valid values are: 
	<?php 
	
	$s_query = "SELECT `values` FROM `scoring` WHERE `ss_uid` LIKE '" . $r_row['scoring'] . "'";
	// Perform SQL Query
	$s_result = mysql_query($s_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$s_row = mysql_fetch_assoc($s_result);
	
	echo $s_row['values'];

	?>
	</p>
	<input type="hidden" name="s_uid" value="<?php echo $s_uid; ?>">
	<input type="hidden" name="user" value="<?php echo $user; ?>">
	<input type="hidden" name="equipment" value="<?php echo $equipment; ?>">
	<input type="hidden" name="shoot" value="<?php echo $shoot; ?>">
	<table border="1" cols="9">
	<tr><td colspan="9">Equipment : <?php echo $eq_row['e_name']; ?></td></tr>
	<tr><td colspan="4">Round : <?php echo $r_row['name']; ?></td><td colspan="5">Date : <?php echo $sh_row['date']; ?></td></tr>
<?php
	
	$rd_query = "SELECT * FROM `round_dists` WHERE `round` LIKE " . $sh_row['round'];
	// Perform SQL Query
	$rd_result = mysql_query($rd_query) or die('Query failed: ' . mysql_error());
	// Get Val
	while($rd_row = mysql_fetch_assoc($rd_result)){
?>
	<tr><td>End</td><td>E/T</td><td>End</td><td>E/T</td><td>H</td><td>S</td><td>X</td><td>G</td><td>R/T</td></tr>
<?php

		// Loop through ends
		for($i=0; $i<$rd_row['ends']; $i++){
			
//----------------------------------------------------------------------------------------------------------------
			
			$e_query = "SELECT `value1`,`value2`,`value3`,`value4`,`value5`,`value6` FROM `end` WHERE `session` LIKE '" . $s_uid . "' AND `distance` LIKE '" . $rd_row['distance'] . "' AND `end` LIKE '" . $i . "'";
			// Perform SQL Query
			$e_result = mysql_query($e_query) or die('Query failed: ' . mysql_error());
			// Get Val
			$e_row = mysql_fetch_assoc($e_result);
			
			if($i % 2 == 0){
?>
	<tr><td>
<?php
				// Loop through values
				for($j=1; $j<7; $j++){
?>
		<input type="text" size="2" name="arrow<?php echo $rd_row['distance'] . "-$i-$j"; ?>" value="<?php echo $e_row['value' . $j]; ?>" />
<?php
				}
?>
	</td><td></td>
<?php
			}
			else{
?>
	<td>
<?php
				// Loop through values
				for($j=1; $j<7; $j++){
?>
		<input type="text" size="2" name="arrow<?php echo $rd_row['distance'] . "-$i-$j"; ?>" value="<?php echo $e_row['value' . $j]; ?>" />
<?php
				}
?>
	</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
<?php
			}
		}
		
		if($rd_row['ends'] % 2 != 0){
?>
	<td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
<?php
		}
		
		$d_query = "SELECT `distance`, `unit` FROM `distance` WHERE `d_uid` LIKE " . $rd_row['distance'];
		// Perform SQL Query
		$d_result = mysql_query($d_query) or die('Query failed: ' . mysql_error());
		// Get Val
		$d_row = mysql_fetch_assoc($d_result);
		
?>
	<tr><td colspan="4">Total at <?php echo $d_row['distance']; if($d_row['unit'] == 'm'){ echo " m"; }else{ echo " yrds"; } ?></td><td></td><td></td><td></td><td></td><td></td></tr>
<?php
	}
?>
	
	<tr><td colspan="4">Handicap ..... Grand Total</td><td></td><td></td><td></td><td></td><td></td></tr>
	</table>
	
	<input type="submit" value="Submit"/>
	
	</form>
<?php
}

function confirm_session_page($db, $uid)
{

	// This is changed to 1 on error
	$error = 0;
	// This string for different string between new and edit
	$action_string = "";
	// String for fields and values to edit
	$query_string = "";
	// String for end of query
	$end_string = "";
	
	// Use to count up score
	$score = 0;
	
	// Import Possible Values from POST
	$s_uid = $_POST["s_uid"];
	$user = $_POST["user"];
	$equipment = $_POST["equipment"];
	$shoot = $_POST["shoot"];
		
//	echo $s_uid;
//	echo $user;
//	echo $equipment;
//	echo $shoot;
	
	if($uid == "new"){
		
		$query = "SELECT `s_uid` FROM `session` ORDER BY `s_uid` DESC";
		// Perform SQL Query
		//echo "Query:" . $query . "<br />\n";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `session` SET ";
		$query_string = $query_string . "`s_uid` = '$uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `session` SET ";
		$end_string = " WHERE `s_uid` = '$uid' LIMIT 1";
		
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
	if($equipment == ""){
		printf("Equipment name field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`equipment` = '$equipment' , ";
	}
	
	// Check shoot not blank
	if($shoot == ""){
		printf("Shoot field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`shoot` = '$shoot' , ";
	}
	
	// Now check scoring!
	$sh_query = "SELECT `round` FROM `shoot` WHERE `sh_uid` LIKE " . $shoot;
	// Perform SQL Query
	//echo "Query:" . $sh_query . "<br />\n";
	$sh_result = mysql_query($sh_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$sh_row = mysql_fetch_assoc($sh_result);
	
	$r_query = "SELECT `scoring` FROM `round` WHERE `r_uid` LIKE " . $sh_row['round'];
	// Perform SQL Query
	//echo "Query:" . $r_query . "<br />\n";
	$r_result = mysql_query($r_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$r_row = mysql_fetch_assoc($r_result);
	
	$s_query = "SELECT `values` FROM `scoring` WHERE `ss_uid` LIKE " . $r_row['scoring'];
	// Perform SQL Query
	//echo "Query:" . $s_query . "<br />\n";
	$s_result = mysql_query($s_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$s_row = mysql_fetch_assoc($s_result);
	
	//echo "VALUES:" . $s_row['values'] . "<br />\n";
	$values = explode(",", $s_row['values']);
	//for($i=0;$i<count($values);$i++){
	//	echo "Value[" . $i . "]:" . $values[$i] . "<br />\n";
	//}
	$rd_query = "SELECT * FROM `round_dists` WHERE `round` LIKE " . $sh_row['round'];
	// Perform SQL Query
	$rd_result = mysql_query($rd_query) or die('Query failed: ' . mysql_error());
	// Get Val
	while($rd_row = mysql_fetch_assoc($rd_result)){
		// Loop through ends
		for($i=0; $i<$rd_row['ends']; $i++){
			// Loop through values
			for($j=1; $j<7; $j++){
				// Check values valid
				$arrow = strtolower(trim($_POST["arrow" . $rd_row['distance'] . "-" . $i . "-" . $j]));
				//echo "arrow:" . $arrow . "<br />\n";
				$valid_arrow = 0;
				if(is_numeric($arrow)) $score += $arrow;
				elseif($arrow == 'x') $score += 10;
				foreach($values as $valid){
					if($arrow == trim($valid)) $valid_arrow = 1;
				}
				if($valid_arrow == 0){
					printf("Invalid field!<br />\n");
					$error = 1;
				}
			}
		}
	}
	
	$query_string = $query_string . "`score` = '$score'";
	
	if($error == 0){
	
		$query = $action_string . $query_string . $end_string;

		//printf("<H3>SQL Query</H3>\n<CODE>" . $query . "</CODE><BR>\n");
		mysql_query($query,$db) or die("Invalid query:" . mysql_error());
		
		$del_query = "DELETE FROM `end` WHERE `session` LIKE '$uid'";
		// Perform SQL Query
		mysql_query($del_query) or die('Query failed: ' . mysql_error());
		
		$rd_query = "SELECT * FROM `round_dists` WHERE `round` LIKE " . $sh_row['round'];
		// Perform SQL Query
		$rd_result = mysql_query($rd_query) or die('Query failed: ' . mysql_error());
		// Get Val
		while($rd_row = mysql_fetch_assoc($rd_result)){
			
			// Loop through ends
			for($i=0; $i<$rd_row['ends']; $i++){
				//$e_query = "SELECT `e_uid` FROM `end` ORDER BY `e_uid` DESC";
				// Perform SQL Query
				//echo "Query:" . $query . "<br />\n";
				//$e_result = mysql_query($e_query) or die('Query failed: ' . mysql_error());
				// Get Val
				//$e_row = mysql_fetch_array($e_result, MYSQL_NUM);
				//$e_uid = $e_row[0] + 1;
				//$end_query = "INSERT INTO `end` SET `e_uid` = '$e_uid' , `session` = '$uid' , `distance` = '" . $rd_row['distance'] . "'";
				$end_query = "INSERT INTO `end` SET `session` = '$uid' , `distance` = '" . $rd_row['distance'] . "'";
				$end_query .= " , `end` = '$i'";
				// Loop through values
				for($j=1; $j<7; $j++){
					$end_query .= " , `value$j` = '" . trim($_POST["arrow" . $rd_row['distance'] . "-" . $i . "-" . $j]) . "'";
				}
				//printf("<H3>SQL Query</H3>\n<CODE>" . $end_query . "</CODE><BR>\n");
				mysql_query($end_query,$db) or die("Invalid query:" . mysql_error());
			}
		}
		
		
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
	<a href="index.php?page=sessions">View Sessions</a>

<?php
}

?> 
