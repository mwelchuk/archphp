<?PHP


function edit_news($db, $u_uid)
{
	$name = trim($_GET['name']);
	$action = trim($_GET['action']);
	
	if(check_priv($u_uid, "edit_news") < 1){
		no_priv();
		return(0);
	}
	
	if(($name == '') && ($action != 'new')){
		?>
		<h2>Please provide a news item</h2>
		<p>It is necessary to provide the UID of the news idem you wish to edit.</p>
		<?php
		return (0);
	}
	
	if($action == '' || $action == 'edit'){
		edit_news_page($name);
	}
	elseif($action == 'new'){
		edit_news_page("new");
	}
	elseif($action == 'confirm'){
		confirm_news_page($db, $name);
	}
	else{
		no_action();
	}
}

function edit_news_page($name)
{
	//echo $name;
	if($name != "new"){
		$query = "SELECT * FROM `news` WHERE `n_uid` LIKE \"" . $name . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$row = mysql_fetch_assoc($result);
	}
?>
	<form method="post" action="index.php?page=edit_news&amp;name=<?php echo $name ?>&amp;action=confirm">
	<h2>Edit News Item</h2>
	<?php
	if($name != "new"){
	?>
		<input type="hidden" name="n_uid" value="<?php echo $row["n_uid"]; ?>">
	<?php
	}
	else{
	?>
		<input type="hidden" name="n_uid" value="new">
	<?php
	}
	?>
	<table>
		<tr><td><h3>Details</h3></td></tr>
		<tr><td>Title</td><td><input name="title" size="95" value="<?php echo $row["title"]; ?>" /></td></tr>
		<tr><td>Contents</td><td><textarea name="content" cols="72" rows="20"><?php echo $row["content"]; ?></textarea></td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_news_page($db, $name)
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
	$n_uid = $_POST["n_uid"];
	$title = $_POST["title"];
	$content = $_POST["content"];
	
	//echo $c_uid;
	//echo $c_name;
	//echo $c_desc;
	
	if($n_uid == "new"){
		
		//$query = "SELECT `n_uid` FROM `news` ORDER BY `n_uid` DESC";
		// Perform SQL Query
		//$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$row = mysql_fetch_array($result, MYSQL_NUM);
		//$n_uid = $row[0] + 1;
		
		$action_string = "INSERT INTO `news` SET ";
		//$query_string = $query_string . "`n_uid` = '$n_uid' , ";
		$query_string = $query_string . "`date` = '" . date("Y-m-d") . "' , ";
	
	}
	else{
		
		$action_string = "UPDATE `news` SET ";
		$end_string = " WHERE `n_uid` = '$n_uid' LIMIT 1";
		
	}
	
	// Check name not blank
	if($title == ""){
		printf("Title blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`title` = '$title' , ";
	}
	
	// Check contents not blank
	if($content == ""){
		printf("Contents blank!<br />\n");
		$error = 1;
	} 
	else{
		$query_string = $query_string . "`content` = '" . $content . "'";
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
	<a href="index.php?page=news">Manage News</a>

<?php
}

?> 
