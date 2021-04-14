<?php
/*
todo:
* encapsulate this all into parameterized functions for code control
*/

function getMYSQLAmbResult(){
	$rowsChanged = mysql_affected_rows();
	if($rowsChanged == 0){
		$result = 'WAR: No changes';
	} elseif($rowsChanged == 1){
		$result = 'Amb info changed';
	} elseif($rowsChanged == -1){
		$result = 'ERR: Query Failed';
	} else {
		$result = 'ERR: Updated multiple ambs';
	}
	return $result;
}

function getMYSQLPWResult(){
	$rowsChanged = mysql_affected_rows();
	if($rowsChanged == 0){
		$result = 'PASS: No changes';
	} elseif($rowsChanged == 1){
		$result = 'Password changed';
	} elseif($rowsChanged == -1){
		$result = 'ERR: Query Failed';
	} else {
		$result = 'ERR: Updated multiple passwords';
	}
	return $result;
}

include('login.php');

$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

$lastPage = $_POST['lastPage']; // get the page that directed you to this page

if(($password == 1) || ($password == 2)){
	require_once('/home/coeamb/database/dbInfo.php');

	require_once('/home/coeamb/public_html/mysqlFunctions.php');

	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword)
		or die('Could not connect: '.mysql_error());

	mysql_select_db($dbName)
		or die('Could not select database: '.mysql_error());

	$safeUserName = mysql_real_escape_string($userName);
	$ambInfo = 		mysqlQuerySingleRow("SELECT * FROM ambassadorInfo WHERE netID='$safeUserName' LIMIT 1");
	//if we index the majors by longName, it saves us some queries later on
	$majList = 		mysqlQueryToListIndexBy('SELECT * FROM majorInfo', 'longName');
	$ambID = $ambInfo['id'];
	$safeAmbID = mysql_real_escape_string($ambID);
	switch($_POST['submit']){
		case 'Update Profile Information':
			$paramArray = array();
			foreach($ambInfo as $param => $value){
				if(isset($_POST["amb_${param}${ambID}"])){
					if($param == 'major'){
						//since majors are indexed by longName, grab the object indexed by its name
						$majID = $_POST["amb_major$ambID"];
						$major = $majList["$majID"];
						$paramArray[] = "major='".$major['id']."'";
					} else if($param == 'bio'){
						$bio = mysql_real_escape_string(htmlspecialchars($_POST["amb_${param}${ambID}"]));
						
						$paramArray[] = "bio='$bio'";
					}
					else {
						$paramArray[] = "$param='".mysql_real_escape_string($_POST["amb_${param}${ambID}"])."'";
					}
				} else {
					//$paramArray[] = "$param='$value'";
				}
			}
			$paramString = (implode(', ', $paramArray));
			mysqlQueryErrorCheck("UPDATE ambassadorInfo SET $paramString WHERE id='$safeAmbID'", 'Error on MySQL query saving ambassador information: ');
			$result = mysql_real_escape_string(getMYSQLAmbResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated profile $safeAmbID', result='$result'");
			break; //we made the changes here, no need to check other ambassadors
		case 'Change Password':
			$safeOldPW = sha1(mysql_real_escape_string($dbSalt.$_POST["amb_oldPW$ambID"].$dbSalt));
			if($_POST["amb_new1PW$ambID"] == $_POST["amb_new1PW$ambID"]){
				$safeNewPW = sha1(mysql_real_escape_string($dbSalt.$_POST["amb_new1PW$ambID"].$dbSalt));
				mysqlQueryErrorCheck("UPDATE ambassadorInfo SET password='$safeNewPW' WHERE id='$safeAmbID' AND password='$safeOldPW'");
				$result = mysql_real_escape_string(getMYSQLPWResult());
				mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated password $safeAmbID', result='$result'");
			} else {
				mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated password $safeAmbID', result='Passwords no match'");
			}
			break;
		case 'Reset Password':
			$safePW = sha1(mysql_real_escape_string($dbSalt.$defaultPassword.$dbSalt));
			mysqlQueryErrorCheck("UPDATE ambassadorInfo SET password='$safePW' WHERE id='$safeAmbID'");
			$result = mysql_real_escape_string(getMYSQLPWResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Reset password $safeAmbID', result='$result'");
			break;
		}
}

switch($_POST['submit']){
	case 'logout':
		require_once('/home/coeamb/public_html/mysqlFunctions.php');
		require_once('/home/coeamb/database/dbInfo.php');
		$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
		mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
		logout($userName); //included from login.php
		break;
	case 'My login info isn\'t working':
		require_once('/home/coeamb/public_html/mysqlFunctions.php');
		require_once('/home/coeamb/database/dbInfo.php');
		$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
		mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
		logout($userName, TRUE); //just clears the cookies, lets the user try to log in again
		break;
}

//here we redirect back to the manager page
header("Location: http://www.engr.utk.edu/ambassador/$lastPage");
//just to make sure that nothing after this gets executed somehow
exit();

?>
