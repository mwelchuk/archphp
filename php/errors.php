<?PHP
function no_connect()
{
?>
	<h2>The Website is currently offline<h2>
	<p>Sorry, this site is currently unavailable. Thank you for your interest, please try again later.</p>
<?PHP
}

function no_database()
{
?>
	<h2>The Website is currently offline</h2>
	<p>Sorry, this site is currently experiencing technical problems. Thank you for your interest, please try again later.</p>
<?PHP
}

function no_user()
{
?>
	<h2>No User</h2>
	<p>Sorry, you do not seem to have a login for the archery database. Please contact your administrator.</p>
<?PHP
}

function wrong_password()
{
?>
	<h2>Wrong Password</h2>
	<p>Sorry, you seem to have used the wrong password. Please try again or contact your administrator if problems persist.</p>
<?PHP
}

function no_action()
{
?>
	<h2>This action does not exist!</h2>
	<p>Please consult your system administrator.</p>
<?PHP
}

function no_priv()
{
?>
	<h2>Sorry</h2>
	<p>You do not have enough privileges to carry out this operation. Please consult your system administrator.</p>
<?PHP
}
?>