<?php
/*
Elliot Greenlee
Edited February 3, 2016
Manager.php
Supervisor can edit and manage ambassador, tour, holiday, etc., information.
*/ 
?>

<?php 
$fileName = 'manager.php';
$submitFile = 'managerSubmit.php';

include('login.php');

$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

include 'webpages.php';

printHeader('UT College of Engineering Ambassadors--Virtual Tours');
printNavigation((($password == 1) || ($password == 2)), false, ($password == 2));
?>

<section class="two-thirds column">
<h1>Manager</h1>

<? 
if($password=="1") //user pages
{
?>
<form name="masterManagerPage" method="post" action="<?= $fileName?>">
<input type="hidden" name="lastPage" value="<?=$fileName?>">
<center>
<b>This page is protected. You do not have permission to access this page.</b>
<br />
<table>
    <tr>
		<td><input type="submit" value="logout" name="submit"></td>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit" value="My password isn't working"></td>
	</tr>
</table>
</form>
</center>
<?
}
elseif($password=="2") //master page
{

require_once('/home/coeamb/database/dbInfo.php');
require_once('/home/coeamb/public_html/mysqlFunctions.php');

$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword)
	or die('Could not connect: '.mysql_error());

mysql_select_db($dbName)
	or die('Could not select database: '.mysql_error());

$ambList = mysqlQueryToListIndexBy('SELECT * FROM ambassadorInfo ORDER BY id', 'id');
$majList = mysqlQueryToListIndexBy('SELECT * FROM majorInfo ORDER BY id', 'id');
$tourTimeList = mysqlQueryToList('SELECT * FROM scheduleInfo ORDER BY id');
$tourInfoList = mysqlQueryToList('SELECT * FROM tourInfo ORDER BY tourTime');
$holidayList = mysqlQueryToList('SELECT * FROM holidayInfo ORDER BY id');
$generalInfo = mysqlQuerySingleRow('SELECT * FROM generalInfo');
$emailInfo = mysqlQueryToList('SELECT * FROM emailAddresses ORDER BY id');
$adminUsers = mysqlQueryToList('SELECT * from coeambAdmins ORDER BY id');

$ambCount = count($ambList);
$majCount = count($majList);
$tourTimeCount = count($tourTimeList);
$tourInfoCount = count($tourInfoList);
$holidayCount = count($holidayList);

//check all the ambassador visibility cookies
//whole ambassador list visibility
if(isset($_COOKIE['ambListVis'])){
	$ambListVisibility = $_COOKIE['ambListVis'];
} else {
	$cookieString = '0';
	setCookie('ambListVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$ambListVisibility = $cookieString;
}

//each ambassador visibility
if(isset($_COOKIE['ambVis'])){
	$ambVisibility = $_COOKIE['ambVis'];
} else {
	$cookieString = str_repeat('0', $ambCount);
	setCookie('ambVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$ambVisibility = $cookieString;
}

//each ambassador's schedule visibility
if(isset($_COOKIE['ambSchedVis'])){
	$ambSchedVisibility = $_COOKIE['ambSchedVis'];
} else {
	$cookieString = str_repeat('1', $ambCount);
	setCookie('ambSchedVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$ambSchedVisibility = $cookieString;
}

//check all the major visibility cookies
//whole major list visibility
if(isset($_COOKIE['majListVis'])){
	$majListVisibility = $_COOKIE['majListVis'];
} else {
	$cookieString = '0';
	setCookie('majListVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$majListVisibility = $cookieString;
}

//each major visibility
if(isset($_COOKIE['majVis'])){
	$majVisibility = $_COOKIE['majVis'];
} else {
	$cookieString = str_repeat('0', $majCount);
	setCookie('majVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$majVisibility = $cookieString;
}

//each major schedule visibility
if(isset($_COOKIE['majorSchedVis'])){
	$majorSchedVisibility = $_COOKIE['majorSchedVis'];
} else {
	$cookieString = str_repeat('1', $majCount);
	setCookie('majorSchedVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$majorSchedVisibility = $cookieString;
}

//each major's ambassadors visibility
if(isset($_COOKIE['majorAmbsVis'])){
	$majorAmbsVisibility = $_COOKIE['majorAmbsVis'];
} else {
	$cookieString = str_repeat('1', $majCount);
	setCookie('majorAmbsVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$majorAmbsVisibility = $cookieString;
}

//each major's similar major visibility
if(isset($_COOKIE['majorSimsVis'])){
	$majorSimsVisibility = $_COOKIE['majorSimsVis'];
} else {
	$cookieString = str_repeat('1', $majCount);
	setCookie('majorSimsVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$majorSimsVisibility = $cookieString;
}

//check all the tour visibility cookies
//whole tour list visibility
if(isset($_COOKIE['tourListVis'])){
	$tourListVisibility = $_COOKIE['tourListVis'];
} else {
	$cookieString = '1';
	setCookie('tourListVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$tourListVisibility = $cookieString;
}

//check all the tour visibility cookies
//whole tour list visibility
if(isset($_COOKIE['emailVis'])){
	$emailVisibility = $_COOKIE['emailVis'];
} else {
	$cookieString = '1';
	setCookie('emailVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$emailVisibility = $cookieString;
}

//each tour visibility
if(isset($_COOKIE['tourVis'])){
	$tourVisibility = $_COOKIE['tourVis'];
	$tourVisibility = str_pad($tourVisibility, $tourCount, '1', STR_PAD_RIGHT);
} else {
	$cookieString = str_repeat('0', $tourCount);
	setCookie('tourVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$tourVisibility = $cookieString;
}

//schedule (holidays) visibility
if(isset($_COOKIE['schedVis'])){
	$schedVisibility = $_COOKIE['schedVis'];
} else {
	$cookieString = '1';
	setCookie('schedVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$schedVisibility = $cookieString;
}

//schedule (holidays) visibility
if(isset($_COOKIE['tourTimeVis'])){
	$tourTimeVisibility = $_COOKIE['tourTimeVis'];
} else {
	$cookieString = '0';
	setCookie('tourTimeVis', $cookieString, time()+24*60*60, '/', 'www.engr.utk.edu');
	$tourTimeVisibility = $cookieString;
}
?>  

    <br />
	<h2>Manage Ambassadors</h2>
<?

echo "\t<img name=\"ambListImg\" id=\"ambListImg\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($ambListVisibility[0]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('ambList', 'ambListImg', 'ambListVis', 0, 1);\" />Collapse/Expand Ambassador List\n";
   echo "\t<div name=\"ambList\" id=\"ambList\" style=\"".(($ambListVisibility[0]=='1')?'display:inline':'display:none')."\">\n\t\t<br />\n\t\t<ul>\n";

      $ambNum = 1;
      foreach($ambList as $amb)
      {
         $ambID = $amb['id'];

         echo "<form name=\"ManageAmbassadors\" method=\"post\" action=\"$submitFile\">";
            echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";
            //make an invisible division with a hyperlink for jumping to this section
            echo "\n\t\t\t<div id=\"sectionAmb$ambID\" style=\"display:inline\"></div>\n";
            echo "\t\t\t<li>Name: <input type=\"text\" name=\"amb_name$ambID\" value=\"".$amb['name']."\" size=\"25\" /></li>\n";	
            echo "\t\t\t<img name=\"ambImg$ambNum\" id=\"ambImg$ambNum\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($ambVisibility[$ambNum]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('amb$ambNum', 'ambImg$ambNum', 'ambVis', $ambNum, 1);\" />Collapse/Expand Ambassador\n";
            echo "\t\t\t<div name=\"amb$ambNum\" id=\"amb$ambNum\" style=\"".(($ambVisibility[$ambNum]=='1')?'display:inline':'display:none')."\">\n";
               echo "\t\t\t\t<ul>\n\t\t\t\t\t<li>NetID: <input type=\"text\" name=\"amb_netID$ambID\" value=\"".$amb['netID']."\" size=\"15\" /></li>\n";
                  echo "<li>Student ID#: <input type=\"text\" name=\"amb_studentID$ambID\" size=\"50\" value=\"".$amb['studentID']."\"></li>\n";
                  echo "\t\t\t\t\t<li>Major: \n";
                 //TODO$majorManager->printMajorsInSelectFormat("ambMaj$ambID", $ambID['major'], "\t\t\t\t\t", FALSE);
                  echo "\t\t\t\t\t<select name=\"amb_major$ambID\" size=\"1\">\n";
                  foreach($majList as $maj)
                  {
                     echo "\t\t\t\t\t\t<option".(($amb['major'] == $maj['id']) ? ' selected="SELECTED"' : '').">".$maj['longName']."</option>\n";
                  }
	              echo "\t\t\t\t\t</select>\n";
	              echo "\n\t\t\t\t\t</li>\n";
	
	              echo "<li>Campus Address: <input type=\"text\" name=\"amb_address$ambID\" size=\"50\" value=\"".$amb['address']."\"></li>\n";
	              echo "<li>City: <input type=\"text\" name=\"amb_city$ambID\" size=\"50\" value=\"".$amb['city']."\"></li>\n";
	              echo "<li>State: <input type=\"text\" name=\"amb_state$ambID\" size=\"50\" value=\"".$amb['state']."\"></li>\n";
	              echo "<li>Zip: <input type=\"text\" name=\"amb_zip$ambID\" size=\"50\" value=\"".$amb['zip']."\"></li>\n";
	              echo "<li>Hometown: <input type=\"text\" name=\"amb_hometown$ambID\" size=\"50\" value=\"".$amb['hometown']."\"></li>\n";
	              echo "<li>Phone Number: <input type=\"text\" name=\"amb_phone$ambID\" size=\"50\" value=\"".$amb['phone']."\"></li>\n";
	              echo "<li>Email Adress: <input type=\"text\" name=\"amb_email$ambID\" size=\"50\" value=\"".$amb['email']."\"></li>\n";
	              echo "<li>Went through the Honors Program?: <input type=\"text\" name=\"amb_honors$ambID\" size=\"50\" value=\"".$amb['honors']."\"></li>\n";
	              echo "<li>Lived in the Honors or Engage Communities as a Freshman?: <input type=\"text\" name=\"amb_community$ambID\" size=\"50\" value=\"".$amb['community']."\"></li>\n";
	              echo "<li>Has done an internship, co-op, or study abroad? If so, where?: <input type=\"text\" name=\"amb_work$ambID\" size=\"50\" value=\"".$amb['work']."\"></li>\n";
	              echo "<li>Biography:<br /><textarea name=\"amb_bio$ambID\" rows=\"20\" cols=\"70\">".$amb['bio']."</textarea><br />Note: Each new line will be its own paragraph.</li>\n";
	              echo "<li>Change Password<ul><li>Old Password: <input type=\"password\" name=\"amb_oldPW$ambID\" size=\"32\" maxlength=\"256\"/></li>\n";
	              echo "<li>New Password: <input type=\"password\" name=\"amb_new1PW$ambID\" size=\"32\" maxlength=\"256\"/></li>\n";
	              echo "<li>Confirm New Password: <input type=\"password\" name=\"amb_new2PW$ambID\" size=\"32\" maxlength=\"256\"/></li>\n";
                  echo "<li><input type=\"submit\" name=\"chPWAmb$ambID\" value=\"Change Password\" />&nbsp;<input type=\"submit\" name=\"resetPWAmb$ambID\" value=\"Reset Password\" /></li></ul>\n";
	              echo "\t\t\t\t\t<li><input type=\"submit\" name=\"confirmAmb$ambID\" value=\"Confirm Changes\" />&nbsp;<input type=\"submit\" name=\"resetAmb$ambID\" value=\"Reset Changes\" />&nbsp;<input type=\"submit\" name=\"deleteAmb$ambID\" value=\"Delete Ambassador\" /></li>\n";
	
               echo "\t\t\t\t</ul>\n";
	        echo "\t\t\t</div>\n\t\t\t<br />\n";

         echo "</form>";
	     $ambNum++;
      }

      echo "<form name=\"NewAmbassador\" method=\"post\" action=\"$submitFile\">";
      echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

         echo "\t\t\t<li><input type=\"submit\" name=\"createAmb\" value=\"Add a New Ambassador\" /></li>\n";
         echo "\t\t</ul>\n";
         echo "\t</div>\n\t<br />\n";

     echo "</form>";

?>  
    <br />
	<h2>Manage Majors</h2>
<?
echo "\t<img name=\"majListImg\" id=\"majListImg\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($majListVisibility[0]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('majList', 'majListImg', 'majListVis', 0, 1);\" />Collapse/Expand Major List\n";
echo "\t<div name=\"majList\" id=\"majList\" style=\"".(($majListVisibility[0]=='1')?'display:inline':'display:none')."\">\n";

echo "\t\t<br />\n\t\t<ul>\n";
$majNum = 1;
foreach($majList as $maj){
	$majID = $maj['id'];

	echo "<form name=\"ManageMajors\" method=\"post\" action=\"$submitFile\">";
	echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";
	
	//make an invisible division with a hyperlink for jumping to this section
	echo "\n\t\t\t<div style=\"display:inline\"><a name=\"maj$majID\"> </a></div>\n";
	
	echo "\t\t\t<li><input type=\"text\" name=\"maj_longName$majID\" value=\"".$maj['longName']."\" size=\"35\" /></li>\n";
	echo "\t\t\t<img name=\"majImg$majNum\" id=\"majImg$majNum\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($majVisibility[$majNum]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('maj$majNum', 'majImg$majNum', 'majVis', $majNum, 1);\" />Collapse/Expand Major\n";
	echo "\t\t\t<div name=\"maj$majNum\" id=\"maj$majNum\" style=\"".(($majVisibility[$majNum]=='1')?'display:inline':'display:none')."\">\n";
	
	echo "<ul><li>Short Name: <input type=\"text\" name=\"maj_shortName$majID\" value=\"".$maj['shortName']."\" size=\"35\" /></li>\n";
	echo "<li>Printed Name: <input type=\"text\" name=\"maj_printName$majID\" value=\"".$maj['printName']."\" size=\"35\" /></li>\n";
	echo "<li>Website: <input type=\"text\" name=\"maj_website$majID\" value=\"".$maj['website']."\" size=\"35\" /></li>\n";
	
	echo "<ul>\t\t\t\t\t<li>Ambassadors of this Major:</li>\n";
	echo "\t\t\t\t\t<img name=\"majAmbsImg$majNum\" id=\"majAmbsImg$majNum\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($majorAmbsVisibility[$majNum]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('majAmbs$majNum', 'majAmbsImg$majNum', 'majorAmbsVis', $majNum, 1);\" />Collapse/Expand List\n";
	echo "\t\t\t\t\t<div name=\"majAmbs$majNum\" id=\"majAmbs$majNum\" style=\"".(($majorAmbsVisibility[$majNum]=='1')?'display:inline':'display:none')."\">\n";

	echo "\t\t\t\t\t\t<ul>\n";
	
	$printed = 0;
	foreach($ambList as $amb){
		if($amb['major'] == $majID){
			//TODOecho "<li><a href=\"#sectionAmb".$ambArray[$ambMaj]->index."\">".$ambArray[$ambMaj]->name."</a></li>\n";
			echo "\t\t\t\t\t\t\t<li>".$amb['name']."</li>\n";
			$printed = 1;
		}
	}
	if($printed == 0){
		echo "\t\t\t\t\t\t\t<li>None</li>";
	}

	echo "\t\t\t\t\t\t</ul>\n";
	echo "\t\t\t\t\t</div>\n";
	echo "\t\t\t\t\t<li>Similar Majors:</li>\n";
	echo "\t\t\t\t\t<img name=\"majSimsImg$majNum\" id=\"majSimsImg$majNum\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($majorSimsVisibility[$majNum]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('majSims$majNum', 'majSimsImg$majNum', 'majorSimsVis', $majNum, 1);\" />Collapse/Expand List\n";
	echo "\t\t\t\t\t<div name=\"majSims$majNum\" id=\"majSims$majNum\" style=\"".(($majorSimsVisibility[$majNum]=='1')?'display:inline':'display:none')."\">\n";
	echo "\t\t\t\t\t\t<ul>\n";
	if($maj['similarMajors'] != ''){
		$majArray = explode(',', $maj['similarMajors']);
		$majCountSim = count($majArray);
		for($majSim = 0; $majSim < $majCountSim; $majSim++){
			//TODOecho "<li><a href=\"#maj".$majArray[$majSim]."\">".$majObjSim->longName."</a></li>\n";
			echo "\t\t\t\t\t\t\t<li>\n";
			echo "\t\t\t\t\t<select name=\"maj_similarMajors${majID}sim${majSim}\" size=\"1\">\n";
			foreach($majList as $majMatch){
				echo "\t\t\t\t\t\t<option".(($majMatch['id'] == $majArray[$majSim]) ? ' selected="SELECTED"' : '').">".$majMatch['longName']."</option>\n";
			}
			echo '<option>N/A</option>';
			echo "\t\t\t\t\t</select>\n";
			//TODO$majorManager->printMajorsInSelectFormat("maj_${maj}_Sim$majSim", $majArray[$majSim], "\t\t\t\t\t\t\t\t", TRUE);
			echo "\t\t\t\t\t\t\t</li>\n";
		}
	}
	echo "<li><input type=\"submit\" name=\"addSimilarMajor$majID\" value=\"Add a major to this list\" /></li>\n";
	echo "\t\t\t\t\t\t</ul>\n";
	
	echo "\t\t\t\t\t</div>\n</ul>";
	echo "\t\t\t\t\t<li><input type=\"submit\" name=\"confirmMaj$majID\" value=\"Confirm Changes\" />&nbsp;<input type=\"submit\" name=\"resetMaj$majID\" value=\"Reset Changes\" />&nbsp;<input type=\"submit\" name=\"deleteMaj$majID\" value=\"Delete Major\" /></li>\n";
	echo "\t\t\t\t</ul>\n\t\t\t</div>\n\t\t\t<br />\n";
	
	echo "</form>";

	$majNum++;
}

echo "<form name=\"NewMajor\" method=\"post\" action=\"$submitFile\">";
echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

echo "\t\t\t<li><input type=\"submit\" name=\"createMaj\" value=\"Add a New Major\" /></li>\n";
echo "\t\t</ul>\n";
echo "\t</div>\n\t<br />\n";

echo "</form>";

?>  
    <br /> 
	<h2>Manage Tours</h2>
    <img name="tourListImg" id="tourListImg" src="http://www.engr.utk.edu/ambassador/images/Orb-Blue-<?php echo ($tourListVisibility[0] == '0') ? 'Plus' : 'Minus'; ?>-24.png" height="12" width="12" title="Click here to minimize this section" onclick="javascript:toggleSection('tourList', 'tourListImg', 'tourListVis', 0, 1);" />Collapse/Expand Tours List
    <div name="tourList" id="tourList" style="list-style: none; <?php echo ($tourListVisibility[0] == '1') ? 'display:inline' : 'display:none'; ?>">
    <ul style="list-style: none;">
<?
        $tourNum = 1;
        foreach($tourInfoList as $tour){
	        $tourID = $tour['id'];
?>
	        <form name="ManageTours" method="post" action="<?php echo $submitFile; ?>">
	        <input type="hidden" name="lastPage" value="<?php echo $fileName; ?>">
	            <div style="display:inline;">
                    <a name="tour<?php echo $tourID; ?>"></a>
                </div>
                <li style="list-style: none;">
	                <img name="tourImg<?php echo $tourNum; ?>" id="tourImg <?php echo $tourNum; ?>" src="http://www.engr.utk.edu/ambassador/images/Orb-Blue-<?php echo ($tourVisibility[$tourNum] == '0') ? 'Plus' : 'Minus'; ?>-24.png" height="12" width="12" title="Click here to minimize this section" onclick="javascript:toggleSection('tour<?php echo $tourNum; ?>', 'tourImg<?php echo $tourNum; ?>', 'tourVis', <?php echo $tourNum; ?>, 1);" />
	                Tour #<?php echo $tourID; ?>, Day: <?php echo $tour['tourTime']; ?>, Student: <?php echo $tour['studentName']; ?>
                <li />

	            <div name="tour<?php echo $tourNum; ?>" id="tour<?php echo $tourNum; ?>" style="list-style: none; <?php echo ($tourVisibility[$tourNum] == '1') ? 'display:inline' : 'display:none'; ?>">
	                <ul style="list-style: none;">
                        <li>
                            Tour Time:
                            <input type="text" name="tour_tourTime<?php echo $tourID; ?>" value="<?php echo $tour['tourTime']; ?>" size="16" /> 
                            (YYYY-MM-DD HH:MM:SS, 24 Hour Time)
                        </li>

	                    <li style="list-style: none;">Majors Of Interest:
                            <ul>
<?
	                            if($tour['majorsOfInterest'] != NULL){
		                            $MOI = explode(',', $tour['majorsOfInterest']);
		                            $majCount = count($MOI);
		                            for($maj = 0; $maj < $majCount; $maj++){
			                            $majID = $MOI[$maj];
			                            $res = $majInfo["$majID"];
?>
			                            <li style="list-style: none;"> <?php echo $res['longName']; ?></li>
<?
		                            }
	                            } else {
?>
		                                <li style="list-style: none;">None Selected.</li>
<?
	                            }
?>
	                        </ul>
                        </li>
                        <li>
                            Student Name: 
                            <input type="text" name="tour_studentName<?php echo $tourID; ?>" value="<?php echo $tour['studentName']; ?>" size="30" />
                        </li>
	                    <li>
                            Parent Name: 
                            <input type="text" name="tour_parentName<?php echo $tourID; ?>" value="<?php echo $tour['parentName']; ?>" size="30" />
                        </li>
	                    <li>
                            Number of People: 
                            <input type="text" name="tour_numPeople<?php echo $tourID; ?>" value="<?php echo $tour['numPeople']; ?>" size="30" />
                        </li>
	                    <li>
                            School Student attends: 
                            <input type="text" name="tour_school<?php echo $tourID; ?>" value="<?php echo $tour['school']; ?>" size="30" />
                        </li>
	                    <li>
                            Year in School: 
                            <input type="text" name="tour_yearInSchool<?php echo $tourID; ?>" value="<?php echo $tour['yearInSchool']; ?>" size="30" />
                        </li>
	                    <li>
                            City Student is From: 
                            <input type="text" name="tour_city<?php echo $tourID; ?>" value="<?php echo $tour['city']; ?>" size="30" />
                        </li>
	                    <li>
                            State: 
                            <input type="text" name="tour_state<?php echo $tourID; ?>" value="<?php echo $tour['state']; ?>" size="30" />
                        </li>
	                    <li>
                            Email address: 
                            <input type="text" name="tour_email<?php echo $tourID; ?>" value="<?php echo $tour['email']; ?>" size="30" />
                        </li>
	                    <li>
                            Phone Number: 
                            <input type="text" name="tour_phone<?php echo $tourID; ?>" value="<?php echo $tour['phone']; ?>" size="30" />
                        </li>
	                    <li>
                            Special Accommodations: 
                            <input type="text" name="tour_needs<?php echo $tourID; ?>" value="<?php echo $tour['special']; ?>" size="30" />
                        </li>
	                    <li>
                            Confirmation Code: 
                            <input type="text" name="tour_confirmationCode<?php echo $tourID; ?>" value="<?php echo $tour['confirmationCode']; ?>" size="30" />
                        </li>
	                    <li>
                            Time slot id: 
                            <input type="text" name="tour_scheduleID<?php echo $tourID; ?>" value="<?php echo $tour['scheduleID']; ?>" size="30" />
                        </li>
	                    <li>
                            Status:
<?
	                        switch($tour['status']){
		                        case 0:
			                        $status = "(0) Waiting on Confirmation";
			                        break;
		                        case 1:
			                        $status = "(1) Accepted by Ambassador";
			                        break;
		                        case 2;
			                        $status = "(2) Denied by all Ambassadors, needs Reassignment";
			                        break;
		                        case 3:
			                        $status = "(3) Followup Email sent after Tour";
			                        break;
		                        case 4;
			                        $status = "(4) Family did not show up for Tour";
			                        break;
		                        case 5;
			                        $status = "(5) Tour was deleted";
			                        break;
		                        default:
			                        $status = "ERROR, Contact Elliot Greenlee";
	                        }
?>
	                        <?php echo $status; ?>
                        </li>
	                    <li>
                            Ambassador Assigned to This Tour:
<?
	                        $ambID = $tour['ambassadorAssigned'];
	                        if($ambID > 0){
		                        $amb = $ambList["$ambID"];
?>
		                        <?php echo $amb['name']; ?>
                                </li>
<?
	                        } else {
?>
		                        None
                                </li>
<?
	                        }
?>
	                    <li style="list-style: none;">
                            <input type="submit" name="confirmTour<?php echo $tourID; ?>" value="Confirm Changes" />
                            &nbsp;
                            <input type="submit" name="resetTour<?php echo $tourID; ?>" value="Reset Changes" />
                            &nbsp;
                            <input type="submit" name="deleteTour<?php echo $tourID; ?>" value="Delete Tour" />
                        </li>
	                </div>
                </form>
<?
	            $tourNum++;
}
?>
                <form name="NewTour" method="post" action="<?php echo $submitFile; ?>">
                <input type="hidden" name="lastPage" value="<?php echo $fileName; ?>">
                    <li style="list-style: none;">
                        <input type="submit" name="createTour" value="Add a New Tour" />
                    </li>
            </ul>
            </div>
    </form>

	<h2>Manage Schedule</h2>
<?

echo "<form name=\"ManageSchedule\" method=\"post\" action=\"$submitFile\">";
echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

echo "\t<img name=\"schedImg\" id=\"schedImg\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($schedVisibility[0]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('sched', 'schedImg', 'schedImgVis', 0, 1);\" />Collapse/Expand Schedule Section\n";
echo "\t<div name=\"sched\" id=\"sched\" style=\"".(($schedVisibility[0]=='1')?'display:inline':'display:none')."\">\n\t\t<br />\n\t\t<ul>\n";

echo "\t\t\t\t\t<li>The first day of classes is: \n";
echo "<input type=\"text\" name=\"sched_firstDayOfTours\" value=\"".$generalInfo['firstDayOfTours']."\" size=\"16\" /> (YYYY-MM-DD)</li>\n";
echo "\t\t\t\t\t<li>The day tours are public: \n";
echo "<input type=\"text\" name=\"sched_tourPublicDay\" value=\"".$generalInfo['tourPublicDay']."\" size=\"16\" /> (YYYY-MM-DD)</li>\n";
echo "\t\t\t\t\t<li>The last day of classes is: \n";
echo "<input type=\"text\" name=\"sched_lastDayOfTours\" value=\"".$generalInfo['lastDayOfTours']."\" size=\"16\" /> (YYYY-MM-DD)</li>\n";

echo "\t\t\t\t\t<li>The message displayed when tours are not going on is: <textarea rows=\"10\" cols=\"70\" name=\"sched_tourPageDownMsg\">".$generalInfo['tourPageDownMsg']."</textarea></li>\n";

echo "\t\t\t\t\t<ul><li><input type=\"submit\" name=\"confirmEos\" value=\"Confirm Changes\" />&nbsp;<input type=\"submit\" name=\"resetEos\" value=\"Reset Changes\" /></li></ul>\n</li>";

echo "</form>";

foreach($holidayList as $hol){
	$holID = $hol['id'];

	echo "<form name=\"ManageHoliday\" method=\"post\" action=\"$submitFile\">";
	echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";
	
	//$holDate = $scheduleManager->holidayList[$hol];
	echo "\t\t\t\t\t<li>Holiday On: \n";
	echo "<input type=\"text\" name=\"sched_holidayDate$holID\" value=\"".$hol['holidayDate']."\" size=\"16\" /> (YYYY-MM-DD)</li>\n";
	echo "<ul><li>Description: \n";
	echo "<input type=\"text\" name=\"sched_description$holID\" value=\"".$hol['description']."\" size=\"32\" /> (YYYY-MM-DD)</li></ul>\n";
	echo "\t\t\t\t\t<ul><li><input type=\"submit\" name=\"confirmHol$holID\" value=\"Confirm Changes\" />&nbsp;<input type=\"submit\" name=\"resetHol$holID\" value=\"Reset Changes\" />&nbsp;<input type=\"submit\" name=\"deleteHol$holID\" value=\"Delete Holiday\" /></li></ul>\n</li>";

	echo "</form>";
}

echo "<form name=\"NewHoliday\" method=\"post\" action=\"$submitFile\">";
echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

echo "\t\t\t<li><input type=\"submit\" name=\"addHoliday\" value=\"Add a New Holiday\" /></li>\n";
echo "\t\t</ul>\n";
echo "\t</div>\n\t<br />\n";

echo "</form>";
	
?>
    <br /> 
	<h2>Manage Tour Times</h2>
<?

echo "\t<img name=\"tourTimeImg\" id=\"tourTimeImg\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($tourVisibility[0]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('tourTime', 'tourTimeImg', 'tourTimeImgVis', 0, 1);\" />Collapse/Expand Tour Times\n";
echo "\t<div name=\"tourTime\" id=\"tourTime\" style=\"".(($tourTimeVisibility[0]=='1')?'display:inline':'display:none')."\">\n\t\t<br />\n\t\t\n";

foreach($tourTimeList as $time){
	$timeID = $time['id'];

	echo "<form name=\"ManageTourTimes\" method=\"post\" action=\"$submitFile\">";
	echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";
	
	echo "<ul><li>Day of Week: <input type=\"text\" name=\"time_dayOfWeek$timeID\" value=\"".$time['dayOfWeek']."\" size=\"16\" /> (0-6 = Su-Sa)</li><ul>\n";
	echo "<li>Time: <input type=\"text\" name=\"time_timeSlot$timeID\" value=\"".$time['timeSlot']."\" size=\"16\" /> (HH:MM:SS - 24 Hour Format)</li>\n";
	echo "<li>Max Groups Allowed: <input type=\"text\" name=\"time_groupsAvailable$timeID\" value=\"".$time['groupsAvailable']."\" size=\"16\" /></li>\n";
	echo "<li>Available Ambassadors:</li>\n<ul>\n";
	if($time['availableAmbassadors'] != ''){
		$ambArray = split(',', $time['availableAmbassadors']);
		$ambs = count($ambArray);
		for($amb = 0; $amb < $ambs; $amb++){
			echo "\t\t\t\t\t\t\t<li>\n";
			echo "\t\t\t\t\t<select name=\"time_ambsAvailable${timeID}amb${amb}\" size=\"1\">\n";
			foreach($ambList as $ambMatch){
				echo "\t\t\t\t\t\t<option".(($ambMatch['id'] == $ambArray[$amb]) ? ' selected="SELECTED"' : '').">".$ambMatch['name']."</option>\n";
			}
			echo '<option>N/A</option>';
			echo "\t\t\t\t\t</select>\n";
			echo "\t\t\t\t\t\t\t</li>\n";
		}
	}// else {
	//	echo "<li>No ambassadors listed</li>\n";
	//}
	echo "<li><input type=\"submit\" name=\"addAmbassador$timeID\" value=\"Add an ambassador to this list\" /></li>\n</ul>\n";
	echo "\t\t\t\t\t<li><input type=\"submit\" name=\"confirmTime$timeID\" value=\"Confirm Changes\" />&nbsp;<input type=\"submit\" name=\"resetTime$timeID\" value=\"Reset Changes\" />&nbsp;<input type=\"submit\" name=\"deleteTime$timeID\" value=\"Delete Tour Time\" />\n</li></ul></ul>";

	echo "</form>";

}

echo "<form name=\"NewTourTime\" method=\"post\" action=\"$submitFile\">";
echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

echo "<ul><li>\t\t\t<input type=\"submit\" name=\"addTourTime\" value=\"Add a New Tour Time\" /></li></ul>\n";
echo "<ul>\t</div>\n\t<br />\n";

echo "</form>";

?>
    <br /> 
	<h2>Manage Email Settings</h2>
<?

echo "\t<img name=\"emailImg\" id=\"emailImg\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($emailVisibility[0]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('email', 'emailImg', 'emailImgVis', 0, 1);\" />Collapse/Expand Email Settings\n";
echo "\t<div name=\"email\" id=\"email\" style=\"".(($emailVisibility[0]=='1')?'display:inline':'display:none')."\">\n\t\t<br />\n\t\t\n";

echo "<form name=\"ManageEmailSettings\" method=\"post\" action=\"$submitFile\">";
echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

echo "<ul><li>Tour email subject line for Ambassadors: <input type=\"text\" name=\"email_ambEmailSubject\" value=\"".$generalInfo['ambEmailSubject']."\" size=\"64\" maxlength=\"256\"/></li>\n";
echo "<li>Tour email body for Ambassadors:<textarea rows=\"10\" cols=\"70\" name=\"email_ambEmailBody\">".$generalInfo['ambEmailBody']."</textarea></li>\n";
echo "<li>Tour email subject line for tour families: <input type=\"text\" name=\"email_tourEmailSubject\" value=\"".$generalInfo['tourEmailSubject']."\" size=\"64\" maxlength=\"256\"/></li>\n";
echo "<li>Tour email body for tour families:<textarea rows=\"10\" cols=\"70\" name=\"email_tourEmailBody\">".$generalInfo['tourEmailBody']."</textarea></li>\n";
echo "<li>Use {amb_name}, {amb_major}, {tour_(studentName, parentName, phone, numPeople, city, state, email, school, yearInSchool, tourTime, confirmationCode, majorsOfInterest)} in the fields above and they will be replaced with the appropriate value before sending.</li>";
echo "<li><input type=\"submit\" name=\"confirmEmailSettings\" value=\"Confirm Changes\" />&nbsp;<input type=\"submit\" name=\"resetEmailSettings\" value=\"Reset Changes\" /></li>\n";

echo "</form>";

foreach($emailInfo as $email){
	$emailID = $email['id'];

	echo "<form name=\"ManageEmail\" method=\"post\" action=\"$submitFile\">";
	echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";
	
	echo "<li>Email Address: <input type=\"text\" name=\"email_email$emailID\" value=\"".$email['email']."\" size=\"64\" maxlength=\"256\"/></li><ul>\n";
	echo "<li>Emails to Receive: <select name=\"email_type$emailID\">\n";
	echo "<option".(($email['type']==0) ? ' selected="SELECTED"' : '').">All emails</option>\n";
	echo "<option".(($email['type']==1) ? ' selected="SELECTED"' : '').">Only tour signup emails</option>\n";
	echo "<option".(($email['type']==2) ? ' selected="SELECTED"' : '').">Only ambassador action emails</option>\n";
	echo "</select></li>\n";
	echo "\t\t\t\t\t<li><input type=\"submit\" name=\"confirmEmail$emailID\" value=\"Confirm Changes\" />&nbsp;<input type=\"submit\" name=\"resetEmail$emailID\" value=\"Reset Changes\" />&nbsp;<input type=\"submit\" name=\"deleteEmail$emailID\" value=\"Delete Email Address\" />\n</li></ul>";

	echo "</form>";
}

echo "<form name=\"NewEmail\" method=\"post\" action=\"$submitFile\">";
echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

echo "<li>\t\t\t<input type=\"submit\" name=\"addEmail\" value=\"Add a New Email Address\" /></li></ul>\n";
echo "\t</div>\n\t<br />\n";

echo "</form>";

?>
    <br /> 
	<h2>Manage Admin Users</h2>
<?

echo "\t<img name=\"adminImg\" id=\"adminImg\" src=\"http://www.engr.utk.edu/ambassador/images/Orb-Blue-".(($adminVisibility[0]=='0')?'Plus':'Minus')."-24.png\" height=\"12\" width=\"12\" title=\"Click here to minimize this section\" onclick=\"javascript:toggleSection('admin', 'adminImg', 'adminImgVis', 0, 1);\" />Collapse/Expand Admin List\n";
echo "\t<div name=\"admin\" id=\"admin\" style=\"".(($adminVisibility[0]=='1')?'display:inline':'display:none')."\">\n\t\t<br />\n\t\t\n";

foreach($adminUsers as $admin){
	$adminID = $admin['id'];

	echo "<form name=\"ManageAdminUsers\" method=\"post\" action=\"$submitFile\">";
	echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";
	
	echo "<li>NetID: <input type=\"text\" name=\"admin_netID$adminID\" value=\"".$admin['netID']."\" size=\"64\" maxlength=\"256\"/></li><ul>\n";
	echo "<li>Type of Permissions: <select name=\"admin_type$adminID\">\n";
	echo "<option".(($admin['type']==0) ? ' selected="SELECTED"' : '').">No special permissions</option>\n";
	echo "<option".(($admin['type']==1) ? ' selected="SELECTED"' : '').">Tour Permissions</option>\n";
	echo "<option".(($admin['type']==2) ? ' selected="SELECTED"' : '').">All Permissions</option>\n";
	echo "</select></li>\n";
	echo "<li>Change Password<ul><li>Old Password: <input type=\"password\" name=\"admin_oldPW$adminID\" size=\"32\" maxlength=\"256\"/></li>\n";
	echo "<li>New Password: <input type=\"password\" name=\"admin_new1PW$adminID\" size=\"32\" maxlength=\"256\"/></li>\n";
	echo "<li>Confirm New Password: <input type=\"password\" name=\"admin_new2PW$adminID\" size=\"32\" maxlength=\"256\"/></li>\n";
	echo "<li><input type=\"submit\" name=\"chPWAdmin$adminID\" value=\"Change Password\" />&nbsp;<input type=\"submit\" name=\"resetPWAdmin$adminID\" value=\"Reset Password\" /></li></ul>\n";
	echo "\t\t\t\t\t<li><input type=\"submit\" name=\"confirmAdmin$adminID\" value=\"Confirm Changes\" />&nbsp;<input type=\"submit\" name=\"resetAdmin$adminID\" value=\"Reset Changes\" />&nbsp;<input type=\"submit\" name=\"deleteAdmin$adminID\" value=\"Delete Admin Account (Be Careful)\" />\n</li></ul>";

	echo "</form>";

}

echo "<form name=\"NewAdmin\" method=\"post\" action=\"$submitFile\">";
echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

echo "<li>\t\t\t<input type=\"submit\" name=\"addAdmin\" value=\"Add a New Admin Account\" /></li></ul>\n";
echo "\t</div>\n\t<br />\n";

echo "</form>";


echo "<form name=\"NewSemester\" method=\"post\" action=\"$submitFile\">";
echo "<input type=\"hidden\" name=\"lastPage\" value=\"$fileName\">";

?>
	<h2>Start a New Semester</h2>
	<p><h4>This moves all current logbook entries and tours into the archives. Only this semester's tours and logs will be visible here.</h4></p>
	<input type="submit" name="newSemester" value="Click Here To Start a New Semester">
	<br />
	
<?

mysql_close($db);

?>

<br />
<input type="submit" name="submit" value="logout">
<input type="hidden" name="lastPage" value="<?=$fileName?>">
<input type="submit" name="downloadAmbassadorHours" value="Download Ambassador Hours">
<input type="submit" name="downloadHistory" value="Download the tour history">
<br />

</form>

<?
}
else //bad login info
{

//<doc>Note: Either the user gave bad login info, or is viewing the page for the first time.</doc>
?>
					<center>
					<b>
					This page is password protected<br />
					please enter username and password<br />
					</b>
					<form method="post" action="manager.php">

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
								
							</td>
							<td>&nbsp;</td>
							<td>
								<input type="submit" name="submit" value="My password isn't working">
							</td>
							</tr>
					</table>
</form>
					</center>

<?
} //end password check
?>

</section>
<?
printFooter(array('Manager Page' => 'manager.php'));
?>
