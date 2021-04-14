<?php
/*
<doc>Filename: logbook.php</doc>
<doc>Summary: Place where Ambassadors log their hours</doc>
<doc>Long: Each Ambassador logs into their individual page using their netID and the password '3ng!neer'. Then they log their hours and can view their previous hour logs. Also, there is a simple profile on this page with basic information that nobody actually ever fills out.</doc>
<doc>Todo:</doc>
<doc>	*Maybe tie this page in with the calendar page so that an ambassador can write a log book entry specifically for each tour</doc>
*/

include('login.php');

$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

$fileName = 'logbook.php';
$submitFile = 'logbookSubmit.php';

include 'webpages.php';

printHeader('Logbook');
printNavigation((($password == 1) || ($password == 2)), false, ($password == 2)); 

?>

<section class="two-thirds column">
<h1>Logbook</h1>

<? 
if($password=="1")
{
?>

<br />


<!-- add notes for logbook entries here -->
<h3>Ambassadors,
<br />
<p>Please record your tour and event information in the fields below. 
Your responses help the College of Engineering plan for future tours 
and events, as well as help us to help you in your service as an ambassador.</p>
<p>Thank you very much for taking your time and being thorough in completing each log entry, and don't forget to send
a thank you to your tour! <a href="http://www.engr.utk.edu/thankyou/thankyouforvisiting.html">http://www.engr.utk.edu/thankyou/thankyouforvisiting.html</a></p>
</h3>
<br />
<form method="post" action="<?=$submitFile?>">
   <input type="hidden" name="lastPage" value="<?=$fileName?>">
   <ul>
      <h5>
      <li>What was the name of the event? (tour, college fair, etc.) </li><br /><br /> 
         <input type="text" size="36" maxlength="48" name="eventName"><br /><br />
      <li>If the event was a tour, what was the tour ID# ? <br /><br /> 
         <input type="text" size="16" maxlength="16" name="tourID"></li><br /><br />
      <li>What was the date of the event? <br /><br /> 
         <input type="text" size="24" maxlength="30" name="eventDate"> (YYYY-MM-DD)</li><br /><br />
      <li>How many hours did you work on this event? <br /><br /> 
         <input type="text" size="24" maxlength="30" name="eventHours"> (numerical values only)</li><br /><br />
      <li>How many people were on the tour, or for other events, with how many people did you interact? <br /><br /> 
         <input type="text" size="24" maxlength="30" name="numberPeople"></li><br /><br />
      <li>If you were talking with prospective students, at what other schools were they looking?<br /><br />
         <textarea rows="3" cols="80" name="otherSchools" wrap="physical"></textarea></li><br /><br /><br />
      <li>Briefly describe your experience. Be sure to tell if it was positive or negative and why.<br /><br />
         <textarea rows="3" cols="80" name="experience" wrap="physical"></textarea></li><br /><br /><br />
      <li>What did people ask about? What were their concerns?<br /><br />
         <textarea rows="3" cols="80" name="questions" wrap="physical"></textarea></li><br /><br /><br />
      <li>What could have made your job better?<br /><br />
         <textarea rows="3" cols="80" name="jobBetter" wrap="physical"></textarea></li><br /><br /><br />
      <li>How could you have improved how it went?<br /><br />
         <textarea rows="3" cols="80" name="improvements" wrap="physical"></textarea></li><br /><br /><br />
      </h5>
   </ul>
   <input type="submit" value="Submit Log" name="submit">
</form>
<br />

<h3>Old Logbook Entries:</h3>
<br />

<?

require_once('/home/coeamb/public_html/mysqlFunctions.php');

require_once('/home/coeamb/database/dbInfo.php');

$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());

mysql_select_db($dbName) or die('Could not select database: '.mysql_error());

$safeNetID = mysql_real_escape_string($userName);
$ambID = mysqlQuerySingleRow("SELECT id FROM ambassadorInfo WHERE netID='$safeNetID' LIMIT 1");
$safeAmbID = mysql_real_escape_string($ambID['id']);
$yourLogs = mysqlQueryToList("SELECT * FROM logbook WHERE ambassador='$safeAmbID' ORDER BY id");

mysql_close($db);

foreach($yourLogs as $log){
	echo "<b>Event Name: ".$log['eventName']."</b><br />\n";
	if($log['tourID'] != 0){
		echo "&nbsp;Tour ID: ".$log['tourID']."<br />\n";
	}
    $tsTimeStamp5 = strtotime($log['eventDate']);
    $dtStr5 = date("c", $tsTimeStamp5);
    $dt = new DateTime($dtStr5);
	//$dt = DateTime::createFromFormat('Y-m-d', $log['eventDate']);
	echo "&nbsp;Event Date: ".$dt->format('l, F jS Y')."<br />\n";
	echo "&nbsp;Hours: ".$log['hours']." hours<br />\n";
	echo "&nbsp;People interacted with: ".$log['peopleInteracted']."<br />\n";
	echo "&nbsp;Other schools of interest: ".$log['otherSchools']."<br />\n";
	echo "&nbsp;Your Experience: ".$log['experience']."<br />\n";
	echo "&nbsp;Questions asked: ".$log['questions']."<br />\n";
	echo "&nbsp;Could have made your job better: ".$log['madeJobBetter']."<br />\n";
	echo "&nbsp;You could have improved: ".$log['improvements']."<br />\n<br />\n";
}
?>

<!--<table>
	<tr>
		<td>
		</td>
		<td align="right">
			<input type="submit" value="logout" name="submit">
		</td>
	</tr>
</table>-->

<?
}
elseif($password=="2")
{

require_once('/home/coeamb/public_html/mysqlFunctions.php');

require_once('/home/coeamb/database/dbInfo.php');

$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());

mysql_select_db($dbName) or die('Could not select database: '.mysql_error());

$allLogs = mysqlQueryToList('SELECT * FROM logbook ORDER BY id');
$ambList = mysqlQueryToListIndexBy('SELECT id, name FROM ambassadorInfo', 'id');
//$tourLogs = mysqlQueryToList("SELECT * FROM tourInfo WHERE tourComments!='' ORDER BY id");

mysql_close($db);

echo "<h3>Old Logbook Entries:</h3>\n";
foreach($allLogs as $log){
	echo "<b>Event Name: ".$log['eventName']."</b><br />\n";
	$ambID = $log['ambassador'];
	$amb = $ambList["$ambID"];
	echo "&nbsp;Ambassador: ".$amb['name']."<br />\n";
	if($log['tourID'] != 0){
		echo "&nbsp;Tour ID: ".$log['tourID']."<br />\n";
	}
    $tsTimeStamp4 = strtotime($log['eventDate']);
    $dtStr4 = date("c", $tsTimeStamp4);
    $dt = new DateTime($dtStr4);
	//$dt = DateTime::createFromFormat('Y-m-d', $log['eventDate']);
	echo "&nbsp;Event Date: ".$dt->format('l, F jS Y')."<br />\n";
	echo "&nbsp;Hours: ".$log['hours']." hours<br />\n";
	echo "&nbsp;People interacted with: ".$log['peopleInteracted']."<br />\n";
	echo "&nbsp;Other schools of interest: ".$log['otherSchools']."<br />\n";
	echo "&nbsp;Your Experience: ".$log['experience']."<br />\n";
	echo "&nbsp;Questions asked: ".$log['questions']."<br />\n";
	echo "&nbsp;Could have made your job better: ".$log['madeJobBetter']."<br />\n";
	echo "&nbsp;You could have improved: ".$log['improvements']."<br />\n<br />\n";
}

//TODO: link these two together if it is possible

/* TODO: Add the sections back in where you can view tour comments
echo "<h3>Tour Comments:</h3>\n";
foreach($tourLogs as $tour){
	$dt = DateTime::createFromFormat('Y-m-d G:i:s', $tour['tourTime']);
	echo "<b>Tour Time: ".$dt->format('l, F jS Y \a\t h:i A')."</b><br />\n";
	$ambID = $tour['ambassadorAssigned'];
	$amb = $ambList["$ambID"];
	echo "&nbsp;Ambassador Giving Tour: ".$amb['name']."<br />\n";
	if($tour['id'] != 0){
		echo "&nbsp;Tour ID: ".$tour['id']."<br />\n";
	}
}*/

?>
<br /><br />
			<form method="post" action="<?=$submitFile?>">
			<input type="hidden" name="lastPage" value="<?=$fileName?>">
			<input type="submit" value="logout" name="submit">
			<input type="submit" value="Download Tour Log" name="submit">
			<input type="submit" value="Download Sneak Peek Log" name="submit">
			<input type="submit" value="Download Ambassador Info" name="submit">
			<br />These buttons won't do anything for a while (logout works though)
			</form>
<br />

<?
}
else
{
	//Either the user gave bad login info, or is viewing the page for the first time.
	printLogIn($fileName, $submitFile);
}
?>
</section>
<?

printFooter(array('Logbook' => 'logbook.php'));

?>
