<?PHP


function edit_face($db, $u_uid)
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
		edit_face_page($uid);
	}
	elseif($action == 'new'){
		edit_face_page("new");
	}
	elseif($action == 'confirm'){
		confirm_face_page($db, $uid);
	}
	else{
		no_action();
	}
}

function edit_face_page($uid)
{
	//echo $name;
	if($name != "new"){
		$query = "SELECT * FROM `face` WHERE `f_uid` LIKE \"" . $uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_face&amp;uid=<?php echo $uid; ?>&amp;action=confirm">
	<h2>Edit Face Type</h2>
	<?php
	if($name == "new"){
	?>
		<input type="hidden" name="f_uid" value="new">
	<?php
	}
	else{
	?>
		<input type="hidden" name="f_uid" value="<?php echo $uid; ?>">
	<?php
	}
	?>
	<table>
		<tr><td><h3>Details</h3></td></tr>
		<tr><td>Name</td><td><input name="f_name" value="<?php echo $row["f_name"]; ?>" /></td></tr>
		<tr><td>Size (cm)</td><td><input name="size" value="<?php echo $row["size"]; ?>" /></td></tr>
		<tr><td>Number</td><td><input name="number" value="<?php echo $row["number"]; ?>" /></td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_face_page($db, $name)
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
	$f_uid = $_POST["f_uid"];
	$f_name = $_POST["f_name"];
	$size = $_POST["size"];
	$number = $_POST["number"];
	
	//echo $f_uid;
	//echo $f_name;
	//echo $size;
	//echo $number;
	
	if($f_uid == "new"){
		
		//$query = "SELECT `f_uid` FROM `face` ORDER BY `f_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//$f_uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `face` SET ";
		//$query_string = $query_string . "`f_uid` = '$f_uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `face` SET ";
		$end_string = " WHERE `f_uid` = '$f_uid' LIMIT 1";
		
	}
	
	// Check name not blank
	if($f_name == ""){
		printf("Name field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`f_name` = '$f_name' , ";
	}
	
	// Check size not blank
	if($size == ""){
		printf("Size field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`size` = '$size' , ";
	}
	
	// Check number not blank
	if($number == ""){
		printf("Number field blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`number` = '$number'";
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
	<a href="index.php?page=faces">Faces</a>

<?php
}

?> 
