<?PHP


function edit_distance($db, $u_uid)
{
	$uid = trim($_GET['uid']);
	$action = trim($_GET['action']);
	
	if(check_priv($u_uid, "edit_rounds") < 1){
		no_priv();
		return(0);
	}
	
	if(($uid == '') && ($action != 'new')){
		?>
		<h2>Please provide a distance</h2>
		<p>It is necessary to provide the name of the class you wish to edit.</p>
		<?php
		return (0);
	}
	
	if($action == '' || $action == 'edit'){
		edit_distance_page($uid);
	}
	elseif($action == 'new'){
		edit_distance_page("new");
	}
	elseif($action == 'confirm'){
		confirm_distance_page($db, $uid);
	}
	else{
		no_action();
	}
}

function edit_distance_page($uid)
{
	//echo $name;
	if($name != "new"){
		$query = "SELECT * FROM `distance` WHERE `d_uid` LIKE \"" . $uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_dist&amp;uid=<?php echo $uid; ?>&amp;action=confirm">
	<h2>Edit Distance</h2>
	<?php
	if($name == "new"){
	?>
		<input type="hidden" name="d_uid" value="new">
	<?php
	}
	else{
	?>
		<input type="hidden" name="d_uid" value="<?php echo $uid; ?>">
	<?php
	}
	?>
	<table>
		<tr><td><h3>Details</h3></td></tr>
		<tr><td>Distance</td><td><input name="distance" value="<?php echo $row["distance"]; ?>" /></td></tr>
		<tr><td>Metric/Imperial</td><td><select name="unit">
			<option value="m"<?php if($row['unit'] == 'm') echo "selected"; ?>>Metric (m)</option>
			<option value="i"<?php if($row['unit'] == 'i') echo "selected"; ?>>Imperial (yrds)</option>
		</select></td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_distance_page($db, $name)
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
	$d_uid = $_POST["d_uid"];
	$distance = $_POST["distance"];
	$unit = $_POST["unit"];
	
	//echo $d_uid;
	//echo $distance;
	//echo $unit;
	
	if($d_uid == "new"){
		
		//$query = "SELECT `d_uid` FROM `distance` ORDER BY `d_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//$d_uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `distance` SET ";
		//$query_string = $query_string . "`d_uid` = '$d_uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `distance` SET ";
		$end_string = " WHERE `d_uid` = '$d_uid' LIMIT 1";
		
	}
	
	// Check distance not blank
	if($distance == ""){
		printf("Distance field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`distance` = '$distance' , ";
	}
	// Check unit not blank
	if($unit == ""){
		printf("Unit field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`unit` = '$unit'";
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
	<a href="index.php">Home</a>

<?php
}

?> 
