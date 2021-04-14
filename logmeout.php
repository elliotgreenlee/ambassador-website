<?php

switch($_GET['logmeout'])  
	{
	case 'yesplease': // logs the user out, politely
		setcookie("username",$logbook,time()-86400); // unsets the cookie and password variable
		$password=0;
		break; // logs the user out
	case 'yes': // not as polite, but still works
		setcookie("username",$logbook,time()-86400); // unsets the cookie and password variable
		$password=0;
		break; // logs the user out
}

if(isset($_GET['redirect'])){ //probably put some other checks in there too
	$redirect = $_GET['redirect'];
} else {
	$redirect = 'index.php';
}
echo "redirect:$redirect\n";
exit;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="refresh" content="0;url=http://www.engr.utk.edu/<?=$redirect?>">
</head>
<body>
</body>
</html>
