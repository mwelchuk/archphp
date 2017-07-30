<?PHP


function edit_class($db, $u_uid)
{
	$name = trim($_GET['name']);
	$action = trim($_GET['action']);
	
	if(check_priv($u_uid, "edit_classes") < 1){
		no_priv();
		return(0);
	}
	
	if(($name == '') && ($action != 'new')){
		?>
		<h2>Please provide a class</h2>
		<p>It is necessary to provide the name of the class you wish to edit.</p>
		<?php
		return (0);
	}
	
	if($action == '' || $action == 'edit'){
		edit_class_page($name);
	}
	elseif($action == 'new'){
		edit_class_page("new");
	}
	elseif($action == 'confirm'){
		confirm_class_page($db, $name);
	}
	else{
		no_action();
	}
}

function edit_class_page($name)
{
	//echo $name;
	if($name != "new"){
		$query = "SELECT * FROM `class` WHERE `c_uid` LIKE \"" . $name . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_class&amp;name=<?php echo $name ?>&amp;action=confirm">
	<h2>Edit Class</h2>
	<?php
	if($name != "new"){
	?>
		<input type="hidden" name="c_uid" value="<?php echo $row["c_uid"]; ?>">
	<?php
	}
	else{
	?>
		<input type="hidden" name="c_uid" value="new">
	<?php
	}
	?>
	<table>
		<tr><td><h3>Details</h3></td></tr>
		<tr><td>Class Name</td><td><input name="c_name" value="<?php echo $row["c_name"]; ?>" /></td></tr>
		<tr><td>Description</td><td><textarea name="c_desc" cols="75" rows="4"><?php echo $row["c_desc"]; ?></textarea></td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_class_page($db, $name)
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
	$c_uid = $_POST["c_uid"];
	$c_name = $_POST["c_name"];
	$c_desc = $_POST["c_desc"];
	
	//echo $c_uid;
	//echo $c_name;
	//echo $c_desc;
	
	if($c_uid == "new"){
		
		//$query = "SELECT `c_uid` FROM `class` ORDER BY `c_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//$c_uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `class` SET ";
		//$query_string = $query_string . "`c_uid` = '$c_uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `class` SET ";
		$end_string = " WHERE `c_uid` = '$c_uid' LIMIT 1";
		
	}
	
	// Check name not blank
	if($c_name == ""){
		printf("Class name field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`c_name` = '$c_name' , ";
	}
	
	$query_string = $query_string . "`c_desc` = '$c_desc'";
	
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
	<a href="index.php?page=classes">Class List</a>

<?php
}

?> 
