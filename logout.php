<?php
include 'login.php';

//here we have to log in so we can get the user's credentials
$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

//now we can log out with this username (mostly for logging purposes - you can ignore username if you want)
logout($userName, FALSE);

//here we redirect back to the logbook page
header("Location: http://www.engr.utk.edu/ambassador/logbook.php");
//just to make sure that nothing after this gets executed somehow
exit();

?>
