<?php

function edit_exec($db, $u_uid)
{
	$pos_uid = trim($_GET['uid']);
	$action = trim($_GET['action']);
	
	if(($pos_uid == '') && ($action != 'new')){
		?>
		<h2>Please provide an exec position</h2>
		<p>It is necessary to provide the ID of the exec position you wish to edit.</p>
		<?php
		return (0);
	}

	$query = "SELECT `user` FROM `exec` WHERE `pos_uid` LIKE \"" . $pos_uid . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$pos_row = mysql_fetch_assoc($result);
	
	if((check_priv($u_uid, "edit_users") < 1) && ($pos_row["user"] != $u_uid)){
		no_priv();
		return(0);
	}
	
	
	
	if($action == '' || $action == 'edit'){
		edit_exec_page($pos_uid, $u_uid);
	}
	elseif($action == 'new'){
		edit_exec_page("new", $u_uid);
	}
	elseif($action == 'confirm'){
		confirm_exec_page($db, $pos_uid);
	}
	else{
		no_action();
	}
}

function edit_exec_page($pos_uid, $u_uid)
{
	
	if($pos_uid != "new"){
		
		// Build Query
		$e_query = "SELECT * FROM `exec` WHERE `pos_uid` LIKE \"" . $pos_uid . "\"";
		// Perform SQL Query
		$e_result = mysql_query($e_query) or die('Query failed: ' . mysql_error());
		// Get Row
		$e_row = mysql_fetch_assoc($e_result);
	
	}
?>
	<form enctype="multipart/form-data" method="post" action="index.php?page=edit_exec&amp;uid=<?php echo $pos_uid ?>&amp;action=confirm">
	<h2>Edit Exec</h2>
	<?php
	if($pos_uid != "new"){
	?>
		<input type="hidden" name="pos_uid" value="<?php echo $e_row["pos_uid"]; ?>">
	<?php
	}
	else{
	?>
		<input type="hidden" name="pos_uid" value="new">
	<?php
	}
	?>
	
	<table cols="2">
<?php
	if(check_priv($u_uid, "edit_users") < 1){
?>
		<tr>
			<td>Position Name</td>
			<td><input type="hidden" name="position" value="<?php echo $e_row["position"]; ?>"><?php echo $e_row["position"]; ?></td>
		</tr>
		<tr>
			<td>User</td>
			<?php 
		
		$u_query = "SELECT `u_uid`,`u_name`,`f_name`,`s_name` FROM `user` WHERE `u_uid` = '" . $e_row["user"] . "'";
		// Perform SQL Query
		$u_result = mysql_query($u_query) or die('Query failed: ' . mysql_error());
		// Get Val
		$u_row = mysql_fetch_assoc($u_result);
?>
			<td><input type="hidden" name="user" value="<?php echo $u_row["u_uid"]; ?>"><?php echo $u_row["f_name"] . " " . $u_row["s_name"] . " [" . $u_row["u_name"] . "]"; ?></td>
		</tr>
<?php
	}
	else{
?>
		<tr>
			<td>Position Name</td>
			<td><input name="position" value="<?php echo $e_row["position"]; ?>" /></td>
		</tr>
		<tr>
			<td>User</td>
			<td><select name="user">
						<option value="">Position not filled</option>
			<?php 
		
		$u_query = "SELECT `u_uid`,`u_name`,`f_name`,`s_name` FROM `user` ORDER BY `f_name`";
		// Perform SQL Query
		$u_result = mysql_query($u_query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		while($u_row = mysql_fetch_assoc($u_result)){
			if($u_row["u_uid"] == $e_row["user"]){
		?>
			<option value="<?php echo $u_row["u_uid"]; ?>" selected ><?php echo $u_row["f_name"] . " " . $u_row["s_name"] . " [" . $u_row["u_name"] . "]"; ?></option>
		<?php
			}
			else{
		?>
			<option value="<?php echo $u_row["u_uid"]; ?>"><?php echo $u_row["f_name"] . " " . $u_row["s_name"] . " [" . $u_row["u_name"] . "]"; ?></option>
		<?php
			}
		}
			?></select></td>
		</tr>
<?php
	}
?>
		<tr>
			<td>Duties</td>
			<td><textarea name="duties" cols="100" rows="8"><?php echo $e_row["duties"]; ?></textarea></td>
		</tr>
		<tr>
			<td>Upload Picture</td>
			<td><input type="hidden" name="MAX_FILE_SIZE" value="1000000"> <input type="file" name="photo" accept="image/png"> <p><b>Please note:</b> This image is currently forced to display at a resolution of 120x170 pixels and has to be a <b>png</b>. Concider this carefully before uploading a picture, else you could look kinda silly or find it won't upload.</p></td>
		</tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_exec_page($db, $name)
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
	$pos_uid =$_POST["pos_uid"];
	$position =$_POST["position"];
	$user = $_POST["user"];
	$duties = $_POST["duties"];
	
	if($pos_uid == "new"){
		
		$action_string = "INSERT INTO `exec` SET ";
	
	}
	else{
		
		$action_string = "UPDATE `exec` SET ";
		$end_string = " WHERE `pos_uid` = '$pos_uid' LIMIT 1";
		
	}
	
	$query_string = $query_string . "`position` = '$position' , ";
	$query_string = $query_string . "`user` = '$user' , ";
	$query_string = $query_string . "`duties` = '$duties' ";
	
	//Deal with picture!!!!
	// Get name of client-side file name of picture
	$imagename = $_FILES['photo']['name'];
	//echo "Photo name: $imagename<br />\n";
	
	//echo "File Type: " . $_FILES['photo']['type'] ."<br />\n";
	//echo "File Size: " . $_FILES['photo']['size'] ."<br />\n";
	//echo "Tmp Name: " . $_FILES['photo']['tmp_name'] ."<br />\n";
	//echo "Error: " . $_FILES['photo']['error'] ."<br />\n";
	// If an image exists
	if ($imagename){
		$parts = explode(".", $imagename);
		$extension = end($parts); // part behind last dot
		if (strtolower($extension) != "png"){
			echo "Sorry, image must currently be a png\n";
			$error = 1;
		}
		if ($_FILES['photo']['size'] == 0){
			echo "Sorry, image upload has failed. Please try again.\n";
			$error = 1;
		}
		if ($_FILES['photo']['size'] > 1000000){
			echo "Sorry the image you have provided is too big.\n";
			$error = 1;
		}

		// Get Photo
		if(is_uploaded_file($_FILES['photo']['tmp_name'])){
			
			$filehandle = fopen($_FILES['photo']['tmp_name'], "r");
			$picture = fread($filehandle, fileSize($_FILES['photo']['tmp_name']));
			
/*			// Check image valid
			$image = imagecreatefromstring($picture); // try to create image
			
			// Unless image invalid
			if (!$image){
				echo "Sorry, the image doesn't seem to be a valid png.\n";
				$error = 1;
			}*/
			
		}
		else{
			echo "Can't find uploaded file!\n";
			$error = 1;
		}
		$safe_picture = addslashes($picture);
		$query_string = $query_string . ", `picture` = '$safe_picture' ";
	}
	
	if($error == 0){
	
		$query = $action_string . $query_string . $end_string;

		//echo "<H3>SQL Query</H3>\n<CODE>" . $query . "</CODE><BR>\n";
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
	<a href="index.php?page=exec">The Exec</a>

<?php
}

?> 
