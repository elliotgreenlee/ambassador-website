<?php
/*
 * backend code for handling the user input on manager.php
 * TODO: Make sure that the input is always checked for changes before taking any other actions
 * 	* But that would take so much more work
 * TODO: Use popups to make sure that the user is sure before continuing?
 * 	* I don't think this is possible using PHP. It would have to be javascript.
 */
 
require_once('excel/Worksheet.php');
require_once('excel/Workbook.php');

function getMYSQLResult(){
	$rowsChanged = mysql_affected_rows();
	if($rowsChanged == 0){
		$result = 'WAR: No changes';
	} elseif($rowsChanged == 1){
		$result = 'Record changed';
	} elseif($rowsChanged == -1){
		$result = 'ERR: Query Failed';
	} else {
		$result = 'ERR: Updated multiple records';
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

$widths = array('.' => 1, 'a' => 1.73, 'b' => 1.8, 'c' => 1.51, 'd' => 1.8, 'e' => 1.73, 'f' => 1.05,
	'g' => 1.8, 'h' => 1.82, 'i' => 0.78, 'j' => 0.93, 'k' => 1.64, 'l' => 0.78, 'l' => 0.93, 'm' => 2.73,
	'n' => 1.82, 'o' => 1.78, 'p' => 1.8, 'r' => 1.2, 's' => 1.45, 't' => 1.11, 'u' => 1.82, 'w' => 2.91,
	'x' => 1.62, 'y' => 1.64, 'z' => 1.45, '0' => 1.78, '1' => 1.78, '2' => 1.78, '3' => 1.78, '4' => 1.78,
	'5' => 1.78, '6' => 1.78, '7' => 1.78, '8' => 1.78, '9' => 1.78, 'A' => 1.96, 'B' => 1.91, 'C' => 1.96,
	'D' => 2.2, 'E' => 1.84, 'F' => 1.71, 'G' => 2.18, 'H' => 2.2, 'I' => 1.22, 'J' => 1.36, 'L' => 1.69,
	'K' => 1.91, 'L' => 1.63, 'M' => 2.51, 'N' => 2.16, 'O' => 2.31, 'P' => 1.8, 'R' => 2.02, 'S' => 1.82,
	'T' => 2.09, 'U' => 2.15, 'W' => 2.93, 'X' => 1.91, 'Y' => 1.89, 'Z' => 1.82, '(' => 1.2, ')' => 1.2,
	'-' => 1.2, '–' => 1.8, '—' => 2.93, ' ' => 1.11, ':' => 1, '\\' => 1.2, '/' => 1.2);
$avg = -1;

/*
 * Takes a string and returns the width in pixels if it were written in Arial 10
 */
function getTextWidth($text){
	global $widths, $avg;
	$len = strlen($text);
	$width = 0.0;
	for($i = 0; $i < $len; $i++){
		$width += $widths[$text[$i]];
	}
	return $width;
}

/*
 * Given two semester names, performs a strcmp-like comparison on the two semesters to sort in descending order
 */
function compareSemesters($sA, $sB){
	$dA = split(' ', $sA);
	$dB = split(' ', $sB);
	if((count($dA) != 2) || (count($dB) != 2) || (count($dA) != count($dB))) return 0;
	if(intval($dA[0]) < intval($dB[0])) return 1;
	else if (intval($dA[0]) > intval($dB[0])) return -1;
	else {
		$times = array("Spring" => 0, "Summer" => 1, "Fall" => 2);
		if($times[$dA[1]] < $times[$dB[1]]) return 1;
		else if($times[$dA[1]] > $times[$dB[1]]) return -1;
		else return 0;
	}
}

/*
 * Given a name for the semester, a list of the tours, and a workbook, creates the sheets for a semester's tours
 */
function createTourLogSheet($name, $data, &$workBook, $writeStats){
	$file = 0;
	$excel = 1;
	$cols = array('Tour ID', 'Date/Time', 'Majors', 'Student', 'Parent', 'People', 'School', 'Year', 'City', 'State', 'Email', 'Phone', 'Tour Status', 'Comments from Family', 'Comments from Ambassadors');
	$entries = array('id', 'tourTime', 'majorsOfInterest', 'studentName', 'parentName', 'numPeople', 'school', 'yearInSchool', 'city', 'state', 'email', 'phone', 'status', 'tourComments', 'ambComments');

	$tourDayCounts = array_fill(0, 7, 0);
	$tourWeekCounts = array_fill(0, 53, 0);
	$tourDateCounts = array_fill(0, 53, null);
	$weekStrings = array();
	$numSemesterTours = count($data);
	$timeStringLength = 0;
	if($excel)
		$tourSheet = & $workBook->add_worksheet($name. ' Tours');
	if($file)
		fwrite($f, "\nWorksheet: $name Tours\n");
	$numCols = count($cols);
	for($col = 0; $col < $numCols; $col++){
		$colName = $cols[$col];
		$colRef = $entries[$col];
		$maxWidth = getTextWidth($colName);
		if($excel)
			$tourSheet->write_string(0, $col, $colName);
		if($file)
			fwrite($f, "Row: 0, Col: $col, $colRef: $colName, width:$maxWidth\t");

		for($tour = 0; $tour < $numSemesterTours; $tour++){
			$text = $data[$tour][$colRef];
			$width = getTextWidth($text);
			if($width > $maxWidth){
				$maxWidth = $width;
			}

			if($excel){
				if(is_numeric($text)){
					$tourSheet->write_number($tour + 1, $col, intval($text));
				} else {
					$tourSheet->write_string($tour + 1, $col, $text);
				}
			}
			if($file)
				fwrite($f, "Row: $tour, Col: $col, val: $text, width: $width\t");

			/*
			 //formats do not work at the moment
			 if($col == 0){
				$tourSheet->set_row($tour + 1, NULL, $formatOffset + ($tour % 2));
			}
			*/
		}
		if($file)
			fwrite($f, "\n");
		if($excel)
			$tourSheet->set_column($col, $col, $maxWidth * (2.0/3.0));
	}

	for($tour = 0; $tour < $numSemesterTours; $tour++){
		if($file)
			fwrite($f, "Week 03: ".$tourWeekCounts["03"]."\n");
		//and now we add each tour to the stats
		$timestamp = strtotime($data[$tour]['tourTime']);
		if($file)
			fwrite($f, "timestamp: $timestamp Time:".$tour['tourTime']." Week: ".date('W', $timestamp)."\n");
		if(($timestamp == false) || ($timestamp == -1)) continue;
		$tourDOW = intval(date('w', $timestamp));
		$tourDayCounts["$tourDOW"] += 1;
		$tourWeek = intval(date('W', $timestamp));
		$tourWeekCounts["$tourWeek"] += 1;
		if($tourDateCounts["$tourWeek"] == null){
			$tourDateCounts["$tourWeek"] = array_fill(0,7,0);
		}
		$tourDateCounts["$tourWeek"]["$tourDOW"] += 1;

		//and create the date string for this week if it doesn't exist already
		if(!array_key_exists($tourWeek, $weekStrings)){
			$timeInfo = getdate($timestamp);
			$sunTimestamp = mktime(0,0,0, $timeInfo['mon'], $timeInfo['mday'] - $tourDOW, $timeInfo['year']);
			$satTimestamp = mktime(0,0,0, $timeInfo['mon'], $timeInfo['mday'] - $tourDOW + 6, $timeInfo['year']);
			if(date('M', $sunTimestamp) == date('M', $satTimestamp)){
				$timeStr = date('M j', $sunTimestamp) . ' - ' . date('j', $satTimestamp);
			} else {
				$timeStr = date('M j', $sunTimestamp) . ' - ' . date('M j', $satTimestamp);
			}
			$weekStrings["$tourWeek"] = $timeStr;
			$tsl = getTextWidth($timeStr);
			if($tsl > $timeStringLength) $timeStringLength = $tsl;
		}
	}

	if(!$writeStats) return;

	if($excel)
		$statsSheet = &$workBook->add_worksheet($name.' Stats');

	//fill the column headers and set the the column widths
	$statsSheet->set_column(0, 0, $timeStringLength * (2.0/3.0));
	$statsSheet->write_string(0, 1, "Monday");
	$statsSheet->set_column(1, 1, getTextWidth("Monday") * (2.0/3.0));
	$statsSheet->write_string(0, 2, "Tuesday");
	$statsSheet->set_column(2, 2, getTextWidth("Tuesday") * (2.0/3.0));
	$statsSheet->write_string(0, 3, "Wednesday");
	$statsSheet->set_column(3, 3, getTextWidth("Wednesday") * (2.0/3.0));
	$statsSheet->write_string(0, 4, "Thursday");
	$statsSheet->set_column(4, 4, getTextWidth("Thursday") * (2.0/3.0));
	$statsSheet->write_string(0, 5, "Friday");
	$statsSheet->set_column(5, 5, getTextWidth("Friday") * (2.0/3.0));
	$statsSheet->write_string(0, 6, "Total");
	$statsSheet->set_column(6, 6, getTextWidth("Total") * (2.0/3.0));

	//then start populating all the data from the tours
	$numWeeks = count($tourDateCounts);
	$displayWeek = 0;
	//write the counts for each week
	for($week = 0; $week < $numWeeks; $week++){
		if($file){
			fwrite($f, "Week $week, Tours ".$tourWeekCounts[$week]."\n");
			for($i = 0; $i < 7; $i++){
				fwrite($f, "Day $i, Tours ".$tourDateCounts[$week][$i]."\n");
			}
		}
		if($tourWeekCounts[$week] == 0) continue;
		$statsSheet->write_string($displayWeek+1, 0, $weekStrings[$week]);
		for($day = 1; $day < 6; $day++){
			if($excel)
				$statsSheet->write_number($displayWeek + 1, $day, $tourDateCounts[$week][$day]);
			if($file)
				fwrite($f, "Week $week, Day $day, Tours ".$tourDateCounts[$week][$day]."\n");
		}
		//write the totals for each week
		if($excel)
			$statsSheet->write_number($displayWeek + 1, 6, $tourWeekCounts[$week]);
		if($file)
			fwrite($f, "Week $week, Total Tours ".$tourWeekCounts[$week]."\n");
		$displayWeek++;
	}
	//then write the totals for the semester
	for($day = 1; $day < 6; $day++){
		if($excel)
			$statsSheet->write_number($displayWeek + 1, $day, $tourDayCounts[$day]);
		if($file)
			fwrite($f, "Day $day, Total Tours ".$tourDayCounts[$day]."\n");
	}

	if($excel)
		$statsSheet->write_number($displayWeek + 1, 6, $numSemesterTours);
	if($file)
		fwrite($f, "Total Tours: $numSemesterTours\n");

	unset($tourDayCounts);
	unset($tourWeekCounts);
	unset($tourDateCounts);
	unset($weekStrings);
}

/*
 * Given a name for the semester, a list of the tours, and a workbook, creates the sheets for a semester's hours
 */
function createHourLogSheet($name, $data, &$workBook, $ambs){
	$file = 0;
	$excel = 1;

	$cols = array('Log ID', 'Date', 'Date Logged', 'Ambassador', 'Event Name', 'Hours', 'People', 'Schools', 'Experience', 'Questions', 'Would make your job better', 'Improvements you could make');
	$entries = array('id', 'eventDate', 'logTime', 'ambassador', 'eventName', 'hours', 'peopleInteracted', 'otherSchools', 'experience', 'questions', 'madeJobBetter', 'improvements');

	$numSemesterTours = count($data);
	if($excel)
		$tourSheet = & $workBook->add_worksheet($name. ' Hours');
	if($file)
		fwrite($f, "\nWorksheet: $name Tours\n");
	$numCols = count($cols);

	//Set the column widths
	for($col = 0; $col < $numCols; $col++){
		$colName = $cols[$col];
		$colRef = $entries[$col];	
		$maxWidth = getTextWidth($colName);
		if($excel)
			$tourSheet->write_string(0, $col, $colName);
		if($file)
			fwrite($f, "Row: 0, Col: $col, $colRef: $colName, width:$maxWidth\t");

		for($logNum = 0; $logNum < $numSemesterTours; $logNum++){
			$text = $data[$logNum][$colRef];
			if($colRef == 'ambassador')
			{
				$text = $ambs["$text"]['name'];
			}
			$width = getTextWidth($text);
			if($width > $maxWidth){
				$maxWidth = $width;
			}
			/*
			 //formats do not work at the moment
			 if($col == 0){
				$tourSheet->set_row($logNum + 1, NULL, $formatOffset + ($tour % 2));
			}
			*/
		}
		if($file)
			fwrite($f, "\n");
		if($excel)
			$tourSheet->set_column($col, $col, $maxWidth * (2.0/3.0));
	}

	//Now we just add all the logs to the right page
	for($col = 0; $col < $numCols; $col++){
		$colRef = $entries[$col];
		for($logNum = 0; $logNum < $numSemesterTours; $logNum++){
			$text = $data[$logNum][$colRef];
			if($colRef == 'ambassador')
			{
				$text = $ambs["$text"]['name'];
			}
                        if(is_numeric($text)){
                        	if ($colRef == 'hours') {
                                   $tourSheet->write_number($logNum + 1, $col, floatval($text));
                                }
                                else {
				   $tourSheet->write_number($logNum + 1, $col, intval($text));
                                }
			} else {
				$tourSheet->write_string($logNum + 1, $col, $text);
			}
		}
	}
}

include('login.php');
$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

$lastPage = $_POST['lastPage']; // get the page that directed you to this page

if(($password == 1) || ($password == 2)){

	require_once('/home/coeamb/public_html/mysqlFunctions.php');

	require_once('/home/coeamb/database/dbInfo.php');

	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());

	mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
	
	$safeUserName = mysql_real_escape_string($userName);

	$ambList = 		mysqlQueryToListIndexBy('SELECT * FROM ambassadorInfo', 'name');
	$ambListID = 		mysqlQueryToListIndexBy('SELECT * FROM ambassadorInfo', 'id');
	$majList = 		mysqlQueryToListIndexBy('SELECT * FROM majorInfo', 'longName');
	$tourTimeList = mysqlQueryToList('SELECT * FROM scheduleInfo');
	$tourInfoList = mysqlQueryToList('SELECT * FROM tourInfo');
	$holidayList =	mysqlQueryToList('SELECT * FROM holidayInfo');
	$generalInfo =  mysqlQuerySingleRow('SELECT * FROM generalInfo');
	$emailInfo = mysqlQueryToList('SELECT * FROM emailAddresses');
	$adminUsers = mysqlQueryToList('SELECT * from coeambAdmins');

	$firstMajor = intval(mysqlQuerySingleRow('SELECT MIN(id) from majorInfo'));
	$safeFirstMajor = mysql_real_escape_string("$firstMajor");
	$firstAmb = intval(mysqlQuerySingleRow('SELECT MIN(id) from ambassadorInfo'));
	$safeFirstAmb = mysql_real_escape_string("$firstAmb");

	$now = strftime('%Y-%m-%d %H:%M:%S');
	$today = strftime('%Y-%m-%d');
	$nowTime = strftime('%H:%M:%S');
	$safeNow = mysql_real_escape_string($now);
	$safeToday = mysql_real_escape_string($today);
	$safeNowTime = mysql_real_escape_string($nowTime);

	date_default_timezone_set('America/New_York'); //so that dates will show up as knoxville time instead of time local to the user

	/*
	 * check through all the ambassadors to see if any actions needed to be taken on any of them
	 */
	foreach($ambList as $amb){
		$ambID = $amb['id'];
		$safeAmbID = mysql_real_escape_string("$ambID");
		//if we are updating ambassador information
		if(isset($_POST["confirmAmb$ambID"]) && ($_POST["confirmAmb$ambID"] == 'Confirm Changes')){
			$paramArray = array();
			foreach($amb as $param => $value){
				if(isset($_POST["amb_${param}${ambID}"])){
					if($param == 'major'){
						$majorName = ($_POST["amb_major$ambID"]);
						$res = $majList["$majorName"];
						$paramArray[] = "major='".$res['id']."'";
					} else {
						$paramArray[] = "$param='".mysql_real_escape_string($_POST["amb_${param}${ambID}"])."'";
					}
				} else {
					//$paramArray[] = "$param='$value'";
				}
			}
			$paramString = (implode(', ', $paramArray));
			mysqlQueryErrorCheck("UPDATE ambassadorInfo SET $paramString WHERE id='$safeAmbID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated amb $safeAmbID', result='$result'");
			break; //we made the changes here, no need to check other ambassadors
		}
		
		//if we are changing the password
		//TODO: notify user somehow
		if(isset($_POST["chPWAmb$ambID"]) && ($_POST["chPWAmb$ambID"] == 'Change Password')){
			$safeOldPW = sha1(mysql_real_escape_string($dbSalt.$_POST["amb_oldPW$ambID"].$dbSalt));
			if($_POST["amb_new1PW$ambID"] == $_POST["amb_new1PW$ambID"]){
				$safeNewPW = sha1(mysql_real_escape_string($dbSalt.$_POST["amb_new1PW$ambID"].$dbSalt));
				mysqlQueryErrorCheck("UPDATE ambassadorInfo SET password='$safeNewPW' WHERE id='$safeAmbID' AND password='$safeOldPW'");
				$result = mysql_real_escape_string(getMYSQLPWResult());
				mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated password $safeAmbID', result='$result'");
			} else {
				mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated password $safeAmbID', result='Passwords no match'");
			}
		}
		
		//if we are resetting the password
		if(isset($_POST["resetPWAmb$ambID"]) && ($_POST["resetPWAmb$ambID"] == 'Reset Password')){;
			$safePW = sha1(mysql_real_escape_string($dbSalt.$defaultPassword.$dbSalt));
			mysqlQueryErrorCheck("UPDATE ambassadorInfo SET password='$safePW' WHERE id='$safeAmbID'");
			$result = mysql_real_escape_string(getMYSQLPWResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Reset password $safeAmbID', result='$result'");
		}
		
		//if we are throwing away changes of ambassador information
		if(isset($_POST["resetAmb$ambID"]) && ($_POST["resetAmb$ambID"] == 'Reset Changes')){
			break; //no changes to be made, just reload the page with the default values
		}
		
		//if we are deleting a ambassador
		if(isset($_POST["deleteAmb$ambID"]) && ($_POST["deleteAmb$ambID"] == 'Delete Ambassador')){
			//TODO: add a popup (if possible) that asks if user is sure
			mysqlQueryErrorCheck("DELETE FROM ambassadorInfo WHERE id='$safeAmbID'", 'Error on MySQL query deleting ambassador: ');
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Deleted amb $safeAmbID', result='$result'");
			break; //we made the changes here, no need to check other ambassadors
		}
	}
	//if we are creating a new ambassador
	if(isset($_POST["createAmb"]) && ($_POST["createAmb"] == 'Add a New Ambassador')){
		$safePW = sha1(mysql_real_escape_string($dbSalt.$defaultPassword.$dbSalt));
		$query = "INSERT INTO ambassadorInfo SET major='$safeFirstMajor', password='$safePW'";
		mysqlQueryErrorCheck($query, 'Error on MySQL query adding new ambassador: ');
		$safeNewID = mysql_real_escape_string(mysql_insert_id());
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Created amb $safeNewID', result='$result'");
	}

	/*
	 * check through all the majors to see if any actions needed to be taken on any of them
	 */
	foreach($majList as $maj){
		$majID = $maj['id'];
		$safeMajID = mysql_real_escape_string("$majID");
		//if we are updating major information
		if(isset($_POST["confirmMaj$majID"]) && ($_POST["confirmMaj$majID"] == 'Confirm Changes')){
			$paramArray = array();
			foreach($maj as $param => $value){
				if(isset($_POST["maj_${param}${majID}"])){
					$paramArray[] = "$param='".mysql_real_escape_string($_POST["maj_${param}${majID}"])."'";
				} else {
					//$paramArray[] = "$param='$value'";
				}
			}
			$simCount = 0;
			$simArray = array();
			while(isset($_POST["maj_similarMajors${majID}sim${simCount}"])){
				$simName = $_POST["maj_similarMajors${majID}sim${simCount}"];
				$simCount++;
				if($simName == 'N/A') continue;
				$res = $majList["$simName"];
				$simArray[] = $res['id'];
			}
			$paramArray[] = "similarMajors='".implode(',', $simArray)."'";
			$paramString = (implode(', ', $paramArray));
			mysqlQueryErrorCheck("UPDATE majorInfo SET $paramString WHERE id='$safeMajID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated major $safeMajID', result='$result'");
			break; //we made the changes here, no need to check other majors
		}
		
		if(isset($_POST["addSimilarMajor$majID"]) && ($_POST["addSimilarMajor$majID"] == 'Add a major to this list')){
			if($maj['similarMajors'] == ''){
				$newSim = "$firstMajor";
			} else {
				$newSim = $maj['similarMajors'].",$firstMajor";
			}
			$safeNewSim = mysql_real_escape_string("$newSim");
			mysqlQueryErrorCheck("UPDATE majorInfo SET similarMajors='$safeNewSim' WHERE id=$safeMajID");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Add similar major $safeMajID', result='$result'");
			break;
		}
		
		//if we are throwing away changes of major information (actually we do nothing here, just reload)
		if(isset($_POST["resetMaj$majID"]) && ($_POST["resetMaj$majID"] == 'Reset Changes')){
			break; //no changes to be made, just reload the page with the default values
		}
		
		//if we are deleting a major
		if(isset($_POST["deleteMaj$majID"]) && ($_POST["deleteMaj$majID"] == 'Delete Major')){
			//TODO: add a popup (if possible) that asks if user is sure
			mysqlQueryErrorCheck("DELETE FROM majorInfo WHERE id='$safeMajID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Deleted major $safeMajID', result='$result'");
			break; //we made the changes here, no need to check other majors
		}
	}

	//if we are creating a new major
	if(isset($_POST["createMaj"]) && ($_POST["createMaj"] == 'Add a New Major')){
		$query = "INSERT INTO majorInfo SET longName='Blank Engineering'";
		mysqlQueryErrorCheck($query, 'Error on MySQL query adding new major: ');
		$safeNewID = mysql_real_escape_string(mysql_insert_id());
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Created major $safeNewID', result='$result'");
	}

	/*
	 * check through all the tours to see if any actions needed to be taken on any of them
	 */
	foreach($tourInfoList as $tour){
		$tourID = $tour['id'];
		$safeTourID = mysql_real_escape_string($tourID);
		//if we are updating tour information
		if(isset($_POST["confirmTour$tourID"]) && ($_POST["confirmTour$tourID"] == 'Confirm Changes')){
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
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated tour $safeTourID', result='$result'");
			break; //we made the changes here, no need to check other tours
		}
		
		//if we are throwing away changes of tour information (actually we do nothing here, just reload)
		if(isset($_POST["resetTour$tourID"]) && ($_POST["resetTour$tourID"] == 'Reset Changes')){
			break; //no changes to be made, just reload the page with the default values
		}
		
		//if we are deleting a tour
		if(isset($_POST["deleteTour$tourID"]) && ($_POST["deleteTour$tourID"] == 'Delete Tour')){
			//TODO: add a popup (if possible) that asks if user is sure
			mysqlQueryErrorCheck("DELETE FROM tourInfo WHERE id=$safeTourID");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Deleted tour $safeTourID', result='$result'");
			break; //we made the changes here, no need to check other tours
		}
	}

	//if we are creating a new tour
	if(isset($_POST["createTour"]) && ($_POST["createTour"] == 'Add a New Tour')){
		$query = "INSERT INTO tourInfo SET tourTime='$safeNow'";
		mysqlQueryErrorCheck($query, 'Error on MySQL query adding new tour: ');
		$safeNewID = mysql_real_escape_string(mysql_insert_id());
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Created tour $safeNewID', result='$result'");
	}

	/*
	 *  check through all the holidays to see if any actions needed to be taken on any of them
	 */

	if(isset($_POST["confirmEos"]) && ($_POST["confirmEos"] == 'Confirm Changes')){
		$safeFDOT = mysql_real_escape_string($_POST['sched_firstDayOfTours']);
                $safePDOT = mysql_real_escape_string($_POST['sched_tourPublicDay']);
		$safeLDOT = mysql_real_escape_string($_POST['sched_lastDayOfTours']);
		$safeTPDM = mysql_real_escape_string($_POST['sched_tourPageDownMsg']);
		$query = "UPDATE generalInfo SET firstDayOfTours='$safeFDOT', tourPublicday='$safePDOT',lastDayOfTours='$safeLDOT', tourPageDownMsg='$safeTPDM'";
		mysqlQueryErrorCheck($query, 'Error on MySQL query updating tour dates: ');
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated dates', result='$result'");
	}

	if(isset($_POST["resetEos"]) && ($_POST["resetEos"] == 'Reset Changes')){
		//no changes to be made, just reload the page with the default values
	}
	 
	foreach($holidayList as $hol){
		$holID = $hol['id'];
		$safeHolID = mysql_real_escape_string($holID);
		if(isset($_POST["confirmHol$holID"]) && ($_POST["confirmHol$holID"] == 'Confirm Changes')){
			$paramArray = array();
			foreach($hol as $param => $value){
				if(isset($_POST["sched_${param}${holID}"])){
					$paramArray[] = "$param='".mysql_real_escape_string($_POST["sched_${param}${holID}"])."'";
				} else {
					//$paramArray[] = "$param='$value'";
				}
			}
			$paramString = (implode(', ', $paramArray));
			mysqlQueryErrorCheck("UPDATE holidayInfo SET $paramString WHERE id='$safeHolID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated holiday $safeHolID', result='$result'");
			break; //we made the changes here, no need to check other tours
		}
		
		//if we are throwing away changes of holiday information (actually we do nothing here, just reload)
		if(isset($_POST["resetHol$holID"]) && ($_POST["resetHol$holID"] == 'Reset Changes')){
			break; //no changes to be made, just reload the page with the default values
		}
		
		//if we are deleting a holiday
		if(isset($_POST["deleteHol$holID"]) && ($_POST["deleteHol$holID"] == 'Delete Holiday')){
			//TODO: add a popup (if possible) that asks if user is sure
			mysqlQueryErrorCheck("DELETE FROM holidayInfo WHERE id='$safeHolID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Deleted holiday $safeHolID', result='$result'");
			break; //we made the changes here, no need to check other holidays
		}
	}
	 
	//if we are creating a new holiday
	if(isset($_POST["addHoliday"]) && ($_POST["addHoliday"] == 'Add a New Holiday')){
		$query = "INSERT INTO holidayInfo SET holidayDate='$safeToday'";
		mysqlQueryErrorCheck($query, 'Error on MySQL query adding new holiday: ');
		$safeNewID = mysql_real_escape_string(mysql_insert_id());
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Created holiday $safeNewID', result='$result'");
	}

	/*
	 *  check the tour times to see if any actions needed to be taken on any of them
	 */

	foreach($tourTimeList as $time){
		$timeID = $time['id'];
		$safeTimeID = mysql_real_escape_string("$timeID");
		if(isset($_POST["confirmTime$timeID"]) && ($_POST["confirmTime$timeID"] == 'Confirm Changes')){
			$paramArray = array();
			foreach($time as $param => $value){
				if(isset($_POST["time_${param}${timeID}"])){
					$paramArray[] = "$param='".mysql_real_escape_string($_POST["time_${param}${timeID}"])."'";
				} else {
					//$paramArray[] = "$param='$value'";
				}
			}
			
			$ambCount = 0;
			$ambArray = array();
			while(isset($_POST["time_ambsAvailable${timeID}amb${ambCount}"])){
				$ambName = $_POST["time_ambsAvailable${timeID}amb${ambCount}"];
				$ambCount++;
				if($ambName == 'N/A') continue;
				$res = $ambList["$ambName"];
				$ambArray[] = $res['id'];
			}
			$paramArray[] = "availableAmbassadors='".implode(',', $ambArray)."'";
			$paramString = (implode(', ', $paramArray));
			mysqlQueryErrorCheck("UPDATE scheduleInfo SET $paramString WHERE id='$safeTimeID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated tour time $safeTimeID', result='$result'");
			break; //we made the changes here, no need to check other time slots
		}
		
		//if we are throwing away changes of time slot information (actually we do nothing here, just reload)
		if(isset($_POST["resetTime$timeID"]) && ($_POST["resetTime$timeID"] == 'Reset Changes')){
			break; //no changes to be made, just reload the page with the default values
		}
		
		//if we are deleting a time slot
		if(isset($_POST["deleteTime$timeID"]) && ($_POST["deleteTime$timeID"] == 'Delete Tour Time')){
			//TODO: add a popup (if possible) that asks if user is sure
			mysqlQueryErrorCheck("DELETE FROM scheduleInfo WHERE id='$safeTimeID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Deleted tour time $safeTimeID', result='$result'");
			break; //we made the changes here, no need to check other time slots
		}
		
		if(isset($_POST["addAmbassador$timeID"]) && ($_POST["addAmbassador$timeID"] == 'Add an ambassador to this list')){
			if($time['availableAmbassadors'] == ''){
				$newAmbs = "$firstAmb";
			} else {
				$newAmbs = $time['availableAmbassadors'].",$firstAmb";
			}
			$safeNewAmbs = mysql_real_escape_string($newAmbs);
			mysqlQueryErrorCheck("UPDATE scheduleInfo SET availableAmbassadors='$safeNewAmbs' WHERE id='$safeTimeID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Added amb to tour time $safeTimeID', result='$result'");
			break;
		}
	}

	//if we are creating a new time slot
	if(isset($_POST["addTourTime"]) && ($_POST["addTourTime"] == 'Add a New Tour Time')){
		$query = "INSERT INTO scheduleInfo SET dayOfWeek='0', timeSlot='$safeNowTime', availableAmbassadors='', groupsAvailable=0";
		mysqlQueryErrorCheck($query, 'Error on MySQL query adding new tour time: ');
		$safeNewID = mysql_real_escape_string(mysql_insert_id());
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Created time slot $safeNewID', result='$result'");
	}

	/*
	 *  check the email settings to see if any actions needed to be taken on any of them
	 */

	//if we are updating email subject/bodies
	if(isset($_POST["confirmEmailSettings"]) && ($_POST["confirmEmailSettings"] == 'Confirm Changes')){
		$safeAmbEmailSubject = mysql_real_escape_string($_POST['email_ambEmailSubject']);
		$safeAmbEmailBody = mysql_real_escape_string($_POST['email_ambEmailBody']);
		$safeTourEmailSubject = mysql_real_escape_string($_POST['email_tourEmailSubject']);
		$safeTourEmailBody = mysql_real_escape_string($_POST['email_tourEmailBody']);
		
		$query = "UPDATE generalInfo SET ambEmailSubject='$safeAmbEmailSubject', ambEmailBody='$safeAmbEmailBody', tourEmailSubject='$safeTourEmailSubject', tourEmailBody='$safeTourEmailBody'";
		mysqlQueryErrorCheck($query);
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated emails', result='$result'");
	}

	//if we are discarding changes
	if(isset($_POST["resetEmailSettings"]) && ($_POST["resetEmailSettings"] == 'Reset Changes')){
		//Nothing to do here
	}

	foreach($emailInfo as $email){
		$emailID = $email['id'];
		$safeEmailID = mysql_real_escape_string("$emailID");
		if(isset($_POST["confirmEmail$emailID"]) && ($_POST["confirmEmail$emailID"] == 'Confirm Changes')){
			$safeEmail = $_POST["email_email$emailID"];
			$type = $_POST["email_type$emailID"];
			if($type == 'All emails'){
				$safeType = 0;
			} elseif($type == 'Only tour signup emails'){
				$safeType = 1;
			} elseif($type == 'Only ambassador action emails'){
				$safeType = 2;
			}
			$query = "UPDATE emailAddresses SET email='$safeEmail', type='$safeType' WHERE id='$safeEmailID'";
			mysqlQueryErrorCheck($query);
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated email $safeEmailID', result='$result'");
		}
		
		//if we are throwing away changes of email information (actually we do nothing here, just reload)
		if(isset($_POST["resetEmail$emailID"]) && ($_POST["resetEmail$emailID"] == 'Reset Changes')){
			break; //no changes to be made, just reload the page with the default values
		}
		
		//if we are deleting a email
		if(isset($_POST["deleteEmail$emailID"]) && ($_POST["deleteEmail$emailID"] == 'Delete Email Address')){
			//TODO: add a popup (if possible) that asks if user is sure
			mysqlQueryErrorCheck("DELETE FROM emailAddresses WHERE id='$safeEmailID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Deleted email $safeEmailID', result='$result'");
			break; //we made the changes here, no need to check other time slots
		}
	}

	//if we are adding a new email address
	if(isset($_POST["addEmail"]) && ($_POST["addEmail"] == 'Add a New Email Address')){
		$query = "INSERT INTO emailAddresses SET email='', type='0'";
		mysqlQueryErrorCheck($query);
		$safeNewID = mysql_real_escape_string(mysql_insert_id());
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Created email $safeNewID', result='$result'");
	}

	/*
	 * check admin account settings to see if we have to change anything here
	 */

	foreach($adminUsers as $admin){
		$adminID = $admin['id'];
		$safeAdminID = mysql_real_escape_string("$adminID");
		if(isset($_POST["confirmAdmin$adminID"]) && ($_POST["confirmAdmin$adminID"] == 'Confirm Changes')){
			$safeNetID = $_POST["admin_netID$adminID"];
			$type = $_POST["admin_type$adminID"];
			if($type == 'No special permissions'){
				$safeType = 0;
			} elseif($type == 'Tour Permissions'){
				$safeType = 1;
			} elseif($type == 'All Permissions'){
				$safeType = 2;
			}
			$query = "UPDATE coeambAdmins SET netID='$safeNetID', type='$safeType' WHERE id='$safeAdminID'";
			mysqlQueryErrorCheck($query);
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated admin $safeAdminID', result='$result'");
		}
		
		//if we are changing the password
		//TODO: notify user somehow
		if(isset($_POST["chPWAdmin$adminID"]) && ($_POST["chPWAdmin$adminID"] == 'Change Password')){
			$safeOldPW = sha1(mysql_real_escape_string($dbSalt.$_POST["admin_oldPW$adminID"].$dbSalt));
			if($_POST["admin_new1PW$adminID"] == $_POST["admin_new1PW$adminID"]){
				$safeNewPW = sha1(mysql_real_escape_string($dbSalt.$_POST["admin_new1PW$adminID"].$dbSalt));
				$res = mysqlQueryErrorCheck("UPDATE coeambAdmins SET password='$safeNewPW' WHERE id='$safeAdminID' AND password='$safeOldPW'");
				$result = mysql_real_escape_string(getMYSQLPWResult());
				mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated admin password $safeAdminID', result='$result'");
			} else {
				mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Updated admin password $safeAdminID', result='Passwords no match'");
			}
		}
		
		//if we are resetting the password
//echo "Hello World1";
		if(isset($_POST["resetPWAdmin$adminID"]) && ($_POST["resetPWAdmin$adminID"] == 'Reset Password')){
//echo "Hello World2";
			$safePW = sha1(mysql_real_escape_string($dbSalt.$defaultPassword.$dbSalt));
//echo "Hello World3";
			$res = mysqlQueryErrorCheck("UPDATE coeambAdmins SET password='$safePW' WHERE id='$safeAdminID'");
//echo "Hello World4";
			$result = mysql_real_escape_string(getMYSQLPWResult());
//echo "Hello World5";
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Reset admin password $safeAdminID', result='$result'");
//echo "Hello World6";
		}
//echo "Hello World7";
		
		//if we are throwing away changes of admin information (actually we do nothing here, just reload)
		if(isset($_POST["resetAdmin$adminID"]) && ($_POST["resetAdmin$adminID"] == 'Reset Changes')){
			break; //no changes to be made, just reload the page with the default values
		}
		
		//if we are deleting a admin account
		if(isset($_POST["deleteAdmin$adminID"]) && ($_POST["deleteAdmin$adminID"] == 'Delete Admin Account (Be Careful)')){
			//TODO: add a popup (if possible) that asks if user is sure
			mysqlQueryErrorCheck("DELETE FROM coeambAdmins WHERE id='$safeAdminID'");
			$result = mysql_real_escape_string(getMYSQLResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Deleted admin $safeAdminID', result='$result'");
			break; //we made the changes here, no need to check other time slots
		}
	}

/********************************************************************************
 * Adding a new Admin Account *
 *******************************************************************************/

	if(isset($_POST["addAdmin"]) && ($_POST["addAdmin"] == 'Add a New Admin Account')){
		$safePW = sha1(mysql_real_escape_string($dbSalt.$defaultPassword.$dbSalt));
		$query = "INSERT INTO coeambAdmins SET netID='netID', type='0', password='$safePW'";
		mysqlQueryErrorCheck($query, 'Error on MySQL query adding new email: ');
		$safeNewID = mysql_real_escape_string(mysql_insert_id());
		$result = mysql_real_escape_string(getMYSQLResult());
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Created admin $safeNewID', result='$result'");
	}
	
/********************************************************************************
 * Starting a new Semester *
 *******************************************************************************/
 
	if(isset($_POST["newSemester"]) && ($_POST["newSemester"] == 'Click Here To Start a New Semester')){
		//grab all the tours from this semester
		$currentTours = mysqlQueryToList('SELECT * FROM tourInfo ORDER BY id');
		foreach($currentTours as $tour){
			//for each tour, grab its old ID
			$oldTourID = $tour['id'];
			$safeOldTourID = mysql_real_escape_string($oldTourID);
			
			//remove id from the array because it will not be needed in new (old) table
			unset($tour['id']);
			//then take all the other parameters and glue them together
			$tourParams = array();
			foreach($tour as $key => $value){
				$safeValue = mysql_real_escape_string($value);
				$tourParams["$key"] = "$key='$safeValue'";
			}
			$paramString = implode(', ', $tourParams);
			
			//then we insert the tour info into the new (old) table and grab its new ID
                        //Make sure that the oldTourInfo and the tourInfo tables match!
			$query = "INSERT INTO oldTourInfo SET $paramString";
			mysqlQueryErrorCheck($query);
			$newID = mysql_insert_id();
			$safeNewID = mysql_real_escape_string($newID);
			//then we find all the logs that are tied to this tour
			$tourLogs = mysqlQueryToList("SELECT id FROM logbook WHERE tourID='$safeOldTourID'");
			foreach($tourLog as $log){
				$safeLogID = mysql_real_escape_string($log['id']);
				//and update their tourID to match the new id in the new (old) table
				$query = "UPDATE logbook SET tourID='$safeNewID' WHERE id='$safeLogID'";
				mysqlQueryErrorCheck($query);
			}
			
			//once we have copied the tour and updated links, it is safe to delete this tour
			mysqlQueryErrorCheck("DELETE FROM tourInfo WHERE id='$safeOldTourID'");
		}
		
		//next, we grab all the logbook entries from this semester
		$currentLogs = mysqlQueryToList('SELECT * FROM logbook ORDER BY id');
		foreach($currentLogs as $log){
			//for each log, grab its old ID
			$oldLogID = $log['id'];
			$safeOldLogID = mysql_real_escape_string($oldLogID);
			
			//remove id from the array because it will not be needed in new (old) table
			unset($log['id']);
			//then take all the other parameters and glue them together
			$logParams = array();
			foreach($log as $key => $value){
				$safeValue = mysql_real_escape_string($value);
				$logParams["$key"] = "$key='$safeValue'";
			}
			$paramString = implode(', ', $logParams);
			
			//then we insert the log info into the new (old) table and grab its new ID
			$query = "INSERT INTO oldLogbook SET $paramString";
			mysqlQueryErrorCheck($query);
			
			//once we have copied the log and updated links, it is safe to delete this log
			mysqlQueryErrorCheck("DELETE FROM logbook WHERE id='$safeOldLogID'");
		}
		
		//now we reset each table's auto_increment count just for the hell of it
		mysqlQueryErrorCheck('ALTER TABLE tourInfo AUTO_INCREMENT = 1');
		mysqlQueryErrorCheck('ALTER TABLE logbook AUTO_INCREMENT = 1');
		
		//TODO: also we are gonna want to generate new DB salts
		//TODO: also we might want to move old logs or just delete them all?
		mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='New Semester', result='Success'");
	}
	
/********************************************************************************
 * Downloading Tour History *
 *******************************************************************************/

	if(isset($_POST["downloadHistory"]) && ($_POST["downloadHistory"] == 'Download the tour history')){
		$excel = 1;
		$file = 0;
		
		if($file){
			$f = fopen('/home/coeamb/public_html/logs/excel.txt', 'w');
			fwrite($f, "Workbook: tourLog.xls\n");
		}
		
		if($excel){
			//send the headers to get ready to receive a spreadsheet file
			$datePart = date("_Y_m_d_H_i_s");
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=tourLog$datePart.xls" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
			
			//create a workbook to hold all this stuff
			$tourWorkBook = new Workbook('-');
			$formatOffset = $tourWorkBook->xf_index;
			$oddLineFormat = array('bg_color' => '#B8CCE4');
			$tourWorkBook->add_format($oddLineFormat);
			$evenLineFormat = array('bg_color' => '#95B3D7');
			$tourWorkBook->add_format($evenLineFormat);
		}
		
		//Get all the tour data, combine into one array and sort by semester
		$currentTours = mysqlQueryToList('SELECT * FROM tourInfo ORDER BY id');
		$oldTours = mysqlQueryToList('SELECT * FROM oldTourInfo ORDER BY id');
		$semesterList = array();
		$sortedSemesterList = array();
		foreach($currentTours as $tour){
			$tourTime = $tour['tourTime'];
			//$timeInfo = date_parse_from_format('Y-m-d G:i:s', $tourTime);
            $timeStamp9 = strtotime($tourTime);
            $timeInfo = getdate($timeStamp9);
			$timestamp = mktime($timeInfo['hour'], $timeInfo['minute'], $timeInfo['second'], 
				$timeInfo['month'], $timeInfo['day'], $timeInfo['year']);
			$timeString = $timeInfo['year'].' ';
			if(($timeInfo['month'] >= 1) && ($timeInfo['month'] <= 5)){
				$timeString .= 'Spring';
			} elseif(($timeInfo['month'] >= 6) && ($timeInfo['month'] <= 7)){
				$timeString .= 'Summer';
			} elseif(($timeInfo['month'] >= 8) && ($timeInfo['month'] <= 12)){
				$timeString .= 'Fall';
			}
			if(!array_key_exists("$timeString", $semesterList)){
				$semesterList["$timeString"] = array();
				$sortedSemesterList["$timeString"] = array();
			}
			$semesterList["$timeString"][] = $tour;
			if(!array_key_exists("$timestamp", $sortedSemesterList["$timeString"])){
				$sortedSemesterList["$timeString"]["$timeStamp"] = array();
			}
			$sortedSemesterList["$timeString"]["$timestamp"][] = $tour;
		}
		foreach($oldTours as $tour){
			$tourTime = $tour['tourTime'];
			//$timeInfo = date_parse_from_format('Y-m-d G:i:s', $tourTime);
            $timeStamp10 = strtotime($tourTime);
            $timeInfo = getdate($timeStamp10);
			$timestamp = mktime($timeInfo['hour'], $timeInfo['minute'], $timeInfo['second'], 
				$timeInfo['month'], $timeInfo['day'], $timeInfo['year']);
			$timeString = $timeInfo['year'].' ';
			if(($timeInfo['month'] >= 1) && ($timeInfo['month'] <= 5)){
				$timeString .= 'Spring';
			} elseif(($timeInfo['month'] >= 6) && ($timeInfo['month'] <= 7)){
				$timeString .= 'Summer';
			} elseif(($timeInfo['month'] >= 8) && ($timeInfo['month'] <= 12)){
				$timeString .= 'Fall';
			}
			if(!array_key_exists("$timeString", $semesterList)){
				$semesterList["$timeString"] = array();
				$sortedSemesterList["$timeString"] = array();
			}
			if(!array_key_exists("$timestamp", $sortedSemesterList["$timeString"])){
				$sortedSemesterList["$timeString"]["$timeStamp"] = array();
			}
			$semesterList["$timeString"][] = $tour;
			$sortedSemesterList["$timeString"]["$timestamp"][] = $tour;
		}
		$numSemesters = count($semesterList);
		
		//Then sort the semesters in descending order by their dates
		uksort($semesterList, 'compareSemesters');
		
		$cols = array('Tour ID', 'Date/Time', 'Majors', 'Student', 'Parent', 'People', 'School', 'Year', 'City', 'State', 'Email', 'Phone', 'Tour Status', 'Comments from Family', 'Comments from Ambassadors');
		$entries = array('id', 'tourTime', 'majorsOfInterest', 'studentName', 'parentName', 'numPeople', 'school', 'yearInSchool', 'city', 'state', 'email', 'phone', 'status', 'tourComments', 'ambComments');
		//then create a worksheet for each of the old semesters
		foreach($semesterList as $semester => $semesterTourList){
			$tourList = $sortedSemesterList["$semester"];
			ksort($tourList, SORT_NUMERIC);
			$sortedTourList = array();
			$keys = array_keys($tourList);
			$numKeys = count($keys);
			for($key = 0; $key < $numKeys; $key++){
				$sortedTourList = array_merge($sortedTourList, $tourList[$keys[$key]]);
			}
			createTourLogSheet($semester.' Sorted', $sortedTourList, $tourWorkBook, 0);
			createTourLogSheet($semester, $semesterTourList, $tourWorkBook, 1);				
		}
		
		/* Worksheet layout
		 * 	1 group with all tour info just sorted by tour date
		 * (All these groups below will just have links to the tour entry above)
		 * 	1 group showing tour data per week
		 * 	1 group showing tour data per day
		 * 	1 group showing tour data per week and day
		 * (Then for each tour, print any logbook entries if available)
		 */
		if($excel)
			$tourWorkBook->close();
		if($file)
			fclose($f);
	}


/********************************************************************************
 * Downloading Ambassador Logs and Hours *
 *******************************************************************************/
 
	if(isset($_POST["downloadAmbassadorHours"]) && ($_POST["downloadAmbassadorHours"] == 'Download Ambassador Hours')){
		require_once('excel/Worksheet.php');
		require_once('excel/Workbook.php');
		
		$excel = 1;
		$file = 0;
		
		if($file){
			$f = fopen('/home/coeamb/public_html/logs/excel.txt', 'w');
			fwrite($f, "Workbook: tourLog.xls\n");
		}
		
		if($excel){
			//send the headers to get ready to receive a spreadsheet file
			$datePart = date("_Y_m_d_H_i_s");
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=ambassadorHours$datePart.xls" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
			
			//create a workbook to hold all this stuff
			$tourWorkBook = new Workbook('-');
			$formatOffset = $tourWorkBook->xf_index;
			$oddLineFormat = array('bg_color' => '#B8CCE4');
			$tourWorkBook->add_format($oddLineFormat);
			$evenLineFormat = array('bg_color' => '#95B3D7');
			$tourWorkBook->add_format($evenLineFormat);
		}
		
		//Get all the logs, combine into one array and sort by semester
		$currentLogs = mysqlQueryToList('SELECT * FROM logbook ORDER BY eventDate');
		$oldLogs = mysqlQueryToList('SELECT * FROM oldLogbook ORDER BY eventDate');
		$semesterList = array();
		foreach($currentLogs as $log){
			$tourTime = $log['logTime'];
			//$timeInfo = date_parse_from_format('Y-m-d G:i:s', $tourTime);
            $timeStamp7 = strtotime($tourTime);
            $timeInfo = getdate($timeStamp7);
			$timeString = $timeInfo['year'].' ';
			if(($timeInfo['month'] >= 1) && ($timeInfo['month'] <= 5)){
				$timeString .= 'Spring';
			} elseif(($timeInfo['month'] >= 6) && ($timeInfo['month'] <= 7)){
				$timeString .= 'Summer';
			} elseif(($timeInfo['month'] >= 8) && ($timeInfo['month'] <= 12)){
				$timeString .= 'Fall';
			}
			if(!array_key_exists("$timeString", $semesterList)){
				$semesterList["$timeString"] = array();
			}
			$semesterList["$timeString"][] = $log;
		}
		foreach($oldLogs as $log){
			$tourTime = $log['logTime'];
			//$timeInfo = date_parse_from_format('Y-m-d G:i:s', $tourTime);
            $timeStamp8 = strtotime($tourTime);
            $timeInfo = getdate($timeStamp8);
			$timeString = $timeInfo['year'].' ';
			if(($timeInfo['month'] >= 1) && ($timeInfo['month'] <= 5)){
				$timeString .= 'Spring';
			} elseif(($timeInfo['month'] >= 6) && ($timeInfo['month'] <= 7)){
				$timeString .= 'Summer';
			} elseif(($timeInfo['month'] >= 8) && ($timeInfo['month'] <= 12)){
				$timeString .= 'Fall';
			}
			if(!array_key_exists("$timeString", $semesterList)){
				$semesterList["$timeString"] = array();
			}
			$semesterList["$timeString"][] = $log;
		}
		$numSemesters = count($semesterList);
		
		//Then sort the semesters in descending order by their dates
		uksort($semesterList, 'compareSemesters');
		
		//then create a worksheet for each of the old semesters
		foreach($semesterList as $semester => $semesterHourList){
			createHourLogSheet($semester, $semesterHourList, $tourWorkBook, $ambListID);	
		}
		
		/* Worksheet layout
		 * 	1 group with all tour info just sorted by tour date
		 * (All these groups below will just have links to the tour entry above)
		 * 	1 group showing tour data per week
		 * 	1 group showing tour data per day
		 * 	1 group showing tour data per week and day
		 * (Then for each tour, print any logbook entries if available)
		 */
		if($excel)
			$tourWorkBook->close();
		if($file)
			fclose($f);

	}

	mysql_close($db);
}

/********************************************************************************
 *******************************************************************************/

if(isset($_POST["submit"]) && ($_POST["submit"] == 'logout')){
	require_once('/home/coeamb/public_html/mysqlFunctions.php');
	require_once('/home/coeamb/database/dbInfo.php');
	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
	mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
	logout($userName); //included from login.php
}

if(isset($_POST["submit"]) && ($_POST["submit"] == 'My login info isn\'t working')){
	require_once('/home/coeamb/public_html/mysqlFunctions.php');
	require_once('/home/coeamb/database/dbInfo.php');
	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
	mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
	logout($userName, TRUE); //just clears the cookies, lets the user try to log in again
}

//here we redirect back to the manager page
header("Location: http://www.engr.utk.edu/ambassador/$lastPage");
//just to make sure that nothing after this gets executed somehow
exit();

?>
