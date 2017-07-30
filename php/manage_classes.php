<?php
function manage_classes($db, $u_uid)
{
	if(check_priv($u_uid, "edit_classes") < 1){
		no_priv();
		return(0);
	}
	
?> 
<h2>Bow Class Management [<a href="index.php?page=edit_class&amp;action=new">Add</a>]</h2>
<table border="0">
	<tr>
		<td class="dark">Class</td>
		<td class="dark">Description</td>
	</tr>
<?php
	$query = "SELECT * FROM `class` ORDER BY `c_name`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
?>
	<tr>
		<td class="light"><a href="index.php?page=edit_class&amp;name=<?PHP echo $row["c_uid"]; ?>"><?PHP echo $row["c_name"]; ?></a></td>
		<td class="light"><?PHP echo $row["c_desc"]; ?></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>
