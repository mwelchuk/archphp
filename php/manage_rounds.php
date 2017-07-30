<?php
function manage_rounds($db, $u_uid)
{
?> 
<h2>Round Management <?php if(check_priv($u_uid, "edit_classes") > 0) echo " [<a href=\"index.php?page=edit_round&amp;action=new\">Add</a>]"; ?></h2>
<table border="0">
	<tr>
		<td class="dark">Round</td>
		<td class="dark">Scoring System</td>
	</tr>
<?php
	$query = "SELECT * FROM `round` ORDER BY `name`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
?>
	<tr>
		<td class="light"><a href="index.php?page=view_round&amp;uid=<?PHP echo $row["r_uid"]; ?>"><?php echo $row["name"]; ?></a></td>
		<td class="light"><?php 
		
		$query = "SELECT `name` FROM `scoring` WHERE `ss_uid` LIKE \"" . $row['scoring'] . "\"";
		// Perform SQL Query
		$ss_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$ss_row = mysql_fetch_assoc($ss_result);
		
		echo $ss_row["name"]; 
		
		?></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>
