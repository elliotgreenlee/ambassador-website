<?php
/*
Filename: calendar.php
*/

$fileName = 'calendar.php';
$submitFile = 'calendarSubmit.php';
$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'); //actually need this one, but automate this

include('login.php');

$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

include 'webpages.php';

printHeader('Calendar');
printNavigation((($password == 1) || ($password == 2)), false, ($password == 2)); 
	
?>

<section class="two-thirds column">
<h1>Tour Schedule</h1>

<?
if($password=="1"){

	require_once('/home/coeamb/database/dbInfo.php');

	require_once('/home/coeamb/public_html/mysqlFunctions.php');

	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword)
		or die('Could not connect: '.mysql_error());

	mysql_select_db($dbName)
		or die('Could not select database: '.mysql_error());
	
	$safeUserName = mysql_real_escape_string($userName);
	$ambInfo = 		mysqlQuerySingleRow("SELECT * FROM ambassadorInfo WHERE netID='$safeUserName' LIMIT 1");
	//index the list of majors by id so we can easily access them later
	$majList = 		mysqlQueryToListIndexBy('SELECT * FROM majorInfo', 'id');
	$availableTours = mysqlQueryToList('SELECT * FROM tourInfo WHERE status=0 AND tourTime > NOW() ORDER BY tourTime');
	$safeAmbID = mysql_real_escape_string($ambInfo['id']);
	$myUpcomingTours = mysqlQueryToList("SELECT * FROM tourInfo WHERE ambassadorAssigned='$safeAmbID' AND tourTime > NOW() ORDER BY tourTime");
	$numMyUpcomingTours = count($myUpcomingTours);
	$myOldTours = mysqlQueryToList("SELECT * FROM tourInfo WHERE ambassadorAssigned='$safeAmbID' AND tourTime < NOW() ORDER BY tourTime");
	$numMyOldTours = count($myOldTours);
	//index the list of schedules by id so we can easily access them later
	$scheduleList = mysqlQueryToListIndexBy('SELECT * FROM scheduleInfo', 'id');

	//here we figure out which tours the ambassador can staff, but also get the ones that they can't
	$myTours = array();
	$notMyTours = array();
	foreach($availableTours as $tour){
		//can probably do some more filtering so that we are not stressing DB as much
		if(intval($tour['status']) > 0) continue;
		//don't have to pull these out of the DB now, but might want to error check
		$scheduleID = $tour['scheduleID'];
		$timeRes = $scheduleList["$scheduleID"];
		if($timeRes['availableAmbassadors'] != ''){
			$ambs = explode(',', $timeRes['availableAmbassadors']);
			foreach($ambs as $amb){
				if($ambInfo['id'] == $amb){
					$myTours[] = "id='".mysql_real_escape_string($tour['id'])."'";
					$notMyTours[] = "id!='".mysql_real_escape_string($tour['id'])."'";
				}
			}
		}
	}
	$myTourString = implode(' OR ', $myTours);
	$notMyTourString = implode(' AND ', $notMyTours);
	//so we will display these major tours
	if($myTourString != ''){
		$myTourList = mysqlQueryToList("SELECT * FROM tourInfo WHERE $myTourString AND tourTime > NOW() AND status='0' ORDER BY tourTime");
	} else {
		$myTourList = array();
	}
	$numMyTours = count($myTourList);
	//but if the ambassador needs/wants to sign up for one of these, it's possible
	if($notMyTourString != ''){
		$notMyTourList = mysqlQueryToList("SELECT * FROM tourInfo WHERE $notMyTourString AND tourTime > NOW() AND status='0' ORDER BY tourTime");
	} else {
		$notMyTourList = array();
	}
	$numNotMyTours = count($notMyTourList);
?>
<form method="post" action="<?=$submitFile?>">
<?

//TODO: Look at the new tours and make changes accordingly
?>

<h2><img name="yourimg" id="yourimg" src="http://www.engr.utk.edu/ambassador/images/Orb-Blue-Minus-24.png" height="12" width="12" title="Click here to minimize this section" onclick="javascript:toggleSection('your','yourimg',0,0);" />Your Upcoming Tours - Tours that you have signed up for.</h2>
<div name="your" id="your" style="display:inline">
<br />
<?php
if($numMyUpcomingTours == 0){
	echo "<h4>No Tours Here!</h4>\n";
}
foreach($myUpcomingTours as $tour){
	$tourID = $tour['id'];
	
	echo "<ul><li>Tour Time: ";
	echo "<input type=\"text\" name=\"tour_tourTime$tourID\" value=\"".$tour['tourTime']."\" size=\"16\" /> (YYYY-MM-DD HH:MM:SS, 24 Hour Time)</li>\n";

	//TODO Let the user change these here
	echo "\t\t\t\t\t<ul><li>Majors Of Interest:\n\t\t\t\t\t\t<ul>\n";
	if($tour['majorsOfInterest'] != NULL){
		$MOI = explode(',', $tour['majorsOfInterest']);
		$majCount = count($MOI);
		for($maj = 0; $maj < $majCount; $maj++){
			$majID = $MOI[$maj];
			$res = $majList["$majID"];
			echo "<li>".$res['longName']."</li>\n";
		}
	} else {
		echo "<li>None Selected.</li>\n";
	}
	echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t</li>\n";
	echo "\t\t\t\t\t<li>Student Name: <input type=\"text\" name=\"tour_studentName$tourID\" value=\"".$tour['studentName']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Parent Name: <input type=\"text\" name=\"tour_parentName$tourID\" value=\"".$tour['parentName']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Number of People: <input type=\"text\" name=\"tour_numPeople$tourID\" value=\"".$tour['numPeople']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>School Student attends: <input type=\"text\" name=\"tour_school$tourID\" value=\"".$tour['school']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Year in School: <input type=\"text\" name=\"tour_yearInSchool$tourID\" value=\"".$tour['yearInSchool']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>City Student is From: <input type=\"text\" name=\"tour_city$tourID\" value=\"".$tour['city']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>State: <input type=\"text\" name=\"tour_state$tourID\" value=\"".$tour['state']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Email address: <input type=\"text\" name=\"tour_email$tourID\" value=\"".$tour['email']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Phone Number: <input type=\"text\" name=\"tour_phone$tourID\" value=\"".$tour['phone']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Special Accommodations: <input type=\"text\" name=\"tour_needs$tourID\" value=\"".$tour['special']."\" size=\"30\" /></li>\n";
	echo "<li>Time slot id: <input type=\"text\" name=\"tour_scheduleID$tourID\" value=\"".$tour['scheduleID']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Confirmation Code: <input type=\"text\" name=\"tour_confirmationCode$tourID\" value=\"".$tour['confirmationCode']."\" size=\"30\" /></li>\n";
	echo "<li>Tour ID: $tourID</li>\n";

	//TODO: Add comments
	echo "\t\t\t\t\t\t<li><input type=\"submit\" name=\"denyTour$tourID\" value=\"Deny Tour\" />&nbsp;<input type=\"submit\" name=\"saveTour$tourID\" value=\"Save Changes\" /></li>\n";
	echo "\n\t\t\t\t\t</li>\n";
	echo "\t\t\t</ul></ul><br />\n";
}
?>
</div>

<h2><img name="availimg" id="availimg" src="http://www.engr.utk.edu/ambassador/images/Orb-Blue-Minus-24.png" height="12" width="12" title="Click here to minimize this section" onclick="javascript:toggleSection('available','availimg',0,0);" />Available Tours - Tours that nobody has signed up for and that are during your regular hours.</h2>
<div name="available" id="available" style="display:inline">
<br />
<?php
if($numMyTours == 0){
	echo "<h4>No Tours Here!</h4>\n";
}
foreach($myTourList as $tour){
	$tourID = $tour['id'];
	
	echo "<ul><li>Tour Time: ";
	echo "<input type=\"text\" name=\"tour_tourTime$tourID\" value=\"".$tour['tourTime']."\" size=\"16\" /> (YYYY-MM-DD HH:MM:SS, 24 Hour Time)</li>\n";

	//TODO Let the user change these here
	echo "\t\t\t\t\t<ul><li>Majors Of Interest:\n\t\t\t\t\t\t<ul>\n";
	if($tour['majorsOfInterest'] != NULL){
		$MOI = explode(',', $tour['majorsOfInterest']);
		$majCount = count($MOI);
		for($maj = 0; $maj < $majCount; $maj++){
			$majID = $MOI[$maj];
			$res = $majList["$majID"];
			echo "<li>".$res['longName']."</li>\n";
		}
	} else {
		echo "<li>None Selected.</li>\n";
	}
	echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t</li>\n";
	echo "\t\t\t\t\t<li>Student Name: <input type=\"text\" name=\"tour_studentName$tourID\" value=\"".$tour['studentName']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Parent Name: <input type=\"text\" name=\"tour_parentName$tourID\" value=\"".$tour['parentName']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Number of People: <input type=\"text\" name=\"tour_numPeople$tourID\" value=\"".$tour['numPeople']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>School Student attends: <input type=\"text\" name=\"tour_school$tourID\" value=\"".$tour['school']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Year in School: <input type=\"text\" name=\"tour_yearInSchool$tourID\" value=\"".$tour['yearInSchool']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>City Student is From: <input type=\"text\" name=\"tour_city$tourID\" value=\"".$tour['city']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>State: <input type=\"text\" name=\"tour_state$tourID\" value=\"".$tour['state']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Email address: <input type=\"text\" name=\"tour_email$tourID\" value=\"".$tour['email']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Phone Number: <input type=\"text\" name=\"tour_phone$tourID\" value=\"".$tour['phone']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Special Accommodations: <input type=\"text\" name=\"tour_needs$tourID\" value=\"".$tour['special']."\" size=\"30\" /></li>\n";
	echo "<li>Time slot id: <input type=\"text\" name=\"tour_scheduleID$tourID\" value=\"".$tour['scheduleID']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Confirmation Code: <input type=\"text\" name=\"tour_confirmationCode$tourID\" value=\"".$tour['confirmationCode']."\" size=\"30\" /></li>\n";
	echo "<li>Tour ID: $tourID</li>\n";

	//TODO: Add comments
	echo "\t\t\t\t\t\t<li><input type=\"submit\" name=\"acceptTour$tourID\" value=\"Accept Tour\" />&nbsp;<input type=\"submit\" name=\"saveTour$tourID\" value=\"Save Changes\" /></li>\n";
	echo "\n\t\t\t\t\t</li>\n";
	echo "\t\t\t</ul></ul><br />\n";
}
?>
</div>
<h2><img name="extraimg" id="extraimg" src="http://www.engr.utk.edu/ambassador/images/Orb-Blue-Minus-24.png" height="12" width="12" title="Click here to minimize this section" onclick="javascript:toggleSection('extra','extraimg',0,0);" />
Extra Available Tours - Tours that nobody has signed up for, but that are <b>NOT</b> during your default hours. You can still sign up for these if you need more hours, but ask someone before you do so.</h2>
<div name="extra" id="extra" style="display:inline">
<br />
<?php
if($numNotMyTours == 0){
	echo "<h4>No Tours Here!</h4>\n";
}
foreach($notMyTourList as $tour){
	$tourID = $tour['id'];
	
	echo "<ul><li>Tour Time: ";
	echo "<input type=\"text\" name=\"tour_tourTime$tourID\" value=\"".$tour['tourTime']."\" size=\"16\" /> (YYYY-MM-DD HH:MM:SS, 24 Hour Time)</li>\n";

	//TODO Let the user change these here
	echo "\t\t\t\t\t<ul><li>Majors Of Interest:\n\t\t\t\t\t\t<ul>\n";
	if($tour['majorsOfInterest'] != NULL){
		$MOI = explode(',', $tour['majorsOfInterest']);
		$majCount = count($MOI);
		for($maj = 0; $maj < $majCount; $maj++){
			$majID = $MOI[$maj];
			$res = $majList["$majID"];
			echo "<li>".$res['longName']."</li>\n";
		}
	} else {
		echo "<li>None Selected.</li>\n";
	}
	echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t</li>\n";
	echo "\t\t\t\t\t<li>Student Name: <input type=\"text\" name=\"tour_studentName$tourID\" value=\"".$tour['studentName']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Parent Name: <input type=\"text\" name=\"tour_parentName$tourID\" value=\"".$tour['parentName']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Number of People: <input type=\"text\" name=\"tour_numPeople$tourID\" value=\"".$tour['numPeople']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>School Student attends: <input type=\"text\" name=\"tour_school$tourID\" value=\"".$tour['school']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Year in School: <input type=\"text\" name=\"tour_yearInSchool$tourID\" value=\"".$tour['yearInSchool']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>City Student is From: <input type=\"text\" name=\"tour_city$tourID\" value=\"".$tour['city']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>State: <input type=\"text\" name=\"tour_state$tourID\" value=\"".$tour['state']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Email address: <input type=\"text\" name=\"tour_email$tourID\" value=\"".$tour['email']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Phone Number: <input type=\"text\" name=\"tour_phone$tourID\" value=\"".$tour['phone']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Special Accommodations: <input type=\"text\" name=\"tour_needs$tourID\" value=\"".$tour['special']."\" size=\"30\" /></li>\n";
	echo "<li>Time slot id: <input type=\"text\" name=\"tour_scheduleID$tourID\" value=\"".$tour['scheduleID']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Confirmation Code: <input type=\"text\" name=\"tour_confirmationCode$tourID\" value=\"".$tour['confirmationCode']."\" size=\"30\" /></li>\n";
	echo "<li>Tour ID: $tourID</li>\n";

	//TODO: Add comments
	echo "\t\t\t\t\t\t<li><input type=\"submit\" name=\"acceptTour$tourID\" value=\"Accept Tour\" />&nbsp;<input type=\"submit\" name=\"saveTour$tourID\" value=\"Save Changes\" /></li>\n";
	echo "\n\t\t\t\t\t</li>\n";
	echo "\t\t\t</ul></ul><br />\n";
}
/*	$now = time();
	$futureList = $tourManager->findToursWhere('tourTime', TourInfo::$IS_GREATER_THAN, $now);
	$numFutureTours = count($futureList);

function cmpTour($a, $b){
	if($a->tourTime == $b->tourTime) return 0;
	return ($a->tourTime < $b->tourTime) ? -1 : 1;
}

if (!usort($futureList, "cmpTour")){
	echo "<div>***NOTE: Tours may not be in the appropriate order***</div>\n";
}

if($numFutureTours > 0){echo "<ol>\n";}
foreach($futureList as $tourNum){
	$tour = $tourManager->getTourByIndex($tourNum);
	if($tour->status == -2){
		continue;
	}
	if($tour->status >= -1){
		//if this ambassador has selected this tour or if this ambassador is the current one selected
		if((($tour->ambassadorsAssigned[0] == $ambIndex) && ($tour->status == -1))
		|| ($tour->ambassadorsAssigned[$tour->status] == $ambIndex)){
			echo "\t<li><h4>".date("l, F jS Y: g:i A - ",$tour->tourTime).date("g:i A",$tour->tourTime+60*60)."<h4></li>\n\t<ul>\n";
			echo "\t\t<li><b>Parent Name:</b>&nbsp;".$tour->parentName."</li>\n";
			echo "\t\t<li><b>Student Name:</b>&nbsp;".$tour->studentName."</li>\n";
			echo "\t\t<li><b>Number of Visitors:</b>&nbsp;".$tour->numPeople."</li>\n";
			echo "\t\t<li><b>High School:</b>&nbsp;".$tour->school."</li>\n";
			echo "\t\t<li><b>Year in School:</b>&nbsp;".$tour->yearInSchool."</li>\n";
			echo "\t\t<li><b>From:</b>&nbsp;".$tour->city.", ".$tour->state."</li>\n";
			if($tour->phone != '()-'){
				echo "\t\t<li><b>Phone:</b>&nbsp;".$tour->phone."</li>\n";
			}
			echo "\t\t<li><b>Email:</b>&nbsp;".$tour->email."</li>\n";
			echo "\t\t<li><b>Major They Selected:</b>&nbsp;".$majorManager->getMajorByIndex($tour->majorSelected)->longName."</li>\n";
			$sum = 0;
			$majArray = str_split($tour->majorsOfInterest);
			foreach($majArray as $index){
				$sum += $index;
			}
			if($sum > 1){
				echo "\t\t<li><b>Other Majors They are Interested In:</b>&nbsp;";
				for($maj = 0, $first = ''; $maj < $majCount; $maj++){
					if(($tour->majorsOfInterest[$maj] == '1') && ($tour->majorSelected != $maj)){
						echo $first.$majorManager->getMajorByIndex($maj)->printName;
						$first = ', ';
					}
				}
				echo "\t\t</li>\n";
			}
			
			if($status != -1){
				//TODO: Get rid of these radio buttons
				echo "\t\t<li>If you are accepting this tour, please use this form to send your confirmation email. ";
				echo "Edit the message however you see fit and it will be sent to the correct email address when you click 'Accept Tour'.\n";
				echo "\t\t<br><textarea rows=\"14\" cols=\"70\" wrap=\"soft\" name=\"email$tourNum\">\n";
				//TODO: Fix this name finding business up in here
				$nameParent = explode(' ', $tour->parentName);
				$nameStudent = explode(' ', $tour->studentName);
				echo "Hi Mr./Ms. ".$nameParent[1].",\n\n";
				echo "My name is ".$ambObjLog->name." and I am a College of Engineering Ambassador at the University of Tennessee. ";
				echo "I just wanted to confirm our tour of the engineering campus at UT this ".date("l, F j \a\\t g:i A", $tour->tourTime). ". ";
				echo "Please meet me in 101 Perkins Hall which is located on Middle Drive. ";
				echo "You can park at the University Center Garage and walk up. ";
				echo "If you have any questions please feel free to email me. ";
				echo "Looking forward to meeting you,\n\nSincerely,\n\n";
				echo $ambObjLog->name."\nCollege of Engineering Ambassadors\n".$majObjLog->longName."\n";
				echo "</textarea>\n";
				echo "<input type=\"submit\" value=\"Accept Tour\" name=\"acceptTour$tourNum\"><input type=\"submit\" value=\"Deny Tour\" name=\"denyTour$tourNum\">\n";
			} else {
				echo "\t\t<li>You have accepted this tour.</li>\n";
				echo "<input type=\"submit\" value=\"Cancel Tour\" name=\"cancelTour$tourNum\">\n";
			}
			echo "\t</ul>\n<br />\n";
		}
	}
}
if($numFutureTours > 0){
	echo "</ol>\n";
} else {
	echo "<div><h4>You have no upcoming tours.</h4></div>\n";
}*/
?>
</div>

<h2><img name="pastimg" id="pastimg" src="http://www.engr.utk.edu/ambassador/images/Orb-Blue-Minus-24.png" height="12" width="12" title="Click here to minimize this section" onclick="javascript:toggleSection('past','pastimg',0,0);" />Your Past Tours - Tours that you have already given. Provide feedback and remember to send a followup email.</h2>
<div name="past" id="past" style="display:inline">
<br />
<?php
if($numMyOldTours == 0){
	echo "<h4>No Tours Here!</h4>\n";
}
foreach($myOldTours as $tour){
	$tourID = $tour['id'];
	
	echo "<ul><li>Tour Time: ";
	echo "<input type=\"text\" name=\"tour_tourTime$tourID\" value=\"".$tour['tourTime']."\" size=\"16\" /> (YYYY-MM-DD HH:MM:SS, 24 Hour Time)</li>\n";

	//TODO Let the user change these here
	echo "\t\t\t\t\t<ul><li>Majors Of Interest:\n\t\t\t\t\t\t<ul>\n";
	if($tour['majorsOfInterest'] != NULL){
		$MOI = explode(',', $tour['majorsOfInterest']);
		$majCount = count($MOI);
		for($maj = 0; $maj < $majCount; $maj++){
			$majID = $MOI[$maj];
			$res = $majList["$majID"];
			echo "<li>".$res['longName']."</li>\n";
		}
	} else {
		echo "<li>None Selected.</li>\n";
	}
	echo "\t\t\t\t\t\t</ul>\n\t\t\t\t\t</li>\n";
	echo "\t\t\t\t\t<li>Student Name: <input type=\"text\" name=\"tour_studentName$tourID\" value=\"".$tour['studentName']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Parent Name: <input type=\"text\" name=\"tour_parentName$tourID\" value=\"".$tour['parentName']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Number of People: <input type=\"text\" name=\"tour_numPeople$tourID\" value=\"".$tour['numPeople']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>School Student attends: <input type=\"text\" name=\"tour_school$tourID\" value=\"".$tour['school']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Year in School: <input type=\"text\" name=\"tour_yearInSchool$tourID\" value=\"".$tour['yearInSchool']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>City Student is From: <input type=\"text\" name=\"tour_city$tourID\" value=\"".$tour['city']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>State: <input type=\"text\" name=\"tour_state$tourID\" value=\"".$tour['state']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Email address: <input type=\"text\" name=\"tour_email$tourID\" value=\"".$tour['email']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Phone Number: <input type=\"text\" name=\"tour_phone$tourID\" value=\"".$tour['phone']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Special Accommodations: <input type=\"text\" name=\"tour_needs$tourID\" value=\"".$tour['special']."\" size=\"30\" /></li>\n";
	echo "<li>Time slot id: <input type=\"text\" name=\"tour_scheduleID$tourID\" value=\"".$tour['scheduleID']."\" size=\"30\" /></li>\n";
	echo "\t\t\t\t\t<li>Confirmation Code: <input type=\"text\" name=\"tour_confirmationCode$tourID\" value=\"".$tour['confirmationCode']."\" size=\"30\" /></li>\n";
	echo "<li>Tour ID: $tourID</li>\n";
	echo "\t\t\t\t\t<li>Status: ";
	switch($tour['status']){
		case 0:
			$status = "Waiting on Confirmation";
			break;
		case 1:
			$status = "Accepted by Ambassador";
			break;
		case 2;
			$status = "Denied by all Ambassadors, needs Reassignment";
			break;
		case 3:
			$status = "Followup Email sent after Tour";
			break;
		case 4;
			$status = "Family did not show up for Tour";
			break;
		case 5;
			$status = "Tour was deleted";
			break;
		default:
			$status = "ERROR, Contact Josh";
	}
	echo "$status</li>\n";
	/*echo "\t\t\t\t\t<li>Ambassador Assigned to This Tour:";
	$ambTour = $tour['ambassadorAssigned'];
	if($ambTour > 0){
		$res = mysqlQuerySingleRow("SELECT name FROM ambassadorInfo WHERE id='$ambTour'", "Error: ");
		echo $res['name']."</li>\n";
	} else {
		echo "None</li>\n";
	}*/

	//TODO: Add comments
	echo "\t\t\t\t\t\t<li><input type=\"submit\" name=\"noshowTour$tourID\" value=\"Family did not show up for tour\" />&nbsp;<input type=\"submit\" name=\"saveTour$tourID\" value=\"Save Changes\" /></li>\n";
	echo "\n\t\t\t\t\t</li>\n";
	echo "\t\t\t</ul></ul><br />\n";
}
?>
</div>

<?php

/*$pastList = $tourManager->findToursWhere('tourTime', TourInfo::$IS_LESS_THAN, $now);
$numPastTours = count($pastList);

if (!usort($pastList, "cmpTour")){
	echo "<div>***NOTE: Tours may not be in the appropriate order***</div>\n";
}

if($numPastTours > 0){echo "<ol>\n";}
foreach($pastList as $tourNum){
	$tour = $tourManager->getTourByIndex($tourNum);
	if($tour->status == -2){
		continue;
	}
	if(($ambIndex == $tour->ambassadorsAssigned[0]) && ($tour->status <= -1)
	|| ($ambIndex == $tour->ambassadorsAssigned[$tour->status])){
		echo "\t<li><h4>".date("l, F jS Y: g:i A - ",$tour->tourTime).date("g:i A",$tour->tourTime+60*60)."<h4></li>\n\t<ul>\n";
		echo "\t\t<li><b>Parent Name:</b>&nbsp;".$tour->parentName."</li>\n";
		echo "\t\t<li><b>Student Name:</b>&nbsp;".$tour->studentName."</li>\n";
		echo "\t\t<li><b>Number of Visitors:</b>&nbsp;".$tour->numPeople."</li>\n";
		echo "\t\t<li><b>High School:</b>&nbsp;".$tour->school."</li>\n";
		echo "\t\t<li><b>Year in School:</b>&nbsp;".$tour->yearInSchool."</li>\n";
		echo "\t\t<li><b>From:</b>&nbsp;".$tour->city.", ".$tour->state."</li>\n";
		if($tour->phone != '()-'){
			echo "\t\t<li><b>Phone:</b>&nbsp;".$tour->phone."</li>\n";
		}
		echo "\t\t<li><b>Email:</b>&nbsp;".$tour->email."</li>\n";
		echo "\t\t<li><b>Major They Selected:</b>&nbsp;".$majorManager->getMajorByIndex($tour->majorSelected)->longName."</li>\n";
		$sum = 0;
		$majArray = str_split($tour->majorsOfInterest);
		foreach($majArray as $index){
			$sum += $index;
		}
		if($sum > 1){
			echo "\t\t<li><b>Other Majors They are Interested In:</b>&nbsp;";
			for($maj = 0, $first = ''; $maj < $majCount; $maj++){
				if(($tour->majorsOfInterest[$maj] == '1') && ($tour->majorSelected != $maj)){
					echo $first.$majorManager->getMajorByIndex($maj)->printName;
					$first = ', ';
				}
			}
			echo "\t\t</li>\n";
		}
		if($tour->status == -1){
			echo "\t\t<li>Please use this form to send your follow-up email. Edit the message however you see fit and it will be sent to the correct email address when you click 'send'.\n";
			echo "\t\t<br><textarea rows=\"14\" cols=\"70\" wrap=\"soft\" name=\"email$tourNum\">\n";
			$nameParent = explode(' ',$tour->parentName);
			$nameStudent = explode(' ',$tour->studentName);
			echo "Hi Mr./Ms. ".$nameParent[1].",\n\n";
			echo "It was great to meet you and your family. ";
			echo "I hope you enjoyed the College of Engineering Tour. ";
			echo "Please let me know if you or your family have any questions while you are going through your decision making process. ";
			echo "I would be glad to answer any questions you have about UT or the College of Engineering. ";
			echo "Good luck with your college applications and have a great summer! \n\n";
			echo $ambObjLog->name."\nCollege of Engineering Ambassadors\n".$majObjLog->longName."\n";
			echo "</textarea>\n\t\t<br>\n\n";
			echo "<input type=\"submit\" value=\"Send Followup Email\" name=\"sendFollowup$tourNum\"><input type=\"submit\" value=\"Family Did not Show up for Tour\" name=\"noShowTour$tourNum\">\n";
		} elseif($tour->status == -3) {
			echo "\t\t<li>Thank you for sending a follow-up email</li>\n";
		} elseif($tour->status == -4) {
			echo "\t\t<li>Tour was a no-show; no follow-up email necessary.</li>\n";
		}
		echo "\t\t</ul>\n\t<br />\n";
	}
}
if($numPastTours > 0){
	echo "</ol>\n";
} else {
	echo "<div><h4>You have no past tours.</h4></div>\n";
}*/
?>

<br />
<input type="hidden" name="lastPage" value="<?=$fileName?>">
<table align="center">
<tr>
	<td align="left">
		<input type="submit" name="submit" value="logout">
	</td>
</tr>
</table>
</form>
<?
	mysql_close($db);
} elseif($password=="2"){ //if this is the master tour page

	require_once('/home/coeamb/database/dbInfo.php');

	require_once('/home/coeamb/public_html/mysqlFunctions.php');

	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());

	mysql_select_db($dbName) or die('Could not select database: '.mysql_error());

	$tourTimeList = mysqlQueryToList('SELECT * FROM scheduleInfo ORDER BY dayOfWeek');
	$majList = mysqlQueryToListIndexBy('SELECT * FROM majorInfo', 'id');
	$ambList = mysqlQueryToListIndexBy('SELECT * FROM ambassadorInfo', 'id');
?>

<form method="post" action="<?=$submitFile?>">
<input type="hidden" name="lastPage" value="<?=$fileName?>">

<? //print out the table for ambassador schedules ?>
<h2>Tour Availability By Ambassador</h2>
<br />
<table width="100%" border="1" cellpadding="0" cellspacing="1">
  <tr width="100%">
<? for($day = 0; $day < 7; $day++) echo "    <td align=\"center\" width=\"14%\"><b>$days[$day]</b></td>\n"; ?>
  </tr>
<?
$tourTimes = array(array());
foreach($tourTimeList as $tour) $tourTimes[$tour['dayOfWeek']][] = $tour;
$maxEntries = 0;
foreach($tourTimes as $dayOfTours){
	if(count($dayOfTours) > $maxEntries){
		$maxEntries = count($dayOfTours);
	}
}
for($entry = 0; $entry < $maxEntries; $entry++){
	echo "<tr>";
	for($day = 0; $day < 7; $day++){
		if(isset($tourTimes[$day][$entry])){
			$time = $tourTimes[$day][$entry];
            $tsTimeStamp3 = strtotime($time['timeSlot']);
            $dtStr3 = date("c", $tsTimeStamp3);
            $dt = new DateTime($dtStr3);
			//$dt = DateTime::createFromFormat('H:i:s', $time['timeSlot']);
			echo "<td><h6>".$dt->format('g:i A')."<br />\n";
			if($time['availableAmbassadors'] != ''){
				$ambList = explode(',', $time['availableAmbassadors']);
				foreach($ambList as $amb){
					$ambID = ($amb['id']);
					$ambInfo = $ambList["$ambID"];
					$majID = ($ambInfo['major']);
					$majInfo = $majList["$majID"];
					echo $ambInfo['name'].' - '.$majInfo['longName']."<br />\n";
				}
			} else {
				echo "No Ambassadors\n";
			}
			echo "</h6></td>\n";
		} else {
			echo "<td>&nbsp;</td>\n";
		}
	}
	echo "</tr>\n";
}
?>
</table>
<br />

<h2>Tour History</h2>
<br />
Lisa, what if anything would you want here? -Elliot
<br />

<br />
<table align="center">
<tr>
	<td>
	<td align="right">
		<input type="submit" name="submit" value="logout">
	</td>
</tr>
</table>
<br />
</form>
<?
} else {
	//Either the user gave bad login info, or is viewing the page for the first time.
	printLogIn($fileName, $submitFile);
}
?>
</section>
<?

printFooter(array('Calendar' => 'calendar.php'));

?>


