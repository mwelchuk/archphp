<?php
function view_equipment($db, $u_uid)
{
	$eq_uid = trim($_GET['uid']);
	
	if($eq_uid == ''){
		?>
		<h2>Sorry</h2>
		<p>You have not provided a valid equipment ID, please try again. If problems persist please consult your system administrator.</p>
		<?php
		return(0);
	}
	
	$query = "SELECT * FROM `equipment` WHERE `eq_uid` LIKE \"" . $eq_uid . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$row = mysql_fetch_assoc($result);
	
	if((check_priv($u_uid, "view_users") < 1) && ($row["user"] != $u_uid)){
		no_priv();
		return(0);
	}

	$query = "SELECT c_name FROM `class` WHERE `c_uid` LIKE \"" . $row["class"] . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$c_row = mysql_fetch_assoc($result);
	
	$query = "SELECT u_name FROM `user` WHERE `u_uid` LIKE \"" . $row["user"] . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$u_row = mysql_fetch_assoc($result);
	
?>
<h2>Equipment : <?PHP echo $row["e_name"]; ?><?php
	if((check_priv($u_uid, "edit_users") > 0) || ($row["user"] == $u_uid)){
		echo " [<a href=\"index.php?page=edit_equip&amp;uid=$eq_uid\">edit</a>]";
	}
?></h2>
<h3>Class : <?php echo $c_row["c_name"]; ?></h3>
<h3>User : <?php echo $u_row["u_name"]; ?></h3>
<table cols="2">
	<tr>
		<td class="dark">Bow</td>
		<td class="light"><?php echo $row["bow"]; ?></td>
	</tr>
	<tr>
		<td class="dark">Arrows</td>
		<td class="light"><?php echo $row["arrows"]; ?></td>
	</tr>
	<tr>
		<td class="dark">Sight</td>
		<td class="light"><?php echo $row["sight"]; ?></td>
	</tr>
	<tr>
		<td class="dark">Stabilisation</td>
		<td class="light"><?php echo $row["stabilisation"]; ?></td>
	</tr>
	<tr>
		<td class="dark">Extras</td>
		<td class="light"><?php echo $row["extras"]; ?></td>
	</tr>
	<tr>
		<td class="dark">Description</td>
		<td class="light"><?php echo $row["e_desc"]; ?></td>
	</tr>
</table>

<?php
}
?>