<?PHP

function main_page($db, $u_uid){

?>
<h2>Archery Club Database</h2>
<p>Welcome to the Archery Club Database. It is not completely finnished yet, there are a lot more features to add, so login from time to time over the next few weeks (or months depending on time and how much feedback I get ;-) ). If you find a bug, would like to request a feature or just want to feed my ego, feel free to <a href="mailto:name@domain.com?subject=Comments from Archery Club Website">email me</a>.</p>
<h3>General Options</h3>
<ul>
	<li><a href="index.php?page=view_user">My Profile</a></li>
	<li><a href="index.php?page=rounds">View Rounds</a></li>
<?PHP
		if(check_priv($u_uid, "view_users")>0){
?>
	<li><a href="index.php?page=view_sales"><b>New!</b> View Items for Sale or Wanted</a></li>
	<li><a href="index.php?page=users">View Other Users Profiles</a></li>
<?PHP
		}
?>
	<li><a href="index.php?page=shoots">View Shoots</a></li>
	<li><a href="index.php?page=sessions">View Scores</a></li>
</ul>
<?php
	if((check_priv($u_uid, "edit_users")>0) || (check_priv($u_uid, "edit_classes")>0) || (check_priv($u_uid, "edit_rounds")>0) || (check_priv($u_uid, "edit_news")>0)){
?>
<h3>Admin Options</h3>
<ul>
<?PHP
		if(check_priv($u_uid, "edit_news")>0){
?>
	<li><a href="index.php?page=news">News Management</a></li>
<?PHP
		}
?>

<?PHP
		if(check_priv($u_uid, "edit_users")>0){
?>
	<li><a href="index.php?page=users">User Management</a></li>
		<ul>
			<li><a href="index.php?page=manage_requests">Outstanding Account Requests</a></li>
		</ul>
<?PHP
		}
?>

<?PHP
		if(check_priv($u_uid, "edit_classes")>0){
?>
	<li><a href="index.php?page=classes">Bow Class Management</a></li>
<?PHP
		}
?>

<?PHP
		if(check_priv($u_uid, "edit_rounds")>0){
?>
	<li><a href="index.php?page=rounds">Manage Rounds</a></li>
	<ul>
		<li><a href="index.php?page=distances">Manage Distances</a></li>
		<li><a href="index.php?page=faces">Manage Faces</a></li>
		<li><a href="index.php?page=scoring">Manage Scoring Systems</a></li>
	</ul>
<?PHP
		}
?>
</ul>
<?php
	}
?>
<?php
}
?>
