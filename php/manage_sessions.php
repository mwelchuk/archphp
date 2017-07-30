<?php
function manage_sessions($db, $u_uid)
{

	$uid = trim($_GET['uid']);
	
	if($uid == '') $uid = $u_uid;

	if((check_priv($u_uid, "view_users") < 1) && ($u_uid != $uid)){
		no_priv();
		return(0);
	}
	
	$query = "SELECT `f_name`,`s_name` FROM `user` WHERE `u_uid` LIKE \"" . $uid . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	$row = mysql_fetch_assoc($result);
	
?> 
<h2>View Scores<?php if($u_uid == $uid) echo " [<a href=\"index.php?page=edit_session&amp;action=new\">Add</a>]"; ?></h2>
<h3>User: <?php echo $row['f_name'] . " " . $row['s_name']; ?></h3>
<?php
	$s_query = "SELECT * FROM `session` WHERE `user` LIKE \"" . $uid . "\"";
	// Perform SQL Query
	$s_result = mysql_query($s_query) or die('Query failed: ' . mysql_error());
	// Check to see if any sessions have been defined
	if(mysql_num_rows($s_result) < 1){
?>
<p>There is currently no scores associated with this user.<?php if($u_uid == $uid) echo " Please <a href=\"index.php?page=edit_session&amp;action=new\">add</a> some!"; ?></p>
<?php
	}else{
?>
<table border="0">
	<tr>
		<td class="dark">Date</td>
		<td class="dark">Round</td>
		<td class="dark">Equipment</td>
		<td class="dark">Score</td>
	</tr>
<?php	
	while($s_row = mysql_fetch_assoc($s_result)){
		$sh_query = "SELECT `date`,`round` FROM `shoot` WHERE `sh_uid` LIKE \"" . $s_row['shoot'] . "\"";
		// Perform SQL Query
		$sh_result = mysql_query($sh_query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$sh_row = mysql_fetch_assoc($sh_result);

?>
<tr>
<td class="light"><a href="index.php?page=view_session&amp;uid=<?PHP echo $s_row["s_uid"]; ?>"><?PHP echo date('d/m/y', strtotime($sh_row["date"])); ?></a></td>
<td class="light"><?PHP 
		$r_query = "SELECT `name` FROM `round` WHERE `r_uid` LIKE \"" . $sh_row['round'] . "\"";
		// Perform SQL Query
		$r_result = mysql_query($r_query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$r_row = mysql_fetch_assoc($r_result);
		
		echo $r_row["name"]; 
?></td>
<td class="light"><?PHP 
		$eq_query = "SELECT `e_name` FROM `equipment` WHERE `eq_uid` LIKE \"" . $s_row['equipment'] . "\"";
		// Perform SQL Query
		$eq_result = mysql_query($eq_query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$eq_row = mysql_fetch_assoc($eq_result);
		
		echo $eq_row["e_name"]; 
?></td>
<td class="light"><?PHP echo $s_row["score"]; ?></td>
</tr>
<?php
	}
?>
</table>
<?php
	}
}
?>
