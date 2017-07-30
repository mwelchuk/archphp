<?PHP


function edit_scoring($db, $u_uid)
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
		<p>It is necessary to provide the name of the distance you wish to edit.</p>
		<?php
		return (0);
	}
	
	if($action == '' || $action == 'edit'){
		edit_scoring_page($uid);
	}
	elseif($action == 'new'){
		edit_scoring_page("new");
	}
	elseif($action == 'confirm'){
		confirm_scoring_page($db, $uid);
	}
	else{
		no_action();
	}
}

function edit_scoring_page($uid)
{
	//echo $name;
	if($name != "new"){
		$query = "SELECT * FROM `scoring` WHERE `ss_uid` LIKE \"" . $uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_scoring&amp;uid=<?php echo $uid; ?>&amp;action=confirm">
	<h2>Edit Scoring System</h2>
	<?php
	if($name == "new"){
	?>
		<input type="hidden" name="ss_uid" value="new">
	<?php
	}
	else{
	?>
		<input type="hidden" name="ss_uid" value="<?php echo $uid; ?>">
	<?php
	}
	?>
	<table>
		<tr><td><h3>Details</h3></td></tr>
		<tr><td>Name</td><td><input name="name" value="<?php echo $row["name"]; ?>" /></td></tr>
		<tr><td>Acceptable Values (comma separated list)</td><td><input name="values" value="<?php echo $row["values"]; ?>" /></td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_scoring_page($db, $name)
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
	$ss_uid = $_POST["ss_uid"];
	$name = $_POST["name"];
	$values = $_POST["values"];
	
	//echo $ss_uid;
	//echo $name;
	//echo $values;
	
	if($ss_uid == "new"){
		
		//$query = "SELECT `ss_uid` FROM `scoring` ORDER BY `ss_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//$ss_uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `scoring` SET ";
		//$query_string = $query_string . "`ss_uid` = '$ss_uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `scoring` SET ";
		$end_string = " WHERE `ss_uid` = '$ss_uid' LIMIT 1";
		
	}
	
	// Check name not blank
	if($name == ""){
		printf("Name field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`name` = '$name' , ";
	}
	
	// Check values not blank
	if($values == ""){
		printf("Value field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`values` = '$values'";
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
	<a href="index.php?page=scoring">View Scoring Systems</a>

<?php
}

?> 
