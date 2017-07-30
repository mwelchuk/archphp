<?php
function manage_users($db, $u_uid)
{
	
	if(check_priv($u_uid, "view_users") < 1){
		no_priv();
		return(0);
	}
?> 
<h2>Users<?php  
	if(check_priv($u_uid, "edit_users")){
		echo " [<a href=\"index.php?page=account_request\">Add</a>]";
	}
?></h2>
<table border="0">
	<tr>
		<td class="dark">Username</td>
		<td class="dark">First Name</td>
		<td class="dark">Surname</td>
		<td class="dark">Best Portsmouth</td>
		<td class="dark">Last Logged On</td>
		<td class="dark">Description</td>
<?php
	if(check_priv($u_uid, "edit_users")){
?>
		<td class="dark">Actions</td>
<?php
	}
?>
	</tr>
<?php
	$query = "SELECT * FROM `user` ORDER BY `u_name`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
	
		$b_query = "SELECT MAX(session.score) AS score FROM `session` LEFT JOIN `shoot` ON session.shoot = shoot.sh_uid LEFT JOIN `round` ON shoot.round = round.r_uid WHERE session.user LIKE '" . $row["u_uid"] . "' AND round.name LIKE 'Portsmouth'";
		$b_result = mysql_query($b_query) or die('Query failed: ' . mysql_error());
		$b_row = mysql_fetch_assoc($b_result)

?>
	<tr>
		<td class="light"><a href="index.php?page=view_user&amp;name=<?PHP echo $row["u_uid"]; ?>"><?PHP echo $row["u_name"]; ?></a></td>
		<td class="light"><?php echo $row["f_name"]; ?></td>
		<td class="light"><?php echo $row["s_name"]; ?></td>
		<td class="light"><?php echo $b_row["score"]; ?></td>
		<td class="light"><?php echo date('d/m/y', strtotime($row["last_log"])); ?></td>
		<td class="light"><?php echo $row["description"]; ?></td>
<?php
	if(check_priv($u_uid, "edit_users")){
?>
		<td class="light">
			<a href="index.php?page=edit_user&amp;name=<?php echo $row["u_uid"]; ?>">Edit</a>  
			<a href="index.php?page=edit_user&amp;name=<?php echo $row["u_uid"]; ?>&amp;action=delete">Delete</a>
		</td>
<?php
	}
?>
	</tr>
<?PHP
	}
?>
</table>
<?PHP
}
?>
