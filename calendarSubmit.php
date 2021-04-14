<?php
/* calendarSubmit.php
 *  This file processes any changes made from the calendar page. The actions that can occur here are an ambassador
 *  changing a tour status, changing their schedule, or logging out. The master page has no actions yet.
 *
 *TODO:
 * Everything.
 * Make sure that only the new info is written to file
 * Back up data before I change anything?
 */

date_default_timezone_set('America/New_York'); //so that dates will show up as knoxville time instead of time local to the user

//not sure if this is necessary
function returnToLastPage(){
	//here we redirect back to the calendar page
	$lastPage = $_POST['lastPage'];
	$lastPageClean = str_replace(array('/', '\\'), array('', ''), $lastPage); 
	header("Location: http://www.engr.utk.edu/ambassador/$lastPageClean");
	//just to make sure that nothing after this gets executed somehow
	exit();
}

function getMYSQLTourResult(){
	$rowsChanged = mysql_affected_rows();
	if($rowsChanged == 0){
		$result = 'WAR: No changes';
	} elseif($rowsChanged == 1){
		$result = 'Tour changed';
	} elseif($rowsChanged == -1){
		$result = 'ERR: Query Failed';
	} else {
		$result = 'ERR: Updated multiple tours';
	}
	return $result;
}

include('login.php');

$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

if(($password == 1) || ($password == 2)){
	
	require_once('/home/coeamb/database/dbInfo.php');

	require_once('/home/coeamb/public_html/mysqlFunctions.php');

	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());

	mysql_select_db($dbName) or die('Could not select database: '.mysql_error());

	$tourInfoList = mysqlQueryToList('SELECT * FROM tourInfo');
	$safeAmbName = mysql_real_escape_string($userName);
	$ambInfo = mysqlQuerySingleRow("SELECT * FROM ambassadorInfo where netID='$safeAmbName' LIMIT 1");
	$safeID = mysql_real_escape_string($ambInfo['id']);
	
	foreach($tourInfoList as $tour){
		$tourID = $tour['id'];
		$safeTourID = mysql_real_escape_string($tourID);
		//if we are updating tour information
		if(isset($_POST["saveTour$tourID"]) && ($_POST["saveTour$tourID"] == 'Save Changes')){
			$paramArray = array();
			foreach($tour as $param => $value){
				if(isset($_POST["tour_${param}${tourID}"])){
					$paramArray[] = "$param='".mysql_real_escape_string($_POST["tour_${param}${tourID}"])."'";
				} else {
					//$paramArray[] = "$param='$value'";
				}
			}
			$paramString = (implode(', ', $paramArray));
			mysqlQueryErrorCheck("UPDATE tourInfo SET $paramString WHERE id='$safeTourID'");
			$result = mysql_real_escape_string(getMYSQLTourResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeAmbName', action='Updated tour $safeTourID', result='$result'");
			break; //we made the changes here, no need to check other tours
		}
		
		if(isset($_POST["acceptTour$tourID"]) && ($_POST["acceptTour$tourID"] == 'Accept Tour')){
			//added a 'AND status='0' ' to make sure that only the first person to grab this tour will get it.
			mysqlQueryErrorCheck("UPDATE tourInfo SET status=1, ambassadorAssigned='$safeID' WHERE id='$safeTourID' AND status='0'");
			$result = mysql_real_escape_string(getMYSQLTourResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeAmbName', action='Accepted tour $safeTourID', result='$result'");
			break;
		}
		
		if(isset($_POST["denyTour$tourID"]) && ($_POST["denyTour$tourID"] == 'Deny Tour')){
			mysqlQueryErrorCheck("UPDATE tourInfo SET status=0, ambassadorAssigned='0' WHERE id='$safeTourID'");
			$result = mysql_real_escape_string(getMYSQLTourResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeAmbName', action='Denied tour $safeTourID', result='$result'");
			break;
		}
		
		if(isset($_POST["noshowTour$tourID"]) && ($_POST["noshowTour$tourID"] == 'Family did not show up for tour')){
			mysqlQueryErrorCheck("UPDATE tourInfo SET status=4 WHERE id='$safeTourID'");
			$result = mysql_real_escape_string(getMYSQLTourResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeAmbName', action='No show'd tour $safeTourID', result='$result'");
			break;
		}
	}
	
	mysql_close($db);
}

if(isset($_POST["submit"]) && ($_POST["submit"] == 'logout')){
	require_once('/home/coeamb/public_html/mysqlFunctions.php');
	require_once('/home/coeamb/database/dbInfo.php');
	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
	mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
	logout($userName); //included from login.php
}

if(isset($_POST["submit"]) && ($_POST["submit"] == 'My password isn\'t working')){
	require_once('/home/coeamb/public_html/mysqlFunctions.php');
	require_once('/home/coeamb/database/dbInfo.php');
	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
	mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
	logout($userName, TRUE); //just clears the cookies, lets the user try to log in again
}

returnToLastPage();

?>
