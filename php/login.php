<?PHP
function login_page(){
?> 
	<h1>ArchPHP Scoring Database</h1>
	<p>Welcome to the ArchPHP Database. Please login below. If you currently don't have an account please speak to a member of <a href="index.php?page=exec">the exec</a>:</p>
	<form method="post" action="index.php?action=login&amp;page=main">
	<center>
	<table border="0" cols="2">
		<tr>
			<td align="right">User</td>
			<td align="left"><input name="username" /></td>
		</tr>
		<tr>
			<td align="right">Password</td>
			<td alight="left"><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td> </td>
			<td align="center"><input type="submit" /></td>
		</tr>
	</table>
	</center>
	</form>
<?PHP
}
?>
