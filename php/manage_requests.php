<?php
function manage_requests($db, $u_uid)
{
	if(check_priv($u_uid, "edit_users") < 1){
		no_priv();
		return(0);
	}
	
?> 
<h2>View Open Account Requests [<a href="index.php?page=account_request">Add User</a>]</h2>
<?php
	$query = "SELECT `email`, `expire` FROM `requests` ORDER BY `expire`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());

	if(mysql_num_rows($result) == 0)
	{
?>
		<p>There are currently no outstanding account requests.</p>
<?php
	}
	else
	{
?>
<table border="0">
	<tr>
		<td class="dark">Email Address</td>
		<td class="dark">Expiration Date</td>
	</tr>
<?php

		while($row = mysql_fetch_assoc($result)){

?>
	<tr>
		<td class="light"><?php echo $row["email"]; ?></td>
		<td class="light"><?php echo date('d/m/y H:i:s', strtotime($row["expire"])); ?></td>
	</tr>
<?php
		}
?>
</table>
<?php
	}
}
?>
