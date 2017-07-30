<?PHP


function edit_doc($db, $u_uid)
{
	$uid = trim($_GET['uid']);
	$action = trim($_GET['action']);
	
	if(check_priv($u_uid, "edit_docs") < 1){
		no_priv();
		return(0);
	}
	
	if(($uid == '') && ($action != 'new')){
		?>
		<h2>Please provide a document</h2>
		<p>It is necessary to provide the UID of the document you wish to edit.</p>
		<?php
		return (0);
	}
	
	if($action == '' || $action == 'edit'){
		edit_doc_page($uid);
	}
	elseif($action == 'new'){
		edit_doc_page("new");
	}
	elseif($action == 'confirm'){
		confirm_doc_page($db, $uid);
	}
	else{
		no_action();
	}
}

function edit_doc_page($uid)
{
	//echo $uid;
	if($uid != "new"){
		$query = "SELECT * FROM `documentation` WHERE `d_uid` LIKE \"" . $uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_doc&amp;uid=<?php echo $uid ?>&amp;action=confirm">
	<h2>Edit Document</h2>
	<?php
	if($uid != "new"){
	?>
		<input type="hidden" name="d_uid" value="<?php echo $row["d_uid"]; ?>">
	<?php
	}
	else{
	?>
		<input type="hidden" name="d_uid" value="new">
	<?php
	}
	?>
	<table>
		<tr><td>Title</td><td><input name="title" size="50" value="<?php echo $row["title"]; ?>" /></td></tr>
		<tr><td>Description</td><td><input name="description" size="115" value="<?php echo $row["description"]; ?>" /></td></tr>
		<tr><td>Contents</td><td><textarea name="content" cols="100" rows="20"><?php echo $row["content"]; ?></textarea></td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_doc_page($db, $uid)
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
	$title = $_POST["title"];
	$description = $_POST["description"];
	$content = $_POST["content"];
	
	//echo $c_uid;
	//echo $c_name;
	//echo $c_desc;
	
	if($d_uid == "new"){
		
		//$query = "SELECT `n_uid` FROM `news` ORDER BY `n_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//$n_uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `documentation` SET ";
		//$query_string = $query_string . "`n_uid` = '$n_uid' , ";
	
	}
	else{
		
		$action_string = "UPDATE `documentation` SET ";
		$end_string = " WHERE `d_uid` = '$d_uid' LIMIT 1";
		
	}
	
	// Update Date
	$query_string = $query_string . "`date` = '" . date("Y-m-d") . "' , ";
	
	// Check name not blank
	if($title == ""){
		printf("Title blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`title` = '$title' , ";
	}
	
	$query_string = $query_string . "`description` = '" . $description . "' , ";
	$query_string = $query_string . "`content` = '" . $content . "'";
	
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
	<a href="index.php?page=docs">Documents</a>

<?php
}

?> 
