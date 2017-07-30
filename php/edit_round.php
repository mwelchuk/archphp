<?PHP


function edit_round($db, $u_uid)
{
	$uid = trim($_GET['uid']);
	$action = trim($_GET['action']);
	if($action == "confirm"){
		$action = trim($_POST['action']);
	}
	
	if(check_priv($u_uid, "edit_rounds") < 1){
		no_priv();
		return(0);
	}
	if(($uid == '') && ($action != 'new')){
		?>
		<h2>Please provide a round</h2>
		<p>It is necessary to provide the name of the round you wish to edit.</p>
		<?php
		return (0);
	}

	if($action == '' || $action == 'edit'){
		edit_round_page($uid);
	}
	elseif($action == 'new'){
		edit_round_extended_page("new");
	}
	elseif($action == 'Add Distance'){
		edit_round_extended_page($uid);
	}
	elseif($action == 'Save'){
		confirm_round_page($db, $uid);
	}
	else{
		no_action();
	}
}

function edit_round_page($uid)
{
	$query = "SELECT * FROM `round` WHERE `r_uid` LIKE \"" . $uid . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	$row = mysql_fetch_assoc($result);

?>
	<form method="post" action="index.php?page=edit_round&amp;uid=<?php echo $uid ?>&amp;action=confirm">
	<h2>Edit Round</h2>
	<input type="hidden" name="uid" value="<?php echo $uid; ?>">
	<h3>Details</h3>
	<table cols="2">
		<tr><td>Round Name</td><td><input name="name" value="<?php echo $row["name"]; ?>" /></td></tr>
		<tr><td>Scoring System</td><td><select name="scoring">
<?php 
	$query = "SELECT ss_uid,name FROM `scoring` ORDER BY `name`";
	// Perform SQL Query
	$ss_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	while($ss_row = mysql_fetch_assoc($ss_result)){
?>
			<option value="<?php echo $ss_row["ss_uid"]; ?>" <?php if($ss_row["ss_uid"] == $row["scoring"]) echo "selected"; ?> ><?php echo $ss_row["name"]; ?></option>
<?php
	}
?>
		</select></td></tr>
	</table>
	<h3>Distance Information</h3>
	<table>
	<tr><td>Distance</td><td>Face Type</td><td>Number of Ends (6 arrows)</td></tr>
<?php
	
	// Get list of distances and number of ends for this round
	$query = "SELECT * FROM `round_dists` WHERE `round` LIKE \"" . $row["r_uid"] . "\"";
	// Perform SQL Query
	$rd_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	
	// Define counter
	$count = 0;
	
	// Loop for each distance in the round
	while($rd_row = mysql_fetch_assoc($rd_result)){
		// Build distance options
		echo "<tr><td><select name=\"dist" . $count . "\">";
		// Get list of available distances
		$query = "SELECT * FROM `distance` ORDER BY `distance`";
		// Perform SQL Query
		$d_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Add Blank row as default
		echo "<option value=\"\">--Select Distance--</option>\n";
		while($d_row = mysql_fetch_assoc($d_result)){
			if($rd_row["distance"] == $d_row["d_uid"]){
				echo "<option value=\"" . $d_row['d_uid'] . "\" selected>" . $d_row['distance']; 
				if($d_row['unit'] == 'm') echo " m"; 
				else echo " yrds";
				echo "</option>\n";
			}
			else{
				echo "<option value=\"" . $d_row['d_uid'] . "\">" . $d_row['distance']; 
				if($d_row['unit'] == 'm') echo " m";
				else echo " yrds";
				echo "</option>\n";
			}
		}
		echo "</select></td>";
		// Build face box
		echo "<td><select name=\"face" . $count . "\">";
	
		$query = "SELECT f_uid,f_name FROM `face` ORDER BY `f_name`";
		// Perform SQL Query
		$f_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		while($f_row = mysql_fetch_assoc($f_result)){
?>
			<option value="<?php echo $f_row["f_uid"]; ?>" <?php if($f_row["f_uid"] == $row["face"]) echo "selected"; ?> ><?php echo $f_row["f_name"]; ?></option>
<?php
	}
		echo "</select></td>";

		// Build ends box
		echo "<td><input name=\"ends" . $count . "\" value=\"" . $rd_row['ends'] . "\" /></td></tr>";
		$count++;
	}
?>
	</table>
	<input type="hidden" name="count" value="<?php echo $count; ?>">
	<input type="submit" name="action" value="Add Distance"/>
	<input type="submit" name="action" value="Save"/>
	</form>
<?php
}

function edit_round_extended_page($uid)
{
?>
	<form method="post" action="index.php?page=edit_round&amp;uid=<?php if($uid == "new"){ echo "new"; }else{ echo trim($_POST['uid']);} ?>&amp;action=confirm">
	<h2>Edit Round</h2>
<?php
	if($uid == "new"){
?>
		<input type="hidden" name="uid" value="new">
<?php
	}
	else{
?>
		<input type="hidden" name="uid" value="<?php echo trim($_POST['uid']); ?>">
<?php
	}
?>
	<h3>Details</h3>
	<table cols="2">
		<tr><td>Round Name</td><td><input name="name" value="<?php echo trim($_POST["name"]); ?>" /></td></tr>
		<tr><td>Scoring System</td><td><select name="scoring">
<?php 
	$query = "SELECT ss_uid,name FROM `scoring` ORDER BY `name`";
	// Perform SQL Query
	$ss_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	while($ss_row = mysql_fetch_assoc($ss_result)){
?>
			<option value="<?php echo $ss_row["ss_uid"]; ?>" <?php if($ss_row["ss_uid"] == trim($_POST["scoring"])) echo "selected"; ?> ><?php echo $ss_row["name"]; ?></option>
<?php
	}
?>
		</select></td></tr>
	</table>
	<h3>Distance Information</h3>
	<table>
	<tr><td>Distance</td><td>Face Type</td><td>Number of Ends (6 arrows)</td></tr>
<?php
	
	$count = trim($_POST['count']);
	
	if($count == ''){
		$count = 0;
	}
	
	// Loop for each saved entry
	for($i = 0; $i < $count; $i++){
		// Build distance options
		echo "<tr><td><select name=\"dist" . $i . "\">";
		// Get list of available distances
		$query = "SELECT * FROM `distance` ORDER BY `distance`";
		// Perform SQL Query
		$d_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Add Blank row as default
		echo "<option value=\"\">--Select Distance--</option>\n";
		while($d_row = mysql_fetch_assoc($d_result)){
			if(trim($_POST['dist' . $i]) == $d_row["d_uid"]){
				echo "<option value=\"" . $d_row['d_uid'] . "\" selected>" . $d_row['distance']; 
				if($d_row['unit'] == 'm') echo " m"; 
				else echo " yrds";
				echo "</option>\n";
			}
			else{
				echo "<option value=\"" . $d_row['d_uid'] . "\">" . $d_row['distance']; 
				if($d_row['unit'] == 'm') echo " m";
				else echo " yrds";
				echo "</option>\n";
			}
		}
		echo "</select></td>";
		// Build face type
		echo "<td><select name=\"face" . $i . "\">";
		// Add Blank row as default
		echo "<option value=\"\" selected>--Select Face--</option>\n";
		$query = "SELECT f_uid,f_name FROM `face` ORDER BY `f_name`";
		// Perform SQL Query
			$f_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		while($f_row = mysql_fetch_assoc($f_result)){

?>
		<option value="<?php echo $f_row["f_uid"]; ?>" <?php if($f_row["f_uid"] == trim($_POST['face' . $i])) echo "selected"; ?> ><?php echo $f_row["f_name"]; ?></option>
<?php
	}
	
		echo "</select></td>";

		// Build ends box
		echo "<td><input name=\"ends" . $i . "\" value=\"" . trim($_POST['ends' . $i]) . "\" /></td></tr>\n";
	}
	
	//Now add extra row!
	echo "<tr><td><select name=\"dist" . $count . "\">";
	// Get list of available distances
	$query = "SELECT * FROM `distance` ORDER BY `distance`";
	// Perform SQL Query
	$d_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Add Blank row as default
	echo "<option value=\"\" selected>--Select Distance--</option>\n";
	while($d_row = mysql_fetch_assoc($d_result)){
		echo "<option value=\"" . $d_row['d_uid'] . "\">" . $d_row['distance']; 
		if($d_row['unit'] == 'm') echo " m";
		else echo " yrds";
		echo "</option>\n";
	}
	echo "</select></td>";
	
	// Build face type
	echo "<td><select name=\"face" . $count . "\">";
	// Add Blank row as default
	echo "<option value=\"\" selected>--Select Face--</option>\n";
	$query = "SELECT f_uid,f_name FROM `face` ORDER BY `f_name`";
	// Perform SQL Query
	$f_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	while($f_row = mysql_fetch_assoc($f_result)){
?>
		<option value="<?php echo $f_row["f_uid"]; ?>"><?php echo $f_row["f_name"]; ?></option>
<?php
	}
	
		echo "</select></td>";


	// Build ends box
	echo "<td><input name=\"ends" . $count . "\" value=\"\" /></td></tr>\n";
	
	$count++;
	
?>
	</table>
	<input type="hidden" name="count" value="<?php echo $count; ?>">
	<input type="submit" name="action" value="Add Distance"/>
	<input type="submit" name="action" value="Save"/>
	</form>
<?php
}

function confirm_round_page($db, $uid)
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
	$uid =$_POST["uid"];
	$name =$_POST["name"];
	$scoring =$_POST["scoring"];
	$count =$_POST["count"];

//	echo $uid;
//	echo $name;
//	echo $face;
//	echo $scoring;
//	echo $count;

	if($uid == "new"){
		
		$query = "SELECT `r_uid` FROM `round` ORDER BY `r_uid` DESC";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `round` SET ";
		$query_string = $query_string . "`r_uid` = '$uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `round` SET ";
		$end_string = " WHERE `r_uid` = '$uid' LIMIT 1";
		
	}
	
	// Check name not blank
	if($name == ""){
		printf("Name field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`name` = '$name' , ";
	}
	
	// Check scoring not blank
	if($scoring == ""){
		printf("Scoring field blank! (How did you do that!)<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`scoring` = '$scoring' ";
	}
	
	$valid = 0;
	//Loop for each distance...
	for($i=0; $i < $count; $i++){
		//Ensure at least one valid distance, ends pair
		if((trim($_POST['dist' . $i]) != '') && (trim($_POST['ends' . $i]) != '') && (trim($_POST['face' . $i]) != '')){
			$valid++;
		}
		
	} 
		
	if($valid == 0){
		printf("Require at least one valid \"distance\"/\"face\"/\"number of ends\" set<br />\n");
		$error = 1;
	}

	if($error == 0){

		//Insert round into round table	
		$query = $action_string . $query_string . $end_string;
		//printf("<H3>SQL Query</H3>\n<CODE>" . $query . "</CODE><BR>\n");
		mysql_query($query,$db) or die("Invalid query:" . mysql_error());
		
		//Remove old round entries from round_dists table
		$rm_query = "DELETE FROM `round_dists` WHERE `round` LIKE '$uid'";
		// Perform SQL Query
		//printf("<H3>SQL Query</H3>\n<CODE>" . $rm_query . "</CODE><BR>\n");
		mysql_query($rm_query) or die('Query failed: ' . mysql_error());
		
		//Add new round_dists entries
		for($i=0; $i < $count; $i++){
			if((trim($_POST['dist' . $i]) != '') && (trim($_POST['ends' . $i]) != '') && (trim($_POST['face' . $i]) != '')){
				$rd_string = "INSERT INTO `round_dists` SET `round` = \"$uid\" , `distance`=\"" . trim($_POST['dist' . $i]) . "\" , `ends` = \"" . trim($_POST['ends' . $i]) . "\" , `face` = \"" . trim($_POST['face' . $i]) . "\"";
				
				//printf("<H3>SQL Query</H3>\n<CODE>" . $rd_string . "</CODE><BR>\n");
				mysql_query($rd_string,$db) or die("Invalid query:" . mysql_error());
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
	<a href="index.php?page=rounds">View Rounds</a>
<?php
}

?> 
