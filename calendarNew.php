<?php
/*
Filename: calendar.php
*/

$fileName = 'calendar.php';
$submitFile = 'calendarSubmit.php';
$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'); //actually need this one, but automate this

/*if(isset($_COOKIE['majorstest']))
	{
	$myMajors = $_COOKIE['majorstest'];
	}
else
	{
	$myMajors = array_fill(0, $majCount, '0');
	setCookie('majorstest', $myMajors, time()+24*60*60, '/', 'www.engr.utk.edu');
	}

if(isset($_COOKIE['visibility']))
	{
	$myVisibility = $_COOKIE['visibility'];
	}
else
	{
	setCookie('visibility', '1111', time()+24*60*60, '/', 'www.engr.utk.edu');
	$myVisibility = '1111';
	}

if(isset($_COOKIE['people']))
	{
	$myPeople = $_COOKIE['people'];
	}
else
	{
	$myPeople = array_fill(0, $ambCount, '0');
	setCookie('people', $myPeople, time()+24*60*60, '/', 'www.engr.utk.edu');
	}*/

include('login.php');

$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];
	
?>

<? //<doc>Note: This is the default UT background. Don't touch anything here.</doc> ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- Replace "Blue One Column" with the title of this particular page. To improve search engine indexing, the page title should be the same here as the Header 1 <h1> in the body -->
  <title>UT College of Engineering Ambassadors--Calendar</title>
  <meta name="description" content="The University of Tennessee College of Engineering Ambassadors provide tours of the college campus, help recruit prospective students, increase involvment of the student body and stimulate community interest in the field of engineering." />
  <meta name="keywords" content="university, tennessee, campus tours, tours, ambassadors, outreach, knoxville, university of tennessee, university of tennessee knoxville, tennessee schools, tennessee colleges, tennessee higher education, higher education" />
  <meta name="author" content="University of Tennessee | Engineering Communications Office" />
  <link rel="shortcut icon" href="http://www.utk.edu/new_images/favicon.ico" />
<!-- The following lines call the primary (global.css) and secondary style sheets -->
  <link href="http://www.utk.edu/cs/templates/css/global.css" rel="stylesheet" type="text/css" />
  <link href="blue.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" type="text/JavaScript">
  <!--
    function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
    }

    function SetCookie(cookieName, cookieData, expireDate) {
        document.cookie = cookieName + "=" + escape(cookieData) + "; expires=" + expireDate.toGMTString()+"; path=/; domain=www.engr.utk.edu";
    }

    function setCharAt(str,index,chr) {
	if(index > str.length-1) return str;
	return str.substr(0,index) + chr + str.substr(index+1);
    }

    function setVisibility(number, vis){
      var cookieData = readCookie('visibility');
      cookieData = setCharAt(cookieData, number, vis);
      var date = new Date();
      date.setTime(date.getTime()+24*60*60*1000);
      SetCookie('visibility', cookieData, date);
      location.reload();
    }

    function toggleSection(section, image, number, cookies){
      var asection = document.getElementById(section);
      var aimage = document.getElementById(image);
      if((asection.style.display == 'inline') || (asection.style.display == 'block')){
	 asection.style.display = 'none';
	 aimage.src = 'http://www.engr.utk.edu/ambassador/images/Orb-Blue-Plus-24.png';
	 aimage.title = 'Click here to restore this section';
        if(cookies==1){setVisibility(number, 0);}
      } else {
	 asection.style.display = 'inline';
	 aimage.src = 'http://www.engr.utk.edu/ambassador/images/Orb-Blue-Minus-24.png';
	 aimage.title = 'Click here to minimize this section';
	 if(cookies==1){setVisibility(number, 1);}
      }
    }

    function setMajor(number){
      var cookieData = readCookie('majorstest');
      if(cookieData.charAt(number)=='0'){
        cookieData = setCharAt(cookieData, number, '1');
      } else {
        cookieData = setCharAt(cookieData, number, '0');
      }
      var date = new Date();
      date.setTime(date.getTime()+24*60*60*1000);
      SetCookie('majorstest', cookieData, date);
      location.reload();
    }

    function setPeople(number){
      var cookieData = readCookie('people');
      if(cookieData.charAt(number)=='0'){
        cookieData = setCharAt(cookieData, number, '1');
      } else {
        cookieData = setCharAt(cookieData, number, '0');
      }
      var date = new Date();
      date.setTime(date.getTime()+24*60*60*1000);
      SetCookie('people', cookieData, date);
      location.reload();
    }
  //-->
</script>

</head>
<body>
<!-- BEGIN HEADER | EDIT ONLY THE HEADER-RIGHT SECTION -->
<p class="skip"><a href="#maincontent">Skip
to Main Content</a></p>
<div id="header-inner">
<div id="header-left"> <a href="http://www.utk.edu"><img src="http://www.utk.edu/cs/templates/images/ut-wordmark.gif" alt="The University of Tennessee" border="0" height="90" width="240" /></a>
<p class="logo"><a href="http://www.utk.edu">The
University of Tennessee</a></p>
</div>
<div id="header-right"><!-- To make the header image a link to your homepage, replace the # with your URL. Also, remember to change the alt tag on the header image! Doing so improves usibility and accessibility. -->
<p><a href="http://www.engr.utk.edu/ambassador/index.php"><img src="images/ambs_header.jpg" alt="UT College of Engineering Ambassadors" border="0" /></a></p>
<div id="header-menu">
<h2>Frequently Used Tools:</h2>
<!-- Does not display in standard browsers -->
<ul>
  <li><a href="http://webmail.utk.edu">Webmail</a>&nbsp;&nbsp;|&nbsp;</li>
  <li><a href="http://tmail.utk.edu">Tmail</a>&nbsp;&nbsp;|&nbsp;</li>
  <li><a href="http://online.utk.edu">Online@UT</a>&nbsp;&nbsp;|&nbsp;</li>
  <li><a href="http://www.utk.edu/alpha">A-Z Index</a></li>
</ul>
</div>
<div id="header-search">
<h2>Search The University of Tennessee:</h2>
<!-- Does not display in standard browsers -->
<form id="utk_seek" name="utk_seek" method="post" accept-charset="iso-8859-1" onsubmit="checkTerms();return false;" action="http://www.tennessee.edu/masthead/query.php"> <label for="utk_seek" class="hide-search">search: </label>
<!-- Does not display in standard browsers --> <input name="qt" size="3" class="searchbox" value="Search" onfocus="this.value=''" title="search" type="text" />
  <select name="qtype" class="searchtext" title="search type">
  <option value="utk">Campus</option>
  <option value="ldap">People</option>
  <option value="system">System</option>
  </select>
  <input name="go" src="http://www.utk.edu/cs/templates/images/go.gif" alt="go" type="image" /> </form>
</div>
<br clear="all" />
<!-- Clears the header-menu and header-search columns --> </div>
<br clear="all" />
<!-- Clears the header-left and header-right columns --> </div>
<!-- END HEADER --><!-- BEGIN DEPARTMENT -->
<div id="department-outer">
<div id="department-inner">
<div id="department-left"><!-- This is where you add the name of YOUR department, college, or unit and link to YOUR home page. Change the # to your URL. Remember, to display properly this MUST be a linked H2 -->
<h2><a href="http://www.engr.utk.edu">College of Engineering &raquo;</a></h2>
</div>
<div id="department-right"><!-- department-right is where you can create "breadcrumb navigation." For example, if the page lists the Ceramics faculty, department-right might include: School of Art > Ceramics > Faculty. This gives users a better sense of how deeply they have dug themselves into a site. You might also use department-right to feature important or frequently-used links -->
<p><a href="http://www.engr.utk.edu/ambassador/index.php">Engineering Ambassadors</a></p>
</div>
<br clear="all" />
<!-- Clears the department-left and department-right columns -->
</div>
</div>
<!-- END DEPARTMENT -->
<!-- BEGIN CONTENT AREA -->
<div id="content-outer">
<div id="content-inner"><!-- BEGIN LEFT MENU -->
<div id="left-menu">

<?php 
include("menu.php");
$redirect = $fileName;
if($password){include("logbookmenu.php");} 
?>

</div>
<!-- END LEFT MENU --><!-- BEGIN MAIN CONTENT AREA -->
<div id="main-content"> <a name="maincontent" id="maincontent"></a><!-- A named anchor included to improve web accessibility. DO NOT EDIT --><!-- Page Heading -->
<? //<doc>Note: This is where the actual page starts.</doc> ?>

<h1>Tour Schedule</h1>

<?
if($password=="1"){

	require_once('/home/coeamb/database/dbInfo.php');

	require_once('/home/coeamb/public_html/mysqlFunctions.php');

	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword)
		or die('Could not connect: '.mysql_error());

	mysql_select_db($dbName)
		or die('Could not select database: '.mysql_error());

	if(isset($_GET['timeSlot'])){
		$selectedTimeSlot = $_GET['timeSlot'];
	} else {
		$selectedTimeSlot = -1;
	}
	$safeSelTimeSlot = mysql_real_escape_string($selectedTimeSlot);

	$timeSlots = mysqlQueryToList('SELECT * FROM scheduleInfo ORDER BY dayOfWeek,timeSlot');
	$safeUserName = mysql_real_escape_string($userName);
	$ambInfo = 		mysqlQuerySingleRow("SELECT * FROM ambassadorInfo WHERE netID='$safeUserName' LIMIT 1");

	$safeAmbID = mysql_real_escape_string($ambInfo['id']);
	$myUpcomingTours = mysqlQueryToList("SELECT * FROM tourInfo WHERE ambassadorAssigned='$safeAmbID' AND tourTime > NOW() ORDER BY tourTime");
	$numMyUpcomingTours = count($myUpcomingTours);
	$myOldTours = mysqlQueryToList("SELECT * FROM tourInfo WHERE ambassadorAssigned='$safeAmbID' AND tourTime < NOW() ORDER BY tourTime");
	$numMyOldTours = count($myOldTours);

?>
<form method="post" action="<?=$submitFile?>">
<?

?>
<?php
	echo '<table width="100%" border="1">'."\n".'<tr align="center"><th colspan="5">Select a time to view avialable tours or <a href="calendarNew.php">view all of your tours</a></th></tr><tr align="center"><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th></tr>'."\n";
	$dayTimes = array_fill(0, 7, NULL);
	foreach($timeSlots as $timeSlot){
		$dayTimes[intval($timeSlot['dayOfWeek'])][] = $timeSlot;
	}
	$maxTimes = 0;
	foreach($dayTimes as $dt){
		$times = count($dt);
		if($times > $maxTimes) $maxTimes = $times;
	}
	for($time = 1; $time <= $maxTimes; $time++){
		echo '<tr align="center">';
		for($day = 1; $day < 6; $day++){
			if(($dayTimes[$day] == NULL) || (count($dayTimes[$day]) < $time)){
				echo "<td>&nbsp</td>\n";
			} else {
				if($dayTimes[$day][$time-1]['availableAmbassadors'] == NULL){
					$bg = '';
				} else {
					$avAmbs = explode(',', $dayTimes[$day][$time-1]['availableAmbassadors']);
					$found = 0;
					foreach($avAmbs as $amb){
						if($safeAmbID == $amb){
							$found = 1;
							break;
						}
					}
					if($found == 1){
						if($selectedTimeSlot == $dayTimes[$day][$time-1]['id']){
							$bg = 'bgcolor="#00FF00"';
						} else {
							$bg = 'bgcolor="#00FFFF"';
						}
					} else {
						if($selectedTimeSlot == $dayTimes[$day][$time-1]['id']){
							$bg = 'bgcolor="#FFFF00"';
						} else {
							$bg = '';
						}
					}
				}
				$tI = strptime($dayTimes[$day][$time-1]['timeSlot'], '%T');
				$tStr = strftime('%l:%M %p', mktime($tI['tm_hour'], $tI['tm_min'], $tI['tm_sec']));
				$ts = $dayTimes[$day][$time-1]['id'];
				echo "<td $bg><a href=\"calendarNew.php?timeSlot=$ts\">$tStr</a></td>";
			}
		}
		echo "</tr>\n";
	}
	echo "</table>\n";

	if($selectedTimeSlot == -1){
		//no timeslot selected, print amb's tours (future and past)
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
			echo "\t\t\t\t\t<li>Number of People on Tour: <input type=\"text\" name=\"tour_numPeople$tourID\" value=\"".$tour['numPeople']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>School Student attends: <input type=\"text\" name=\"tour_school$tourID\" value=\"".$tour['school']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Year in School: <input type=\"text\" name=\"tour_yearInSchool$tourID\" value=\"".$tour['yearInSchool']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>City Student is From: <input type=\"text\" name=\"tour_city$tourID\" value=\"".$tour['city']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>State: <input type=\"text\" name=\"tour_state$tourID\" value=\"".$tour['state']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Email address: <input type=\"text\" name=\"tour_email$tourID\" value=\"".$tour['email']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Phone Number: <input type=\"text\" name=\"tour_phone$tourID\" value=\"".$tour['phone']."\" size=\"30\" /></li>\n";
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
			echo "\t\t\t\t\t<li>Number of People on Tour: <input type=\"text\" name=\"tour_numPeople$tourID\" value=\"".$tour['numPeople']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>School Student attends: <input type=\"text\" name=\"tour_school$tourID\" value=\"".$tour['school']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Year in School: <input type=\"text\" name=\"tour_yearInSchool$tourID\" value=\"".$tour['yearInSchool']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>City Student is From: <input type=\"text\" name=\"tour_city$tourID\" value=\"".$tour['city']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>State: <input type=\"text\" name=\"tour_state$tourID\" value=\"".$tour['state']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Email address: <input type=\"text\" name=\"tour_email$tourID\" value=\"".$tour['email']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Phone Number: <input type=\"text\" name=\"tour_phone$tourID\" value=\"".$tour['phone']."\" size=\"30\" /></li>\n";
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
	/* TODO: Add this back in when we get the multiple ambassador business going
	echo "\t\t\t\t\t<li>Ambassador Assigned to This Tour:";
	$ambTour = $tour['ambassadorAssigned'];
	if($ambTour > 0){
		$res = mysqlQuerySingleRow("SELECT name FROM ambassadorInfo WHERE id='$ambTour'", "Error: ");
		echo $res['name']."</li>\n";
	} else {
		echo "None</li>\n";
	}*/

			//TODO: Let an ambassador fill in comments here, have them all linked in a separate table instead
			// of being stored in the actual tour object
			echo "\t\t\t\t\t\t<li><input type=\"submit\" name=\"noshowTour$tourID\" value=\"Family did not show up for tour\" />&nbsp;<input type=\"submit\" name=\"saveTour$tourID\" value=\"Save Changes\" /></li>\n";
			echo "\n\t\t\t\t\t</li>\n";
			echo "\t\t\t</ul></ul><br />\n";
		}
?>
</div>
<?php
	} else {
		//TODO: Right now this just shows a timeslot's upcoming and past tours, but when we get multiple ambassadors
		//per tour, it should show open, filled and then past tours
		
		//get all timeslots
		$scheduleList = mysqlQueryToListIndexBy('SELECT * FROM scheduleInfo', 'id');
		$safeTimeslotDOW = mysql_real_escape_string($scheduleList[$selectedTimeSlot]['dayOfWeek']+1);
		$safeTimeslotTOD = mysql_real_escape_string($scheduleList[$selectedTimeSlot]['timeSlot']);

		$thisTSUCTours = mysqlQueryToList("SELECT * FROM tourInfo WHERE DAYOFWEEK(tourTime)='$safeTimeslotDOW' and TIME(tourTime)='$safeTimeslotTOD' and tourTime > NOW() ORDER BY tourTime");
		$thisPSPastTours = mysqlQueryToList("SELECT * FROM tourInfo WHERE DAYOFWEEK(tourTime)='$safeTimeslotDOW' and TIME(tourTime)='$safeTimeslotTOD' and tourTime < NOW() ORDER BY tourTime");

		$numThisTSUCTours = count($thisTSUCTours);
		$numThisPSPastTours = count($thisPSPastTours);

?>
<h2><img name="availimg" id="availimg" src="http://www.engr.utk.edu/ambassador/images/Orb-Blue-Minus-24.png" height="12" width="12" title="Click here to minimize this section" onclick="javascript:toggleSection('available','availimg',0,0);" />Available Tours - Upcoming tours for the selected timeslot.</h2>
<div name="available" id="available" style="display:inline">
<br />
<?php
		if($numThisTSUCTours == 0){
			echo "<h4>No Tours Here!</h4>\n";
		}
		foreach($thisTSUCTours as $tour){
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
			echo "\t\t\t\t\t<li>Number of People on Tour: <input type=\"text\" name=\"tour_numPeople$tourID\" value=\"".$tour['numPeople']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>School Student attends: <input type=\"text\" name=\"tour_school$tourID\" value=\"".$tour['school']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Year in School: <input type=\"text\" name=\"tour_yearInSchool$tourID\" value=\"".$tour['yearInSchool']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>City Student is From: <input type=\"text\" name=\"tour_city$tourID\" value=\"".$tour['city']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>State: <input type=\"text\" name=\"tour_state$tourID\" value=\"".$tour['state']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Email address: <input type=\"text\" name=\"tour_email$tourID\" value=\"".$tour['email']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Phone Number: <input type=\"text\" name=\"tour_phone$tourID\" value=\"".$tour['phone']."\" size=\"30\" /></li>\n";
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
Past Tours - Tours that have been given at this timeslot.</h2>
<div name="extra" id="extra" style="display:inline">
<br />
<?php
		if($numThisPSPastTours == 0){
			echo "<h4>No Tours Here!</h4>\n";
		}
		foreach($thisPSPastTours as $tour){
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
			echo "\t\t\t\t\t<li>Number of People on Tour: <input type=\"text\" name=\"tour_numPeople$tourID\" value=\"".$tour['numPeople']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>School Student attends: <input type=\"text\" name=\"tour_school$tourID\" value=\"".$tour['school']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Year in School: <input type=\"text\" name=\"tour_yearInSchool$tourID\" value=\"".$tour['yearInSchool']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>City Student is From: <input type=\"text\" name=\"tour_city$tourID\" value=\"".$tour['city']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>State: <input type=\"text\" name=\"tour_state$tourID\" value=\"".$tour['state']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Email address: <input type=\"text\" name=\"tour_email$tourID\" value=\"".$tour['email']."\" size=\"30\" /></li>\n";
			echo "\t\t\t\t\t<li>Phone Number: <input type=\"text\" name=\"tour_phone$tourID\" value=\"".$tour['phone']."\" size=\"30\" /></li>\n";
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
<?php
	}
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
			$dt = DateTime::createFromFormat('H:i:s', $time['timeSlot']);
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
I'll do this eventually.
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

//<doc>Note: Either the user gave bad login info, or is viewing the page for the first time.</doc>
?>
<center>
<b>
This page is password protected<br />
please enter username and password<br />
</b>
<form method="post" action="<?=$submitFile?>">
<input type="hidden" name="lastPage" value="<?=$fileName?>">
<table>
	<tr>
		<td colspan="3">
		Username:
		</td>
	</tr>
	<tr>
		<td colspan="3">
		<input type="text" size="24" maxlength="30" name="username">
		</td>
	</tr>
	<tr>
		<td colspan="3">
		Password:
		</td>
	</tr>
	<tr>
		<td  colspan="3">
		<input type="password" size="24" maxlength="30" name="password">
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" value="submit" name="submit">
			</form>
		</td>
		<td>&nbsp;</td>
		<td>
			<input type="submit" name="submit" value="My password isn't working">
		</td>
		</tr>
</table>
</center>
<?
}
//<doc>Note: Don't touch anything after here again.</doc>
?>

<!-- Clears the left-menu and main-content columns --> </div>
</div>
<!-- END CONTENT AREA -->
<!-- BEGIN FOOTER | DO NOT EDIT THIS AREA -->
<div id="footer-orange-outer">
<div id="footer-orange-inner"> &nbsp; </div>
</div>
<div id="footer-grey-outer">
<div id="footer-grey-inner">
<p><a href="http://www.utk.edu">The University of
Tennessee</a> &nbsp;&middot;&nbsp; Knoxville, TN
37996 &nbsp;&middot;&nbsp; (865) 974-1000
&nbsp;&middot;&nbsp;<a href="http://www.utk.edu/contact/">Contact
UT</a></p>
</div>
</div>
<!-- END FOOTER -->
</div>
</body>
</html>
