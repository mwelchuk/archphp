<?php
function manage_scoring($db, $u_uid)
{
	if(check_priv($u_uid, "edit_rounds") < 1){
		no_priv();
		return(0);
	}
	
?> 
<h2>Scoring System Management [<a href="index.php?page=edit_scoring&amp;action=new">Add</a>]</h2>
<table border="0">
	<tr>
		<td class="dark">Name</td>
		<td class="dark">Valid Values</td>
	</tr>
<?php
	$query = "SELECT * FROM `scoring` ORDER BY `ss_uid`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
?>
	<tr>
		<td class="light"><a href="index.php?page=edit_scoring&amp;uid=<?PHP echo $row["ss_uid"]; ?>"><?PHP echo $row["name"]; ?></a></td>
		<td class="light"><?PHP echo $row["values"]; ?></td>
	</tr>
<?php
	}
?>
</table>

<?php
}
?>
