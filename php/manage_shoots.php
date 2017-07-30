<?php
function manage_shoots($db, $u_uid)
{
//	if(check_priv($u_uid, "edit_classes") < 1){
//		no_priv();
//		return(0);
//	}
	
?> 
<h2>View Shoots<?php if(check_priv($u_uid, "edit_shoots")) echo " [<a href=\"index.php?page=edit_shoot&amp;action=new\">Add</a>]"; ?></h2>
<table border="0">
	<tr>
		<td class="dark">Date</td>
		<td class="dark">Round</td>
		<td class="dark">Location</td>
	</tr>
<?php
	$query = "SELECT * FROM `shoot` ORDER BY `date`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
?>
	<tr>
<?php
	if(check_priv($u_uid, "edit_shoots")){
?>
		<td class="light"><a href="index.php?page=edit_shoot&amp;uid=<?PHP echo $row["sh_uid"]; ?>"><?PHP echo date('d/m/y', strtotime($row["date"])); ?></a></td>
<?php
	}
	else{
?>
		<td class="light"><?PHP echo $row["date"]; ?></td>
<?php
	}
?>
		<td class="light"><?PHP
	$query = "SELECT `name` FROM `round` WHERE `r_uid` LIKE '" . $row["round"] . "'";
	// Perform SQL Query
	$r_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	$r_row = mysql_fetch_assoc($r_result);
	
	echo $r_row['name']; 
?></td>
		<td class="light"><?PHP echo $row["location"]; ?></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>
