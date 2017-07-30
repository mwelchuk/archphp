<?PHP
function view_session($db, $u_uid)
{
	$uid = trim($_GET['uid']);
	
	if($uid == ''){
		?>
		<h2>Please provide a session</h2>
		<p>It is necessary to provide the ID of the session you wish to view.</p>
		<?php
		return (0);
	}
	
	$query = "SELECT * FROM `session` WHERE `s_uid` LIKE \"" . $uid . "\"";
	// Perform SQL Query
	$s_result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$s_row = mysql_fetch_assoc($s_result);
	
	$user = $s_row["user"];
	
	$eq_query = "SELECT `e_name` FROM `equipment` WHERE `eq_uid` LIKE '" . $s_row["equipment"] . "'";
	// Perform SQL Query
	$eq_result = mysql_query($eq_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$eq_row = mysql_fetch_assoc($eq_result);
	
	$sh_query = "SELECT * FROM `shoot` WHERE `sh_uid` LIKE '" . $s_row["shoot"] . "'";
	// Perform SQL Query
	$sh_result = mysql_query($sh_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$sh_row = mysql_fetch_assoc($sh_result);
	
	$r_query = "SELECT `name` FROM `round` WHERE `r_uid` LIKE " . $sh_row['round'];
	// Perform SQL Query
	$r_result = mysql_query($r_query) or die('Query failed: ' . mysql_error());
	// Get Val
	$r_row = mysql_fetch_assoc($r_result);

?>
	<h2>View Score<?php if((check_priv($u_uid, "edit_users") > 0) || ($user == $u_uid)){echo " [<a href=\"index.php?page=edit_session&amp;uid=" . $s_row["s_uid"] . "\">Edit</a>]";} ?></h2>
	<table border="0" cols="9" bgcolor="#000000">
		<tr><td colspan="9" bgcolor="#FFFFFF">Location : <?php echo $sh_row['location']; ?></td></tr>
		<tr><td colspan="9" bgcolor="#FFFFFF">Equipment : <?php echo $eq_row['e_name']; ?></td></tr>
		<tr><td colspan="4" bgcolor="#FFFFFF">Round : <?php echo $r_row['name']; ?></td><td colspan="5" bgcolor="#FFFFFF">Date : <?php echo date('d/m/y', strtotime($sh_row['date'])); ?></td></tr>
<?php
	
	$tot_hits = 0;
	$tot_x = 0;
	$tot_golds = 0;
	$tot_score = 0;

        $rd_query = "SELECT round_dists.ends,round_dists.face, distance.* FROM round_dists, distance WHERE round_dists.distance = distance.d_uid AND round_dists.round = ".$sh_row['round'] ." ORDER BY distance.distance DESC LIMIT 0, 30";
	//$rd_query = "SELECT * FROM `round_dists` WHERE `round` = " . $sh_row['round'] . "";
	// Perform SQL Query
	$rd_result = mysql_query($rd_query) or die('Query failed: ' . mysql_error());
	// Get Val
	while($rd_row = mysql_fetch_assoc($rd_result)){
		
		//$d_query = "SELECT `distance`, `unit` FROM `distance` WHERE `d_uid` LIKE " . $rd_row['distance'];
		// Perform SQL Query
		//$d_result = mysql_query($d_query) or die('Query failed: ' . mysql_error());
		// Get Val
		//$d_row = mysql_fetch_assoc($d_result);
		
?>
	<tr>
		<td bgcolor="#FFFFFF">End</td>
		<td bgcolor="#FFFFFF">E/T</td>
		<td bgcolor="#FFFFFF">End</td>
		<td bgcolor="#FFFFFF">E/T</td>
		<td bgcolor="#FFFFFF">H</td>
		<td bgcolor="#FFFFFF">S</td>
		<td bgcolor="#FFFFFF">X</td>
		<td bgcolor="#FFFFFF">G</td>
		<td bgcolor="#FFFFFF">R/T</td>
	</tr>
<?php
		$r_total = 0;
		
		// Loop through ends
		for($i=0; $i<$rd_row['ends']; $i++){
			
//----------------------------------------------------------------------------------------------------------------
			
			$e_query = "SELECT `value1`,`value2`,`value3`,`value4`,`value5`,`value6` FROM `end` WHERE `session` LIKE '" . $uid . "' AND `distance` LIKE '" . $rd_row['d_uid'] . "' AND `end` LIKE '" . $i . "'";
			// Perform SQL Query
			$e_result = mysql_query($e_query) or die('Query failed: ' . mysql_error());
			// Get Val
			$e_row = mysql_fetch_assoc($e_result);
			
			if($i % 2 == 0){
?>
	<tr>
		<td bgcolor="#FFFFFF">
<?php
				$end = 0;
				
				$hits = 0;
				
				$score = 0;
				
				$x = 0;
				$golds = 0;
				
				// Loop through values
				for($j=1; $j<7; $j++){
					$value = $e_row['value' . $j];
					echo $value . " "; 
					if(is_numeric($value)){
						$end += $value;
						$hits++;
					}
					if($value == 'x'){
						$x++;
						$end += 10;
						$hits++;
					}
					if(($value == 'x') || ($value == 10)) $golds++;
				}
				
				$score += $end;

?>
		</td>
		<td bgcolor="#FFFFFF"><?php echo $end; ?></td>
<?php
			}
			else{
?>
		<td bgcolor="#FFFFFF">
<?php
				$end = 0;
				// Loop through values
				for($j=1; $j<7; $j++){
					$value = $e_row['value' . $j];
					echo $value . " "; 
					if(is_numeric($value)){
						$end += $value;
						$hits++;
					}
					if($value == 'x'){
						$x++;
						$end += 10;
						$hits++;
					}
					if(($value == 'x') || ($value == 10)) $golds++;
				}
				
				$score += $end;
				$r_total += $score;
?>
		</td>
		<td bgcolor="#FFFFFF"><?php echo $end; ?></td>
		<td bgcolor="#FFFFFF"><?php echo $hits; ?></td>
		<td bgcolor="#FFFFFF"><?php echo $score; ?></td>
		<td bgcolor="#FFFFFF"><?php echo $x; ?></td>
		<td bgcolor="#FFFFFF"><?php echo $golds; ?></td>
		<td bgcolor="#FFFFFF"><?php echo $r_total; ?></td>
	</tr>
<?php
				$tot_hits += $hits;
				$tot_x += $x;
				$tot_golds += $golds;
				$tot_score += $score;
			}
		}
		
		if($rd_row['ends'] % 2 != 0){
?>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
	</tr>
<?php
		}
		
?>
	<tr>
		<td colspan="4" bgcolor="#FFFFFF">Total at <?php echo $rd_row['distance']; if($rd_row['unit'] == 'm'){ echo " m"; }else{ echo " yrds"; } ?></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"><?php echo $r_total; ?></td>
	</tr>
<?php
	}
?>
	
	<tr>
		<td colspan="4" bgcolor="#FFFFFF">Handicap ..... Grand Total</td>
		<td bgcolor="#FFFFFF"><?php echo $tot_hits; ?></td>
		<td bgcolor="#FFFFFF"></td>
		<td bgcolor="#FFFFFF"><?php echo $tot_x; ?></td>
		<td bgcolor="#FFFFFF"><?php echo $tot_golds; ?></td>
		<td bgcolor="#FFFFFF"><?php echo $tot_score; ?></td>
	</tr>
</table>

<?php
}
