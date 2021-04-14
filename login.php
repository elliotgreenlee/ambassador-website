<?php

/* login.php
 * Contains all the code for any type of user authentication for the website. There are two types of logging in:
 * ambassasdor and tour. Ambassadors (and admins) can log in and update profiles, sign up for tours, etc.
 * Tour people can log in and view their tour and provide comments. Each type of login has its own logout
 * function and they must be used appropriately.
 * No visual stuff here, nothing to update
 */

require_once('/home/coeamb/public_html/mysqlFunctions.php');
require_once('/home/coeamb/database/dbInfo.php');

function openDB(){
	global $dbHostName, $dbPortNumber, $dbUserName, $dbPassword, $dbName;
	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName,$dbPassword)
		or die('Could not connect: '.mysql_error());

	mysql_select_db($dbName)
		or die('Could not select database: '.mysql_error());
	return $db;
}

function logout($userName, $loginNotWorking=FALSE){
	$db = openDB();
	$cookieTime = time()-60*60*24;
	setcookie('username', '', $cookieTime, '/', 'www.engr.utk.edu');
	setcookie('password', 0, $cookieTime, '/', 'www.engr.utk.edu');
	
	$safeName = mysql_real_escape_string($userName);
	if($loginNotWorking == FALSE){
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeName', action='Logout'");
	} else {
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeName', action='Login not working'");
	}
	mysql_close($db);
}

function logoutTour($userName, $loginNotWorking=FALSE){
	$db = openDB();
	$cookieTime = time()-60*60*24;
	setcookie('tourUsername', '', $cookieTime, '/', 'www.engr.utk.edu');
	setcookie('tourPassword', 0, $cookieTime, '/', 'www.engr.utk.edu');
	
	$safeName = mysql_real_escape_string($userName);
	if($loginNotWorking == FALSE){
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeName', action='Logout tour'");
	} else {
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeName', action='Login not working tour'");
	}
	mysql_close($db);
}

function loginTour(){
	global $dbSalt;

	$db = openDB();

	$now = time();
	$cookieTime = $now + (60*60*24); //cookies will last 24 hours
	$password = 0;
	
	if(isset($_COOKIE['tourUsername']) && isset($_COOKIE['tourPassword'])){
		$safeUserName = mysql_real_escape_string(htmlspecialchars($_COOKIE['tourUsername']));
		$safeConfirmationCode = mysql_real_escape_string($_COOKIE['tourPassword']);
		$tourRes = mysqlQueryToList("SELECT email FROM tourInfo WHERE email='$safeUserName' AND confirmationCode='$safeConfirmationCode'");
		if(isset($tourRes) && (count($tourRes) != 0)){
			$password = 1; //just a regular user
			$userName = htmlspecialchars($_COOKIE['tourUsername']);
			$confirmationCode = $_COOKIE['tourPassword'];
			//update DB with new login info, make sure cookies stay valid
			setcookie("tourUsername", $userName, $cookieTime, '/', 'www.engr.utk.edu');
			setcookie("tourPassword", $_COOKIE['tourPassword'], $cookieTime, '/', 'www.engr.utk.edu');
		} else {
			$password = 0; //invalid login
		}
	} else {
		//otherwise we have to make sure that the user's UN/PW is valid
		if(isset($_POST['tourUsername']) && isset($_POST['tourPassword'])){
			//if the user is not trying to log in, don't try to query the db
			$safeUserName = mysql_real_escape_string($_POST['tourUsername']);
			$safeConfirmationCode = (mysql_real_escape_string($_POST['tourPassword']));
			//first we check if the user is just a regular user
			$tourRes = mysqlQueryToList("SELECT email FROM tourInfo WHERE email='$safeUserName' AND confirmationCode='$safeConfirmationCode'");
			if(isset($tourRes) && (count($tourRes) != 0)){
				//set cookies for next login attempt
				$userName = $_POST['tourUsername'];
				$password = 1;
				$confirmationCode = $_POST['tourPassword'];
				setcookie("tourUsername", $userName, $cookieTime, '/', 'www.engr.utk.edu');
				setcookie("tourPassword", $_POST['tourPassword'], $cookieTime, '/', 'www.engr.utk.edu');
				mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Logged in - tour'");
			} else {
				$password = 0;
			}
		}
	}

	mysql_close($db);
	
	$retVal = array();
	$retVal['tourUsername'] = $userName;
	$retVal['tourPassword'] = $password;
	$retVal['confirmationCode'] = $confirmationCode;
	return $retVal;
}

function login(){
	global $dbSalt;

	$db = openDB();
	//echo "Username: ".$_POST['username']."\n";
	$now = time();
	$cookieTime = $now + (60*60*24); //cookies will last 24 hours
	//if the user has already logged in, just check that the username is valid
	
	$password = 0;
	if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
		$safeUserName = mysql_real_escape_string($_COOKIE['username']);
		$passWD = $_COOKIE['password'];
		//check if the user is an ambassador first
		$userName = $_COOKIE['username'];
		$safeLoginInfo = sha1(mysql_real_escape_string($dbSalt.$userName.$dbSalt.$passWD.$dbSalt));
		unset($userName);
		$ambRes = mysqlQueryToList("SELECT id FROM ambassadorInfo WHERE netID='$safeUserName' AND loginInfo='$safeLoginInfo'");
		if(isset($ambRes) && (count($ambRes) != 0)){
			$password = 1; //just a regular user
			$userName = $_COOKIE['username'];
			//update DB with new login info, make sure cookies stay valid
			$safeLoginInfo = sha1(mysql_real_escape_string($dbSalt.$userName.$dbSalt.$now.$dbSalt));
			setcookie("username", $userName, $cookieTime, '/', 'www.engr.utk.edu');
			setcookie("password", $now, $cookieTime, '/', 'www.engr.utk.edu');
			mysqlQueryErrorCheck("UPDATE ambassadorInfo SET loginInfo='$safeLoginInfo' WHERE netID='$safeUserName'");
		} else {
			//then we check if the user is an admin
			$specRes = mysqlQueryToList("SELECT id FROM coeambAdmins WHERE netID='$safeUserName' AND type!='0'");
			if(isset($specRes) && (count($specRes) != 0)){
				$password = 2; //this person is an admin user
				$userName = $_COOKIE['username'];
				$safeLoginInfo = sha1(mysql_real_escape_string($dbSalt.$userName.$dbSalt.$now.$dbSalt));
				setcookie("username", $userName, $cookieTime, '/', 'www.engr.utk.edu');
				setcookie("password", $now, $cookieTime, '/', 'www.engr.utk.edu');
				mysqlQueryErrorCheck("UPDATE coeambAdmins SET loginInfo='$safeLoginInfo' WHERE netID='$safeUserName'");
			} else {
				$password = 0; //invalid login
			}
		}
	} else {
		//otherwise we have to make sure that the user's UN/PW is valid
		if(isset($_POST['username']) && isset($_POST['password'])){
			//if the user is not trying to log in, don't try to query the db
			$safeUserName = mysql_real_escape_string($_POST['username']);
			$safePasswordHash = sha1(mysql_real_escape_string($dbSalt.$_POST['password'].$dbSalt));
			//first we check if the user is just a regular user
			$ambRes = mysqlQueryToList("SELECT id FROM ambassadorInfo WHERE netID='$safeUserName' AND password='$safePasswordHash'");
			if(isset($ambRes) && (count($ambRes) != 0)){
				$userName = $_POST['username'];
				$password = 1;
				//set login time in DB, set cookies for next login attempt
				$safeLoginInfo = sha1(mysql_real_escape_string($dbSalt.$userName.$dbSalt.$now.$dbSalt));
				setcookie("username", $userName, $cookieTime, '/', 'www.engr.utk.edu');
				setcookie("password", $now, $cookieTime, '/', 'www.engr.utk.edu');
				mysqlQueryErrorCheck("UPDATE ambassadorInfo SET loginInfo='$safeLoginInfo' WHERE netID='$safeUserName'");
				mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Logged in - amb'");
			} else {
				//then we check if the user is a loginnable admin
				$specRes = mysqlQueryToList("SELECT id FROM coeambAdmins WHERE netID='$safeUserName' AND password='$safePasswordHash' AND type!='0'");
				if(isset($specRes) && (count($specRes) != 0)){
					$userName = $_POST['username'];
					$password = 2;
					//set login time in DB, set cookies for next login attempt
					$safeLoginInfo = sha1(mysql_real_escape_string($dbSalt.$userName.$dbSalt.$now.$dbSalt));
					setcookie("username", $userName, $cookieTime, '/', 'www.engr.utk.edu');
					setcookie("password", $now, $cookieTime, '/', 'www.engr.utk.edu');
					mysqlQueryErrorCheck("UPDATE coeambAdmins SET loginInfo='$safeLoginInfo' WHERE netID='$safeUserName'");
					mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Logged in - admin'");
				} else {
					$password = 0;
				}
			}
		}
	}

	mysql_close($db);
//$password = 1;	
	$retVal = array();
	$retVal['userName'] = $userName;
	$retVal['password'] = $password;

	return $retVal;
}
 
?>
