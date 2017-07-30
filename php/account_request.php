<?php

/*
 * For now, allow privileged users to request account creation.
 *
 * A request sends an email to the email address specified, containing 
 * a url with a long unique key. This allows the receiver to access an
 * account creation page.
*/

function account_request($db, $u_uid)
{

	$action = trim($_GET['action']);

	//See if user allowed to edit other users
	$edit_user = check_priv($u_uid, "edit_users");
	
	//See if user allowed to edit privileges
	$edit_priv = check_priv($u_uid, "edit_privileges");
	
	if($edit_user < 1)
	{
		no_priv();
		return(0);
	}
	
	if($action == '' || $action == 'add')
	{
		account_request_page($u_uid, $edit_priv);
	}
	elseif($action == 'confirm')
	{
		confirm_account_request_page($db, $u_uid, $edit_priv);
	}
	else
	{
		no_action();
	}

}

function account_request_page($u_uid, $edit_priv)
{

?>
	<form method="post" action="index.php?page=account_request&amp;action=confirm">
	<h2>Account Request</h2>
	<p>Add the email address of the new user below. An email will automatically be sent to the user containing a time limited offer to to sign up to the ArchPHP website (alternatively add user <a href="index.php?page=edit_user&amp;action=new">manually</a>):</p>
	<table>
		<tr><td>Email Address</td><td><input name="email" value="" /></td></tr>
<?php
	if($edit_priv == 1)
	{
?>
		<tr><td><h3>Privileges</h3></td></tr>
<?php
		$default_privilege_level = 1;
		$query = "SELECT * FROM `priv_level` ORDER BY `p_uid`";
		// Perform SQL Query
		$priv_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		while($priv_row = mysql_fetch_array($priv_result, MYSQL_NUM))
		{
			if((($priv_row[0]+1)-1)&(($default_privilege_level+1)-1))
			{
				printf("<tr><td><input type=\"checkbox\" name=\"p_". $priv_row[1]. "\" value=\"1\" checked>". $priv_row[1] . "</td></tr>");
			}
			else
			{
				printf("<tr><td><input type=\"checkbox\" name=\"p_". $priv_row[1]. "\" value=\"1\">". $priv_row[1] . "</td></tr>");
			}
		}
	}
?>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php
}

function confirm_account_request_page($db, $u_uid, $edit_priv)
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
	$email = trim($_POST["email"]);
	
	// Display Possible Values from POST
	// printf("email = \"" . $_POST["email"] . "\"<br />\n");
	
	// Do basic email check
	if (check_email_address($email) < 1)
	{
		printf("Invalid Email Address<br />\n");
		$error = 1;
	}
	else
	{
		$query_string = "`email` = '$email'";
	}

	// See if current user can edit privileges and work out privilege level if necessary
	if($edit_priv > 0){
		
		$query = "SELECT * FROM `priv_level` ORDER BY `p_uid`";
		// Perform SQL Query
		$priv_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		while($priv_row = mysql_fetch_array($priv_result, MYSQL_NUM)){
			$privilege_level += $_POST["p_$priv_row[1]"] * $priv_row[0];
			//printf($priv_row[1]. " = \"" . $_POST["p_" . $priv_row[1]] * $priv_row[0] . "\"<br />\n");
		}
// 		printf("privilege_level = \"" . $privilege_level . "\"<br />\n");
		$query_string = $query_string . " , `priv` = '$privilege_level'";
	}
	//If new user and now privileges to edit privileges, set to default
	else
	{
// 		printf("privilege_level = \"1\"<br />\n");
		$query_string = $query_string . " , `priv` = '1'";
	}

	// Get date and time
	$time_now = time();

	$time_expire = $time_now + (30 * 24 * 60 * 60);
// 7 days; 24 hours; 60 mins; 60secs
	$date = date ("Y-m-d H:m:s", $time_expire);
	$query_string = $query_string . " , `expire` = '$date'";
	
	$times = 0;
	// Create URL safe hash and check not already used
	do
	{
		$random = rand(0000, 9999);
		$hash_string = $time_now . $random;
	
		$hashed_string = md5($hash_string);
	
		$hash_query = "SELECT `hash` FROM `requests` WHERE `hash` = '$hashed_string'";
		// Perform SQL Query
		$hash_result = mysql_query($hash_query) or die('Query failed: ' . mysql_error());

		$times = $times + 1;
	}
	while((mysql_num_rows($hash_result) > 0) ||$times > 100);

	$query_string = $query_string . " , `hash` = '$hashed_string'";

	//New User - Need insert action_string
	$action_string = "INSERT INTO `requests` SET ";
	//New User - Need created date

	if($error == 0){
		
		$email_subject = 'Subscription to ArchPHP website';
		$email_body = "Hi!\r\n\r\nSomeone has applied for an account on the ArchPHP website using this email address.\r\n\r\nIf you would like an account on this website enter the following address into your web browser:\r\n\r\nhttp://www.luac.org.uk/index.php?page=new_account&amp;action=start&amp;hash=$hashed_string\r\n\r\nYou have 30 days to apply, after which time this link will be invalid and a new application will need to be made.\r\n\r\nThe Exec\r\n";

		$mail_check = mail( $email, $email_subject, $email_body, 'From: luac@luac.co.uk');
		//printf("Mail Sent:" . $mail_check . "\n");

		$query = $action_string . $query_string . $end_string;

		//printf("<H3>SQL Query</H3>\n<CODE>" . $query . "</CODE><BR>\n");
		//printf("Times:" . $times . "\n");
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
	<a href="index.php?page=users">User List</a>

<?php

}

function add_account($db, $action)
{

	// Get date
	$date = date ("Y-m-d H:m:s");
	// Check all invalid rows have been removed
	$old_query = "DELETE FROM `requests` WHERE `expire` < '$date'";
	// Perform SQL Query
	$old_result = mysql_query($old_query) or die('Query failed: ' . mysql_error());

	$hash = trim($_GET["hash"]);

	//printf("<p>Hash:$hash</p>");

	$hash_query = "SELECT * FROM `requests` WHERE `hash` = '$hash'";
	// Perform SQL Query
	$hash_result = mysql_query($hash_query) or die('Query failed: ' . mysql_error());

	if(mysql_num_rows($hash_result) != 1)
	{
?>
	<h2>Error!</h2>
	<p>The link you have provided is not valid. Please ensure that the correct link has been used. If the email including the link is more than 30 days old you will need to apply again!</p>
<?php	
	}
	elseif($action == "start") add_account_initial($db, $hash);
	elseif($action == "confirm") add_account_confirm($db, $hash);
}

function add_account_initial($db, $hash)
{

	$hash_query = "SELECT `email` FROM `requests` WHERE `hash` = '$hash'";
	// Perform SQL Query
	$hash_result = mysql_query($hash_query) or die('Query failed: ' . mysql_error());

	$row = mysql_fetch_assoc($hash_result);

?>
	<h2>Please complete the following form</h2>
	<p>Required values are labeled as such:</p>
	<form method="post" action="index.php?page=new_account&amp;action=confirm&amp;hash=<?php echo $hash; ?>">
	<input type="hidden" name="u_uid" value="new" />
	<input type="hidden" name="email" value="<?php echo $row["email"]; ?>" />
	<table>
	<tr><td colspan="2"><h3>Basic Details</h3></td></tr>
	<tr><td>First Name</td><td><input name="f_name" value="" /> Required</td></tr>
	<tr><td>Surname Name</td><td><input name="s_name" value="" /> Required</td></tr>
	<tr><td>Description</td><td><textarea name="description" cols="80" rows="5"></textarea></td></tr>
	<tr><td colspan="2"><h3>Account Details</h3></td></tr>
	<tr><td>Username Name</td><td><input name="u_name" value="" /> Required</td></tr>
	<tr><td>New Password</td><td><input type="password" name="password1" value="" /> Required</td></tr>
	<tr><td>New Password</td><td><input type="password" name="password2" value="" /> Required</td></tr>
	</table>
	<input type="submit" value="Save"/>
	</form>
<?php

}

function add_account_confirm($db, $hash)
{

	$hash_query = "SELECT * FROM `requests` WHERE `hash` = '$hash'";
	// Perform SQL Query
	$hash_result = mysql_query($hash_query) or die('Query failed: ' . mysql_error());

	$request_row = mysql_fetch_assoc($hash_result);

	// This is changed to 1 on error
	$error = 0;
	// This string for different string between new and edit
	$action_string = "";
	// String for fields and values to edit
	$query_string = "";
	// String for end of query
	$end_string = "";
	
	// Import Possible Values from POST
	$u_uid = $_POST["u_uid"];
	$u_name = $_POST["u_name"];
	$f_name = $_POST["f_name"];
	$s_name = $_POST["s_name"];
	$email = $_POST["email"];
	$description = $_POST["description"];
	$password1 = $_POST["password1"];
	$password2 = $_POST["password2"];
	
	
	// Display Possible Values from POST
	/*
	printf("u_uid = \"" . $_POST["u_uid"] . "\"<br />\n");
	printf("u_name = \"" . $_POST["u_name"] . "\"<br />\n");
	printf("f_name = \"" . $_POST["f_name"] . "\"<br />\n");
	printf("s_name = \"" . $_POST["s_name"] . "\"<br />\n");
	printf("description = \"" . $_POST["description"] . "\"<br />\n");
	printf("old_password = \"" . $_POST["old_password"] . "\"<br />\n");
	printf("password1 = \"" . $_POST["password1"] . "\"<br />\n");
	printf("password2 = \"" . $_POST["password2"] . "\"<br />\n");
	*/
	
	
	//$query_string = $query_string . "`u_uid` = '$u_uid' , ";
	//New User - Need insert action_string
	$action_string = "INSERT INTO `user` SET ";
	//New User - Need created date
	$query_string = $query_string . "`created` = '" . date ("Y-m-d") . "' , ";

	$email_subject = 'Your ArchPHP account';
	$email_body = "Hi " . $f_name . ",\r\n\r\nYou account on www.luac.org.uk has now been created:\r\n\r\nusername: " . $u_name . "\r\npassword: " . $password1 . "\r\n\r\nThe Exec\r\n";

	
	// Check username not blank and not same as existing...
	$query = "SELECT `u_uid` FROM `user` WHERE `u_name` LIKE \"" . $u_name . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	//get result
	$row = mysql_fetch_array($result, MYSQL_NUM);
	if($u_name == ""){
		printf("Username field blank!<br />\n");
		$error = 1;
	} 
	elseif(($row[0] != "") && ($row[0] != $u_uid)){
		printf("Error: Name already exists<br />\n");
		$error = 1;
	}
	else{
		$query_string = $query_string . "`u_name` = '$u_name' , ";
	}
	
	// Check Important Fields not blank
	if(($f_name == "") || ($s_name == "") || ($email == "")){
		printf("Required field blank!<br />\n");
		$error = 1;
		
	}else{
		$query_string = $query_string . "`f_name` = '$f_name' , `s_name` = '$s_name' , `email` = '$email'";
	}
	
	
	$query_string = $query_string . " , `privilege_level` = '" . $request_row["priv"] . "'";
	
	
	$query_string = $query_string . " , `description` = '$description'";
	
	
	// Password required if new
	if($password1 == ""){
		printf("Password Required!<br />\n");
		$error = 1;

	}
	
	if($password1 != $password2){
		//Mis-matched Passwords
		printf("New Passwords not the same!<br />\n");
		$error = 1;
	}
	else{
		//Passwords OK!
		//printf("Passwords OK<br />\n");
		$query_string = $query_string . " , `password` = '" . md5($password1) . "'";
	}

	// Build nice to email address
	$email_to = $f_name . ' ' . $s_name .' <' . $email . '>';

	if($error == 0){
		
		// Send email notification if required
		if(($password1 != "") || ($new == 1))
		$mail_check = mail( $email_to, $email_subject, $email_body, 'From: luac@luac.org.uk');
// 		if($mail_check == 0){
// 			printf("Mail not sent :-(<br />\n");
// 			$error = 1;
// 		}

		$query = $action_string . $query_string . $end_string;

		//printf("<H3>SQL Query</H3>\n<CODE>" . $query . "</CODE><BR>\n");
		mysql_query($query,$db) or die("Invalid query:" . mysql_error());

		//Remove request
		$old_query = "DELETE FROM `requests` WHERE `hash` = '$hash'";
		// Perform SQL Query
		$old_result = mysql_query($old_query) or die('Query failed: ' . mysql_error());


?>
	<h2>You have been successful!</h2>

	<p>Go to the <a href="index.php?page=login">Login Page</a> to access the archery club website</p>

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
}

?>
