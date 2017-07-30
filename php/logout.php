<?php

function logout_page()
{
	//Logging Out - Destroy Session
	session_destroy();
?>

<h2>Logged Out!</h2>
<p>Please return to the <a href="index.php">front page</a>.</p>

<?php
}
?>
