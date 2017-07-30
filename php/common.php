<?PHP
	
function check_priv($u_uid, $required){

	// Check to see if user has enough privileges
	$query = "SELECT `privilege_level` FROM `user` WHERE `u_uid` LIKE \"" . $u_uid . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$row = mysql_fetch_row($result);
	//Adding and subtracting 1 turns the string to a number...
	$user_priv = $row[0]+1-1;
	
	
	// Check to see if user has enough privileges
	$query = "SELECT `p_uid` FROM `priv_level` WHERE `name` LIKE \"" . $required . "\"";
	// Perform SQL Query
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	// Get Val
	$row = mysql_fetch_row($result);
	//Adding and subtracting 1 turns the string to a number...
	$req_priv = $row[0]+1-1;
	
	
	//echo "$user_priv\n";
	//echo "$req_priv\n";
	//echo $user_priv & $req_priv;
	
	if(($user_priv & $req_priv) > 0){
		return(1);
	}
	else{
		return(0);
	}
} 


// Courtesy of "http://www.ilovejackdaniels.com/php/email-address-validation/"

function check_email_address($email){
  // First, we check that there's one @ symbol, and that the lengths are right
  if (!ereg("[^@]{1,64}@[^@]{1,255}", $email)) {
    // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
     if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
      return false;
    }
  }  
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}

?>
