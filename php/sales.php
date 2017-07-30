<?php

function view_sales($db, $u_uid)
{
//	if(check_priv($u_uid, "view_users") < 1){
//		no_priv();
//		return(0);
//	}
?> 
<h2>Equipment for sale or wanted[<a href="index.php?page=edit_sale&amp;action=new">Add</a>]</h2>
<h3>Items For sale</h3>
<?php
	$query = "SELECT * FROM `sales` WHERE `direction` = 'sale' ORDER BY `date`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());

	if(mysql_num_rows($result) == 0){
?>
	<p>There is currently no equipment for sale.</p>
<?php
	}
	else{
?>

<?php
		while($row = mysql_fetch_assoc($result)){
?>
<table border="0" cols="2" width="100%">
	<tr>
		<td class="dark" colspan="2"><h4><?php echo $row["title"]; ?><?php
			if((check_priv($u_uid, "edit_users") > 0) || ($row["seller"] == $u_uid)){
				printf(" [<a href=\"index.php?page=edit_sale&amp;uid=" . $row["sale_uid"] . "\">Edit</a>]");
				printf(" [<a href=\"index.php?page=edit_sale&amp;action=delete&amp;uid=" . $row["sale_uid"] . "\">Delete</a>]");
			}
		?></h4></td>
	</tr>
<?php
			$u_query = "SELECT `f_name`,`s_name` FROM `user` WHERE `u_uid`='" . $row["seller"] . "'";
			// Perform SQL Query
			$u_result = mysql_query($u_query) or die('Query failed: ' . mysql_error());
			// Get Val
			$u_row = mysql_fetch_assoc($u_result);
?>
	<tr>
		<td class="dark" colspan="2">Listed by <b><?php echo $u_row["f_name"] . " " . $u_row["s_name"]; ?></b> on <?php echo date('d/m/y', strtotime($row["date"])); ?> for <b><?php echo $row["price"]; ?></b></td>
	</tr>
	<tr>
		<td class="light" width="120"><img height="170" width="120" alt="[No Photos yet]" src="" /></td>
		<td class="light"><?php echo $row["description"]; ?></td>
	</tr>
</table>
<?php
		}
?>
<?php
	}

?>
<h3>Items Wanted</h3>
<?php
	$query = "SELECT * FROM `sales` WHERE `direction` = 'wanted' ORDER BY `date`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());

	if(mysql_num_rows($result) == 0){
?>
	<p>There is currently no equipment wanted.</p>
<?php
	}
	else{
?>

<?php
		while($row = mysql_fetch_assoc($result)){
?>
<table border="0" cols="2" width="100%">
	<tr>
		<td class="dark" colspan="2"><h4><?php echo $row["title"]; ?><?php
			if((check_priv($u_uid, "edit_users") > 0) || ($row["seller"] == $u_uid)){
				printf(" [<a href=\"index.php?page=edit_sale&amp;uid=" . $row["sale_uid"] . "\">Edit</a>]");
				printf(" [<a href=\"index.php?page=edit_sale&amp;action=delete&amp;uid=" . $row["sale_uid"] . "\">Delete</a>]");
			}
		?></h4></td>
	</tr>
<?php
			$u_query = "SELECT `f_name`,`s_name` FROM `user` WHERE `u_uid`='" . $row["seller"] . "'";
			// Perform SQL Query
			$u_result = mysql_query($u_query) or die('Query failed: ' . mysql_error());
			// Get Val
			$u_row = mysql_fetch_assoc($u_result);
?>
	<tr>
		<td class="dark" colspan="2">Listed by <b><?php echo $u_row["f_name"] . " " . $u_row["s_name"]; ?></b> on <?php echo date('d/m/y', strtotime($row["date"])); ?> for <b><?php echo $row["price"]; ?></b></td>
	</tr>
	<tr>
		<td class="light" width="120"><img height="170" width="120" alt="[No Photos yet]" src="" /></td>
		<td class="light"><?php echo $row["description"]; ?></td>
	</tr>
</table>
<?php
		}
?>
<?php
	}
}

function edit_sales($db, $u_uid)
{
	$sales_uid = trim($_GET['uid']);
	$action = trim($_GET['action']);
	
	if(($sales_uid == '') && ($action != 'new')){
		?>
		<h2>Please provide a sales item</h2>
		<p>It is necessary to provide the ID of the item you wish to edit.</p>
		<?php
		return (0);
	}

	if($sales_uid != 'new')
	{
		$query = "SELECT `seller` FROM `sales` WHERE `sale_uid` LIKE \"" . $sales_uid . "\"";
		// Perform SQL Query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		$sales_row = mysql_fetch_assoc($result);
		$seller = $sales_row["seller"];
	}
	else
	{
		$seller = $u_uid;
	}
	
	if((check_priv($u_uid, "edit_users") < 1) && ($seller != $u_uid) && ($action != 'new')){
		no_priv();
		return(0);
	}
	
	
	
	if($action == '' || $action == 'edit'){
		edit_sales_page($sales_uid, $u_uid);
	}
	elseif($action == 'new'){
		edit_sales_page("new", $u_uid);
	}
	elseif($action == 'confirm'){
		confirm_sales_page($db, $sales_uid);
	}
	elseif($action == 'delete')
	{
		if((check_priv($u_uid, "edit_users") < 1) && ($sales_row["seller"] != $u_uid))
		{
			no_priv();
		}
		else
		{
			delete_sales_page($sales_uid);
		}
	}
	elseif($action == 'really_delete')
	{
		if((check_priv($u_uid, "edit_users") < 1) && ($sales_row["seller"] != $u_uid))
		{
			no_priv();
		}
		else
		{
			really_delete_sales_page($sales_uid);
		}
	}
	else{
		no_action();
	}
}

function edit_sales_page($sale_uid, $u_uid)
{
	
	if($sale_uid != "new"){
		
		// Build Query
		$s_query = "SELECT * FROM `sales` WHERE `sale_uid` LIKE \"" . $sale_uid . "\"";
		// Perform SQL Query
		$s_result = mysql_query($s_query) or die('Query failed: ' . mysql_error());
		// Get Row
		$s_row = mysql_fetch_assoc($s_result);
	
	}
?>
	<form enctype="multipart/form-data" method="post" action="index.php?page=edit_sale&amp;uid=<?php echo $sale_uid ?>&amp;action=confirm">
	<h2>Edit item for sale or wanted</h2>
	<?php
	if($sale_uid != "new"){
	?>
		<input type="hidden" name="sale_uid" value="<?php echo $s_row["sale_uid"]; ?>">
		<input type="hidden" name="seller" value="<?php echo $s_row["seller"]; ?>">
		<input type="hidden" name="date" value="<?php echo $s_row["date"]; ?>">
	<?php
	}
	else{
	?>
		<input type="hidden" name="sale_uid" value="new">
		<input type="hidden" name="seller" value="<?php echo $u_uid; ?>">
		<input type="hidden" name="date" value="">
	<?php
	}
	?>
	
	<table cols="2">
		<tr>
			<td>Item</td>
			<td><select name="direction">
					<option value="sale" <?php if($s_row["direction"] == "sale") echo "selected"; ?> >For Sale</option>
					<option value="wanted" <?php if($s_row["direction"] == "wanted") echo "selected"; ?> >Wanted</option>
		</select></td></tr>

		</tr>
		<tr>
			<td>Title</td>
			<td><input name="title" value="<?php echo $s_row["title"]; ?>" /></td>
		</tr>
		<tr>
			<td>Price</td>
			<td><input name="price" value="<?php echo $s_row["price"]; ?>" /> <b>N.B.</b> This doesn't have to be a numberic value (for example it could be "to a good home"), though if it is, it might be worth including the currency. I'd suggest £'s or pints.</b></td>
		</tr>
		<tr>
			<td>Description</td>
			<td><textarea name="description" cols="100" rows="8"><?php echo $s_row["description"]; ?></textarea></td>
		</tr>
		<tr>
			<td>Upload Picture</td>
			<td>Image upload currently disabled</td>
<?php
/*
			<td><input type="hidden" name="MAX_FILE_SIZE" value="1000000"> <input type="file" name="photo" accept="image/png"> <p><b>Please note:</b> This image is currently forced to display at a resolution of 120x170 pixels and has to be a <b>png</b>. Concider this carefully before uploading a picture, else you could look kinda silly or find it won't upload.</p></td>
*/
?>
		</tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_sales_page($db, $name)
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
	$sale_uid = trim($_POST["sale_uid"]);
	$seller = trim($_POST["seller"]);
	$date = trim($_POST["date"]);
	$title = trim($_POST["title"]);
	$price = trim($_POST["price"]);
	$description = trim($_POST["description"]);
	$direction = trim($_POST["direction"]);

	
	if($sale_uid == "new"){
		
		$action_string = "INSERT INTO `sales` SET ";
		$date = date ("Y-m-d");
	
	}
	else{
		
		$action_string = "UPDATE `sales` SET ";
		$end_string = " WHERE `sale_uid` = '$sale_uid' LIMIT 1";
	}
	

	$query_string = $query_string . "`seller` = '$seller'";

	if($title == '')
	{
		printf("Title must be given\n");
		$error = 1;
	}
	else
	{
		$query_string = $query_string . " , `title` = '$title'";
	}

	if($price == '')
	{
		printf("Price must be given\n");
		$error = 1;
	}
	else
	{
		$query_string = $query_string . " , `price` = '$price'";
	}

	if($description == '')
	{
		printf("Description must be given\n");
		$error = 1;
	}
	else
	{
		$query_string = $query_string . " , `description` = '$description'";
	}

	$query_string = $query_string . " , `date` = '$date'";
	$query_string = $query_string . " , `direction` = '$direction'";
/*
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
			
			// Check image valid
			$image = imagecreatefromstring($picture); // try to create image
			
//			// Unless image invalid
//			if (!$image){
//				echo "Sorry, the image doesn't seem to be a valid png.\n";
//				$error = 1;
//			}
			
		}
		else{
			echo "Can't find uploaded file!\n";
			$error = 1;
		}
		$safe_picture = addslashes($picture);
		$query_string = $query_string . ", `picture` = '$safe_picture' ";
	}
*/
	
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
	<a href="index.php?page=view_sales">Current Items for Sale or Wanted</a>

<?php
}

function delete_sales_page($uid){
?>
	<h2>Delete item for sale</h2>
	<p>Are you sure you wish to do this?</p>
	<table><tr>
	<td>
		<form method="post" action="index.php?page=edit_sale&amp;action=really_delete&amp;uid=<?php echo $uid ?>">
			<input type="hidden" name="uid" value="<?php echo $row["name"]; ?>">
			<input type="submit" value="Yes" />
		</form>
	</td>
	<td>
		<form method="get" action="index.php">
			<input type="hidden" name="page" value="main" />
			<input type="submit" value="No" />
		</form>
	</td>
	</tr></table>
<?php
}

function really_delete_sales_page($uid){
	
	//Delete all references from Equipment Table
	$query = "DELETE FROM `sales` WHERE `sale_uid` LIKE '$uid'";
	// Perform SQL Query
	mysql_query($query) or die('Query failed: ' . mysql_error());
?>
	<h2>Item for sale deleted</h2>
	<a href="index.php?page=view_sales">Current Items for Sale or Wanted</a>
<?php
}

?>


