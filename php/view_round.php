<?PHP
function view_round($db, $u_uid)
{
	$uid = trim($_GET['uid']);
	
	if($uid == ''){
		?>
		<h2>Please provide a round</h2>
		<p>It is necessary to provide the name of the round you wish to view.</p>
		<?php
		return (0);
	}
	
//	if(check_priv($u_uid, "view_users") < 1){
//		no_priv();
//		return(0);
//	}

	$query = "SELECT * FROM `round` WHERE `r_uid` LIKE \"" . $uid . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$row = mysql_fetch_assoc($result);
?>
<h2>Round : <?PHP echo $row["name"]; ?><?PHP
	if(check_priv($u_uid, "edit_rounds")){
		echo " [<a href=\"index.php?page=edit_round&amp;uid=$uid\">Edit</a>]";
	}
?></h2>
<h3>Details</h3>
<table cols="2">
<tr><td>Scoring System</td><td><?php 
	
	$query = "SELECT `name` FROM `scoring` WHERE `ss_uid` LIKE \"" . $row['scoring'] . "\"";
	// Perform SQL Query
	$ss_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	
	$ss_row = mysql_fetch_assoc($ss_result);
	
	echo $ss_row["name"]; 
	
	?></td></tr>
</table>
<h3>Distances</h3>
<?php 
	
	$query = "SELECT * FROM `round_dists` WHERE `round` LIKE \"" . $row['r_uid'] . "\"";
	// Perform SQL Query
	$rd_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	
?>
<table cols="2" border="1">
<tr><td>Distance</td><td>Face Type</td><td>Number of Ends (6 arrows)</td></tr>
<?php
	
	// Get Val
	while($rd_row = mysql_fetch_assoc($rd_result)){

		$query = "SELECT * FROM `distance` WHERE `d_uid` LIKE \"" . $rd_row['distance'] . "\"";
		// Perform SQL Query
		$d_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		$d_row = mysql_fetch_assoc($d_result);
		
		if($d_row['unit'] == 'm'){
			$string = $d_row['distance'] . " m";
		}
		else{
			$string = $d_row['distance'] . " yrds";
		}
	
?>
<tr><td><?php echo $string; ?></td>
<td><?php 
		
		$query = "SELECT `f_name` FROM `face` WHERE `f_uid` LIKE \"" . $rd_row['face'] . "\"";
		// Perform SQL Query
		$f_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		
		$f_row = mysql_fetch_assoc($f_result);
		
		echo $f_row["f_name"];
		
	?></td>
<td><?php echo $rd_row["ends"]; ?></td></tr>
<?php
	
	}
	
?>
</table>

<?php
}

?>
