<?PHP
function front_page($db){
	
	$query = "SELECT * FROM `news` WHERE `n_uid` LIKE \"1\"";
	// Make Query
	$result = mysql_query($query, $db) or die("Invalid query: " . mysql_error());
	// Count Returns
	$static_row = mysql_fetch_assoc($result);
	
	
?> 
	<h1><?php echo $static_row['title']; ?></h1>
	<p><?php echo $static_row['content']; ?></p>
	<h2>News</h2>
	<table border="0" cols="1" width="100%">
<?php
	$query = "SELECT * FROM `news` WHERE `n_uid` NOT LIKE '1' ORDER BY `date` DESC LIMIT 10";
	// Make Query
	$result = mysql_query($query, $db) or die("Invalid query: " . mysql_error());
	// Count Returns
	while($n_row = mysql_fetch_assoc($result)){
?>
		<tr>
			<td class="dark" >
				<table cols="2" width="100%"><tr>
				<td align="left"><?php echo $n_row['title']; ?></td>
				<td align="right"><?php echo date('d/m/y', strtotime($n_row["date"])); ?></td>
				</tr></table>
			</td>
		</tr>
		<tr>
			<td class="light" align="left"><?php echo $n_row['content']; ?></td>
		</tr>
		<tr>
			<td> </td>
		</tr>
<?php
	}
?>
	</table>
<?PHP
}
?>
