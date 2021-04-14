<?php
/*
<doc>Filename: profile.php</doc>
<doc>Summary: Place where Ambassadors log their hours</doc>
<doc>Long: Each Ambassador logs into their individual page using their netID and the password '3ng!neer'. Then they log their hours and can view their previous hour logs. Also, there is a simple profile on this page with basic information that nobody actually ever fills out.</doc>
<doc>Todo:</doc>
	*add the ability for each ambassador to create their own profile page, consolidate all of the profiles into one page
	*each  ambassador's profile only shows up if they have submitted their paragraph? possibly cant give tours until bio is up?
*/ ?>

<?php
include('login.php');

$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

//echo "HI $userName, $password\n";
$fileName = 'profile.php';
$submitFile = 'profileSubmit.php';

include('webpages.php');
printHeader('Profile');
printNavigation((($password == 1) || ($password == 2)), false, ($password == 2)); 

?>
<section class="two-thirds column">
<h1>Ambassador Profile</h1>
<?
if($password=="1"){
	//echo "HI user $userName, $password\n";
	//$ambObj = $ambassadorManager->getAmbassadorByUTid($logbook);
	//TODO: do a check here for valid ambassador
	require_once('/home/coeamb/database/dbInfo.php');

	require_once('/home/coeamb/public_html/mysqlFunctions.php');

	$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword)
		or die('Could not connect: '.mysql_error());

	mysql_select_db($dbName)
		or die('Could not select database: '.mysql_error());
	
	$safeUserName = mysql_real_escape_string($userName);
	$ambInfo = 		mysqlQuerySingleRow("SELECT * FROM ambassadorInfo WHERE netID='$safeUserName' LIMIT 1");
	$majList = 		mysqlQueryToList('SELECT * FROM majorInfo');
?>

<h2><img name="profimg" id="profimg" src="http://www.engr.utk.edu/ambassador/images/Orb-Blue-Minus-24.png" height="12" width="12" title="Click here to minimize this section" onclick="javascript:toggleSection('profile','profimg',0,0);" />Current Profile Information:</h2>
<div id="profile" name="profile" style="display:inline">
<br />
<form method="post" action="<?=$submitFile?>">
<input type="hidden" name="lastPage" value="<?=$fileName?>">
<ul>
<?php
	$ambID = $ambInfo['id'];
	echo "\t\t\t<li>Name: <input type=\"text\" name=\"amb_name$ambID\" value=\"".$ambInfo['name']."\" size=\"25\" /></li>\n";	

	echo "\t\t\t\t\n\t\t\t\t\t<li>NetID: <input type=\"text\" name=\"amb_netID$ambID\" value=\"".$ambInfo['netID']."\" size=\"15\" /></li>\n";
	echo "<li>Student ID#: <input type=\"text\" name=\"amb_studentID$ambID\" size=\"50\" value=\"".$ambInfo['studentID']."\"></li>\n";
	echo "\t\t\t\t\t<li>Major: \n";
	//TODO$majorManager->printMajorsInSelectFormat("ambMaj$ambID", $ambID['major'], "\t\t\t\t\t", FALSE);
	echo "\t\t\t\t\t<select name=\"amb_major$ambID\" size=\"1\">\n";
	foreach($majList as $maj){
		echo "\t\t\t\t\t\t<option".(($ambInfo['major'] == $maj['id']) ? ' selected="SELECTED"' : '').">".$maj['longName']."</option>\n";
	}
	echo "\t\t\t\t\t</select>\n";
	echo "\n\t\t\t\t\t</li>\n";

	echo "<li>Campus Address: <input type=\"text\" name=\"amb_address$ambID\" size=\"50\" value=\"".$ambInfo['address']."\"></li>\n";
	echo "<li>City: <input type=\"text\" name=\"amb_city$ambID\" size=\"50\" value=\"".$ambInfo['city']."\"></li>\n";
	echo "<li>State: <input type=\"text\" name=\"amb_state$ambID\" size=\"50\" value=\"".$ambInfo['state']."\"></li>\n";
	echo "<li>Zip: <input type=\"text\" name=\"amb_zip$ambID\" size=\"50\" value=\"".$ambInfo['zip']."\"></li>\n";
	echo "<li>Hometown: <input type=\"text\" name=\"amb_hometown$ambID\" size=\"50\" value=\"".$ambInfo['hometown']."\"></li>\n";
	echo "<li>Phone Number: <input type=\"text\" name=\"amb_phone$ambID\" size=\"50\" value=\"".$ambInfo['phone']."\"></li>\n";
	echo "<li>Email Adress: <input type=\"text\" name=\"amb_email$ambID\" size=\"50\" value=\"".$ambInfo['email']."\"></li>\n";
	echo "<li>Went through the Honors Program?: <input type=\"text\" name=\"amb_honors$ambID\" size=\"50\" value=\"".$ambInfo['honors']."\"></li>\n";
	echo "<li>Lived in the Honors or Engage Communities as a Freshman?: <input type=\"text\" name=\"amb_community$ambID\" size=\"50\" value=\"".$ambInfo['community']."\"></li>\n";
	echo "<li>Did an internship, co-op, or study abroad? If so, where?: <input type=\"text\" name=\"amb_work$ambID\" size=\"50\" value=\"".$ambInfo['work']."\"></li>\n";
	echo "<li>Biography:<br /><textarea name=\"amb_bio$ambID\" rows=\"20\" cols=\"70\">".$ambInfo['bio']."</textarea><br />Note: Each new line will be its own paragraph.</li>\n";
	echo "<li>Change Password<ul><li>Old Password: <input type=\"password\" name=\"amb_oldPW$ambID\" size=\"32\" maxlength=\"256\"/></li>\n";
	echo "<li>New Password: <input type=\"password\" name=\"amb_new1PW$ambID\" size=\"32\" maxlength=\"256\"/></li>\n";
	echo "<li>Confirm New Password: <input type=\"password\" name=\"amb_new2PW$ambID\" size=\"32\" maxlength=\"256\"/></li>\n";
	echo "<li><input type=\"submit\" name=\"submit\" value=\"Change Password\" />&nbsp;<input type=\"submit\" name=\"submit\" value=\"Reset Password\" /></li></ul>\n";

	echo "\t\t\t\t\n";
	echo "\n\t\t\t<br />\n";
//TODO: Actually add the fields here to the ambassador objects
/*
<ul>
	<li>Name: <input type="text" name="profname" size="50" value="<?=$ambObj->name?>"></li><br />
	<li>Student ID#: <input type="text" name="profid" size="50" value="<?=$ambObj->studentID?>"></li><br />
	<li>Campus Address: <input type="text" name="profaddress" size="50" value="<?=$ambObj->address?>"></li><br />
	<li>City: <input type="text" name="profcity" size="50" value="<?=$ambObj->city?>"></li><br />
	<li>State: <input type="text" name="profstate" size="50" value="<?=$ambObj->state?>"></li><br />
	<li>Zip: <input type="text" name="profzip" size="50" value="<?=$ambObj->zip?>"></li><br />
	<li>Hometown: <input type="text" name="profhometown" size="50" value="<?=$ambObj->homeTown?>"></li><br />
	<li>Phone Number: <input type="text" name="profphone" size="50" value="<?=$ambObj->phoneNumber?>"></li><br />
	<li>Email Adress: <input type="text" name="profemail" size="50" value="<?=$ambObj->email?>"></li><br />
	<li>Did you go through the Honors Program?: <input type="text" name="profhonors" size="50" value="<?=$ambObj->honors?>"></li><br />
	<li>Did you live in the Honors or Engage Communities as a Freshman?: <input type="text" name="profCommunity" size="50" value="<?=$ambObj->community?>"></li><br />

	echo "<li>What is your major?: \n";
	$majorManager->printMajorsInSelectFormat("profMajor", $ambObj->major, "\t", FALSE);
	echo "</li><br />\n";

	<li>Have you done an internship, co-op, or study abroad? If so, where?: <input type="text" name="profWork" size="50" value="<?=$ambObj->work?>"></li><br />
	<li>Profile page text: (each line break will become a new paragraph)<br />
		<textarea name="profText" cols="80" rows="40"><?=$profText?></textarea>
	</li>
</ul>
*/
?>
</h4>
</div>
<br />
<input type="submit" name="submit" value="Update Profile Information">
<input type="submit" name="submit" value="logout">
</form>

<?
} elseif($password=="2"){
	
?>
<form method="post" action="<?=$submitFile?>">
<input type="hidden" name="lastPage" value="<?=$fileName?>">
Lisa, let me know if you want anything on this page -Elliot
<!-- put something here that would be good to put on a master profile page -->
<!-- such as downloading all logs, viewing condensed log info, etc -->
<input type="submit" name="submit" value="logout">
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

</section>
<?

printFooter(array('Profile' => 'profile.php'));

?>
