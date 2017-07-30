<?php
function manage_face($db, $u_uid)
{
	if(check_priv($u_uid, "edit_rounds") < 1){
		no_priv();
		return(0);
	}
	
?> 
<h2>Face Management [<a href="index.php?page=edit_face&amp;action=new">Add</a>]</h2>
<table border="0">
	<tr>
		<td class="dark">Name</td>
		<td class="dark">Size</td>
		<td class="dark">Number</td>
	</tr>
<?php
	$query = "SELECT * FROM `face` ORDER BY `size` DESC";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
?>
	<tr>
		<td class="light"><a href="index.php?page=edit_face&amp;uid=<?PHP echo $row["f_uid"]; ?>"><?PHP echo $row["f_name"]; ?></a></td>
		<td class="light"><?PHP echo $row["size"]; ?></td>
		<td class="light"><?PHP echo $row["number"]; ?></td>
	</tr>
<?php
	}
?>
</table>

<?php
}
?>
