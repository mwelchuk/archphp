<?PHP
// Stop warnings from appearing!
//error_reporting(0);

// Start PHP session (http://www.phpfreaks.com/tutorials/41/0.php)
session_start();
header("Cache-control: private"); // IE 6 Fix.
header("Content-type: text/html; charset=utf-8");


// Get required files
require 'config.php';
require 'common.php';
require 'front.php';
require 'login.php';
require 'main.php';
require 'logout.php';
require 'errors.php';
require 'manage_users.php';
require 'view_user.php';
require 'edit_user.php';
require 'manage_classes.php';
require 'edit_class.php';
require 'view_equipment.php';
require 'edit_equipment.php';
require 'edit_distance.php';
require 'manage_distance.php';
require 'edit_face.php';
require 'manage_face.php';
require 'edit_scoring.php';
require 'manage_scoring.php';
require 'manage_rounds.php';
require 'view_round.php';
require 'edit_round.php';
require 'manage_shoots.php';
require 'edit_shoot.php';
require 'manage_sessions.php';
require 'edit_session.php';
require 'view_session.php';
require 'manage_news.php';
require 'edit_news.php';
require 'manage_exec.php';
require 'edit_exec.php';
require 'manage_docs.php';
require 'edit_doc.php';
require 'view_doc.php';
require 'account_request.php';
require 'manage_requests.php';
require 'sales.php';

// Retrieve page wanted
$page = trim($_GET['page']);
// Retrieve any action string
$action = trim($_GET['action']);

//Set valid login attempt initally to fail
$valid = 0;

//Try to login to database, check to see if a valid database exists
if($db = mysql_connect($server, $db_username, $db_password))
{
	//See if actual database exists
	if(mysql_select_db($database,$db))
	{
		$valid = 1;
	}
}

// If actioned to login, get password and username from POST and get u_uid, otherwise retrieve from session.
if($action == "login")
{
	$username = trim($_POST["username"]);
	$password = $_POST["password"];
	
	$query = "SELECT `u_uid` FROM `user` WHERE `u_name` LIKE \"" . $username . "\"";
	// Make Query
	$result = mysql_query($query, $db) or die("Invalid query: " . mysql_error());
	// Count Returns
	$numrows=mysql_num_rows($result);

	// If we have more or less than 1 result, something wrong - don't login
	if ($numrows == 1)
	{
		$valid = 2;
		$row = mysql_fetch_assoc($result);
		$u_uid = $row['u_uid'];
	}
}
else
{
	$u_uid = $_SESSION["u_uid"];
	$password = $_SESSION["password"];
	
	if((trim($u_uid) != '') && (trim($password) != ''))
	{
		$valid = 2;
	}
}

// Try and login if DB exists and username and password seem OK
if($valid == 2)
{
	//md5 password
	$crypt_pass = md5($password);
	
	// Build SQL Query
	$query = "SELECT * FROM `user` WHERE `u_uid` LIKE \"" . $u_uid . "\" AND `password` LIKE \"" . $crypt_pass . "\"";
	// Make Query
	$result = mysql_query($query, $db) or die("Invalid query: " . mysql_error());
	// Count Returns
	$numrows=mysql_num_rows($result);

	// If we have more or less than 1 result, something wrong - don't login
	if ($numrows == 1)
	{
		$valid = 3;
		
		if(trim($_GET['action']) == "login")
		{
			//logged in - update login date
			$query_string = "UPDATE `user` SET `last_log` = '" . date ("Y-m-d") . "' WHERE `u_uid` = '$u_uid' LIMIT 1";
			mysql_query($query_string,$db) or die("Invalid query:" . mysql_error());
		}
	}
	
}

// If database valid, save username and password to session
if($valid == 3)
{
	$_SESSION["u_uid"] = $u_uid;
	$_SESSION["password"] = $password;
}

// Start building page
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>ArchPHP</title>
	<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?PHP
//	printf("Crypt_pass:". $crypt_pass);
?>

<div class="header">
<?PHP
// Build different title if logged in
if(($valid == 3) && ($page != 'logout'))
{
?>
<table width="100%" border="0"><tr>
	<td align="center"><a href="index.php">Home</a></td>
	<td align="center"><a href="index.php?page=main">Club Database</a></td>
	<td align="center"><a href="index.php?page=view_user">My Profile</a></td>
	<td align="center"><a href="index.php?page=exec">The Exec</a></td>
	<td align="center"><a href="index.php?page=docs">Club Documentation</a></td>
	<td align="center"><a href="index.php?page=logout">Log out</a></td>
</tr></table>
<?PHP
}
else
{
?>
<table width="100%" border="0"><tr>
	<td align="center"><a href="index.php">Home</a></td>
	<td align="center"><a href="index.php?page=exec">The Exec</a></td>
	<td align="center"><a href="index.php?page=docs">Club Documentation</a></td>
	<td align="center"><a href="index.php?page=login">Log in</a></td>
</tr></table>
<?PHP
}
?>
	
</div>
<div class="main">
<?PHP
// Check that the mysql server is available and database can be accessed
if($valid == 0)
{
	// Print page if no mysql server with which to connect
	no_connect();
	session_destroy();
}
else{

	// At this point pages that don't require logging in can be displayed...
	if($page == "") front_page($db);
	elseif($page == "login") login_page();
	elseif(($page == "exec") && ($valid != 3)) manage_exec($db, 'none');
	elseif(($page == "docs") && ($valid != 3)) manage_docs($db, 'none');
	elseif($page == 'view_doc') view_doc($db);
	elseif(($page == "new_account") && (($action == "start") || ($action == "confirm"))) add_account($db, $action);
	else
	{
		
		// Further pages require valid username and password
		if(($valid == 1)||($valid == 2))
		{
			// Print page if password invalid
			invalid_login();
			session_destroy();
		}
		elseif($valid == 3)
		{
			// If successful, allow pages to be displayed
			if($page == 'main') main_page($db, $u_uid);
			elseif($page == 'logout') logout_page();
			elseif($page == 'users') manage_users($db, $u_uid);
			elseif($page == 'view_user') view_user($db, $u_uid);
			elseif($page == 'edit_user') edit_user($db, $u_uid);
			elseif($page == 'classes') manage_classes($db, $u_uid);
			elseif($page == 'edit_class') edit_class($db, $u_uid);
			elseif($page == 'view_equip') view_equipment($db, $u_uid);
			elseif($page == 'edit_equip') edit_equipment($db, $u_uid);
			elseif($page == 'distances') manage_distance($db, $u_uid);
			elseif($page == 'edit_dist') edit_distance($db, $u_uid);
			elseif($page == 'faces') manage_face($db, $u_uid);
			elseif($page == 'edit_face') edit_face($db, $u_uid);
			elseif($page == 'scoring') manage_scoring($db, $u_uid);
			elseif($page == 'edit_scoring') edit_scoring($db, $u_uid);
			elseif($page == 'rounds') manage_rounds($db, $u_uid);
			elseif($page == 'view_round') view_round($db, $u_uid);
			elseif($page == 'edit_round') edit_round($db, $u_uid);
			elseif($page == 'shoots') manage_shoots($db, $u_uid);
			elseif($page == 'edit_shoot') edit_shoot($db, $u_uid);
			elseif($page == 'sessions') manage_sessions($db, $u_uid);
			elseif($page == 'edit_session') edit_session($db, $u_uid);
			elseif($page == 'view_session') view_session($db, $u_uid);
			elseif($page == 'news') manage_news($db, $u_uid);
			elseif($page == 'edit_news') edit_news($db, $u_uid);
			elseif($page == 'exec') manage_exec($db, $u_uid);
			elseif($page == 'edit_exec') edit_exec($db, $u_uid);
			elseif($page == 'docs') manage_docs($db, $u_uid);
			elseif($page == 'edit_doc') edit_doc($db, $u_uid);
			elseif($page == 'account_request') account_request($db, $u_uid);
			elseif($page == 'manage_requests') manage_requests($db, $u_uid);
			elseif($page == 'view_sales') view_sales($db, $u_uid);
			elseif($page == 'edit_sale') edit_sales($db, $u_uid);
			elseif($page == 'blank'); 
			else no_action();
		}
		else
		{
			no_user();
		}
	}
}

// Close DB if one opened
if($valid > 0)
{
mysql_close($db);
}

?>
</div>
<div class="footer">
<table width="100%">
<tr><td align="left">&copy;Martyn Welch 2005</td>
<td align="right"></td></tr>
</table>
</div>
</body>
</html>
