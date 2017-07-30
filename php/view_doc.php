<?PHP
function view_doc($db)
{
	$uid = trim($_GET['uid']);
	
	if($uid == ''){
		?>
		<h2>Please provide a document</h2>
		<p>It is necessary to provide the UID of the document you wish to view.</p>
		<?php
		return (0);
	}
	
	$query = "SELECT `date`,`title`,`content` FROM `documentation` WHERE `d_uid` LIKE \"" . $uid . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$row = mysql_fetch_assoc($result);
?>

<h1><?php echo $row["title"];?></h1>
<?php echo $row["content"]; ?>
<hr />
<p>Last Edited on: <?php echo date('d/m/y', strtotime($row["date"])); ?></p>
<?php
}
?>