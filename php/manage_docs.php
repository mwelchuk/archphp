<?php
function manage_docs($db, $u_uid)
{
?> 
<h2>Club Documentation<?php if(check_priv($u_uid, "edit_docs") > 0) echo " [<a href=\"index.php?page=edit_doc&amp;action=new\">Add</a>]"; ?></h2>
<table border="0">
	<tr>
		<td class="dark">Title</td>
		<td class="dark">Last Edited</td>
		<td class="dark">Description</td>
		<?php if(check_priv($u_uid, "edit_docs") > 0) echo "<td class=\"dark\">Actions</td>"; ?>
	</tr>
<?php
	$query = "SELECT `d_uid`,`date`,`title`,`description` FROM `documentation` ORDER BY `d_uid`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
?>
	<tr>
		<td class="light"><a href="index.php?page=view_doc&amp;uid=<?php echo $row["d_uid"]; ?>"><?php echo $row["title"]; ?></a></td>
		<td class="light"><?PHP echo date('d/m/y', strtotime($row["date"])); ?></td>
		<td class="light"><?PHP echo $row["description"]; ?></td>
		<?php if(check_priv($u_uid, "edit_docs") > 0) echo "<td class=\"light\"><a href=\"index.php?page=edit_doc&amp;uid=" . $row["d_uid"] . "\">Edit</a></td>"; ?>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>
