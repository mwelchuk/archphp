<?php
function manage_news($db, $u_uid)
{
	if(check_priv($u_uid, "edit_news") < 1){
		no_priv();
		return(0);
	}
	
?> 
<h2>News Management [<a href="index.php?page=edit_news&amp;action=new">Add</a>]</h2>
<table border="0">
	<tr>
		<td class="dark">Title</td>
		<td class="dark">Date</td>
	</tr>
<?php
	$query = "SELECT `n_uid`,`date`,`title` FROM `news` ORDER BY `date`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
?>
	<tr>
		<td class="light"><a href="index.php?page=edit_news&amp;name=<?PHP echo $row["n_uid"]; ?>"><?PHP echo $row["title"]; ?></a></td>
		<td class="light"><?PHP echo $row["date"]; ?></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>
