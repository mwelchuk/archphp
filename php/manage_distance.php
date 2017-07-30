<?php
function manage_distance($db, $u_uid)
{
	if(check_priv($u_uid, "edit_rounds") < 1){
		no_priv();
		return(0);
	}
	
?> 
<h2>Distance Management [<a href="index.php?page=edit_dist&amp;action=new">Add</a>]</h2>
<?php
	$query = "SELECT * FROM `distance` ORDER BY `d_uid`";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	while($row = mysql_fetch_assoc($result)){
?>
<a href="index.php?page=edit_dist&amp;uid=<?PHP echo $row["d_uid"]; ?>"><?PHP echo $row["distance"]; 
if($row["unit"] == 'i'){
	echo "yrds";
}
else{
	echo "m";
}
 ?></a><br />
<?php
	}
}
?>
