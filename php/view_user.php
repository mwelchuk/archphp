<?PHP
function view_user($db, $u_uid)
{
	$name = trim($_GET['name']);
	
	if($name == ''){
		$name = $u_uid;
	}
	
	if((check_priv($u_uid, "view_users") < 1) && ($u_uid != $name)){
		no_priv();
		return(0);
	}
	
	$query = "SELECT * FROM `user` WHERE `u_uid` LIKE \"" . $name . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$row = mysql_fetch_assoc($result);
?>
<h2>User Profile : <?PHP echo $row["f_name"] . " " . $row["s_name"]; ?></h2>
<h3>Basic Details<?php
	if(check_priv($u_uid, "edit_users") || ($u_uid == $name)){
		echo " [<a href=\"index.php?page=edit_user&amp;name=$name\">Edit</a>]";
	}
?></h3>
<table cols="2">
	<tr><td>First Name</td><td><?php echo $row["f_name"]; ?></td></tr>
	<tr><td>Surname Name</td><td><?php echo $row["s_name"]; ?></td></tr>
	<tr><td>Created</td><td><?php echo date('d/m/y', strtotime($row["created"])); ?></td></tr>
	<tr><td>Last Logged in</td><td><?php echo date('d/m/y', strtotime($row["last_log"])); ?></td></tr>
	<tr><td>Description</td><td><?php echo $row["description"]; ?></td></tr>
</table>

<h3>Equipment <?php if($u_uid == $name){ ?>[<a href="index.php?page=edit_equip&amp;action=new">Add</a>]<?php } ?></h3>
<?php
	$query = "SELECT eq_uid,e_name,e_desc FROM `equipment` WHERE `user` LIKE '" . $name . "'";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	if(mysql_num_rows($result) > 0){
?>
<table>
<?php		
		while($e_row = mysql_fetch_assoc($result)){
?>

	<tr><td><a href="index.php?page=view_equip&amp;uid=<?php echo $e_row["eq_uid"]; ?>"><?php echo $e_row["e_name"]; ?></a></td><td><?php echo $e_row["e_desc"]; ?></td></tr>

<?php
		}
	
?>
</table>
<?php
	}
	else{
?>
<p>There is currently no equipment associated with this user.<?php if($u_uid == $name){ ?> Please <a href="index.php?page=edit_equip&amp;action=new">add</a> some!<?php } ?></p>
<?php
	}
?>
<?php

	$eq_query = "SELECT `eq_uid`,`e_name` FROM `equipment` WHERE `user` LIKE '" . $name . "'";
	// Perform SQL Query
	$eq_result = mysql_query($eq_query) or die('Query failed: ' . mysql_error());
	// Get Val
	if(mysql_num_rows($eq_result) > 0){
	//If have equipment show personal bests
?>
<h3>Personal Bests [<a href="index.php?page=sessions&uid=<?php echo $name; ?>">All Scores</a>]</h3>
<?php
		while($eq_row = mysql_fetch_assoc($eq_result)){
			echo "<h4>" . $eq_row['e_name'] . "</h4>\n";
			$s_query = "
			SELECT MAX(session.score) AS score,round.name FROM `session` LEFT JOIN `shoot` ON session.shoot = shoot.sh_uid LEFT JOIN `round` ON shoot.round = round.r_uid WHERE session.user LIKE '" . $name . "' AND session.equipment LIKE '" . $eq_row['eq_uid'] . "' GROUP BY round.name";
			$s_result = mysql_query($s_query) or die('Query failed: ' . mysql_error());
			//Show if scores in database with equipment
			if(mysql_num_rows($s_result) > 0){
				echo "<ul>\n";
				while($s_row = mysql_fetch_assoc($s_result)){
					echo "<li>" . $s_row['name'] . " = " . $s_row['score'] . "</li>\n";
				}
				echo "</ul>\n";
			}
			else{
				echo "<p>No scores have been entered using this equipment.</p>";
			}
		}
	}

	if(check_priv($u_uid, "edit_users") > 0){
?>
<h3>System Privileges</h3>
<table cols="2">
<?PHP
		$query = "SELECT * FROM `priv_level` ORDER BY `p_uid`";
		// Perform SQL Query
		$priv_result = mysql_query($query) or die('Query failed: ' . mysql_error());
		// Get Val
		while($priv_row = mysql_fetch_array($priv_result, MYSQL_NUM)){
			printf("<tr><td>" . $priv_row[1] . "</td><td>");
			if((($priv_row[0]+1)-1)&(($row["privilege_level"]+1)-1)){
				echo "yes";
			}else{
				echo "no";
			}
			printf("</td></tr>\n");
		}
?>
</table>
<?PHP
	}	
}
?>