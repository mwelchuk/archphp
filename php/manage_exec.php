<?PHP
function manage_exec($db, $u_uid)
{

	
?> 
<h2>The Exec<?php  
	if(check_priv($u_uid, "edit_users")){
		echo " [<a href=\"index.php?page=edit_exec&amp;action=new\">Add</a>]";
	}
?></h2>
<?php
	$query = "SELECT * FROM `exec` ORDER BY `pos_uid`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
		if((check_priv($u_uid, "edit_users") > 0) || ($row["user"] != "0")){
?>
<table border="0" cols="2" width="100%">
	<tr>
		<td class="dark" colspan="2"><h2><?PHP echo $row["position"]; ?><?PHP
			if((check_priv($u_uid, "edit_users") > 0) || ($row["user"] == $u_uid)){
				printf(" [<a href=\"index.php?page=edit_exec&amp;uid=" . $row["pos_uid"] . "\">edit</a>]");
			}
		?></h2></td>
	</tr>
<?php
			$u_query = "SELECT `f_name`,`s_name` FROM `user` WHERE `u_uid`='" . $row["user"] . "'";
			// Perform SQL Query
			$u_result = mysql_query($u_query) or die('Query failed: ' . mysql_error());
			// Get Val
			
			$u_row = mysql_fetch_assoc($u_result);
		
			if($row["user"] != "0"){
?>
	<tr>
		<td class="light" width="120"><img height="170" width="120" alt="[Photo] <?php echo $u_row["f_name"]; ?>" src="get_image.php?uid=<?php echo $row["pos_uid"]; ?>" /></td>
		<td class="light">
			<ul>
				<li>Name: <?PHP echo $u_row["f_name"] . " " . $u_row["s_name"]; ?></li>
				<li>Duties: <?PHP echo $row["duties"]; ?></li>
			</ul>
		</td>
	</tr>
<?php
			}
?>
</table>
<?php
		}
	}
?>
<?php
}
?>
