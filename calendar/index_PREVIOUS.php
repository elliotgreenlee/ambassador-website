<?
/* notes/todo
-possibly work on cookies to make it more robust
-possibly make confirming the date/time less awkward
-work on integrating the undecided engineering stuff into the rest of the code, make it so that the first major selected would show up? (with a note to say that it might change)
-the javascript confirm function will report improper times if DST is in effect in Knoxville and the user is in an area that does not follow DST. Or at least I think it will. There's really no way to check as far as I know
-get rid of that stupid scroll bar, figure out how to dynamically resize the size of the iframe. I guess thats on the tours.php page. but figure something out here.
-update parts of the page to use the $_engFullNames and $_engFancyNames where it would be easier to do so
-look at strange combinations of similar/undecided majors
*/

$timestamp = time();
date_default_timezone_set('America/New_York'); //so that dates will show up as knoxville time instead of time local to the user
$maxEventsShown=4;
$colWidth = 90;
require_once('/home/coeamb/public_html/commonInfo.php');
$websites = array('http://www.engr.utk.edu/mabe/up-ae.html', 'http://www.engr.utk.edu/cbe/', 'http://www.eecs.utk.edu/', 'http://www.engr.utk.edu/mse/',
		'http://www.engr.utk.edu/mabe/up-be.html', 'http://www.engr.utk.edu/civil/', 'http://www.eecs.utk.edu/', 'http://www.engr.utk.edu/mabe/up-me.html',
		'http://bioengr.ag.utk.edu/', 'http://www.eecs.utk.edu/', 'http://www.engr.utk.edu/ie/', 'http://www.engr.utk.edu/nuclear/');
$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'); //replace with date("l");
$times = array('9:00 AM', '9:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM', '1:00 PM', '1:30 PM', '2:00 PM', '2:30 PM', '3:00 PM', '3:30 PM', '4:00 PM', '4:30 PM', '5:00 PM', '5:30 PM', '6:00 PM');

IF(!isset($_GET['year'])){
    $_GET['year'] = date("Y", $timestamp);
}
IF(!isset($_GET['month'])){
    $_GET['month'] = date("n", $timestamp);
}
if(!isset($_GET['day'])){
	$_GET['day'] = '-1';
}

$month = $_GET['month'];
$year = $_GET['year'];
$day = $_GET['day'];

$tourData = file('/home/coeamb/public_html/logs/tourlogdata.txt');

$todays_date = date("j", $timestamp);
$todays_month = date("n", $timestamp);
$todays_year = date("Y", $timestamp);

$days_in_month = date ("t", mktime(0,0,0,$month,1,$year));
$first_day_of_month = date ("w", mktime(0,0,0,$month,1,$year));
$first_day_of_month = $first_day_of_month + 1;
$count_boxes = 0;
$days_so_far = 0;

$prev_day = ($day!=-1) ? date("j", mktime(0,0,0,$month,$day-1,$year)) : -1;
$next_day = ($day!=-1) ? date("j", mktime(0,0,0,$month,$day+1,$year)) : -1;
$prev_month = ($day!=-1) ? date("n", mktime(0,0,0,$month,$day-1,$year)) : date("n", mktime(0,0,0,$month-1,1,$year));
$next_month = ($day!=-1) ? date("n", mktime(0,0,0,$month,$day+1,$year)) : date("n", mktime(0,0,0,$month+1,1,$year));
$prev_year = ($day!=-1) ? date("Y", mktime(0,0,0,$month,$day-1,$year)) : date("Y", mktime(0,0,0,$month-1,1,$year));
$next_year = ($day!=-1) ? date("Y", mktime(0,0,0,$month,$day+1,$year)) : date("Y", mktime(0,0,0,$month+1,1,$year));

$noToursUntil = mktime(0,0,0,$todays_month,$todays_date+7,$todays_year);

if(isset($_COOKIE['userid']))
	{
	$userid = $_COOKIE['userid'];
	//echo "userid:$userid\n";
	}
else
	{
	die("This site uses cookies in order to function properly. If you have cookies disabled, please enable them for this site. Otherwise, contact the webmaster at jclark74 (at) utk.edu. Thank you.\n");
	}

if(isset($_COOKIE['majors']))
	{
	$majors = $_COOKIE['majors'];
	//echo "majors:$majors\n";
	}
else
	{
	die("This site uses cookies in order to function properly. If you have cookies disabled, please enable them for this site. Otherwise, contact the webmaster at jclark74 (at) utk.edu. Thank you.\n");
	}

if(isset($_COOKIE['similarMajors']))
	{
	$similarMajorsEnabled = $_COOKIE['similarMajors'];
	//echo "similarMajors:$similarMajorsEnabled\n";
	}
else
	{
	die("This site uses cookies in order to function properly. If you have cookies disabled, please enable them for this site. Otherwise, contact the webmaster at jclark74 (at) utk.edu. Thank you.\n");
	}

if(isset($_COOKIE['undecided']))
	{
	$undecided = $_COOKIE['undecided'];
	//echo "undecided:$undecided\n";
	}
else
	{
	die("This site uses cookies in order to function properly. If you have cookies disabled, please enable them for this site. Otherwise, contact the webmaster at jclark74 (at) utk.edu. Thank you.\n");
	}

//this is ugly as shit but it's the best way to do this as of right now. I'll probably want to move this to a different section later on so that it doesnt have to run this here.
//Maybe if I did this command every time someone updated their schedule, and then stored the values in a file it would be better *************************************************
//somehow figure out how to script this based on information given in their profile

$tourTimes = array( /*also this array should be scripted just so it is easier to view/edit*/
	array('aerospace' => str_repeat('0',17), 'biomedical' => str_repeat('0',17), 'biosystems' => str_repeat('0',17), 'chemical' => str_repeat('0',17), 'civil' => str_repeat('0',17), 'computer' => str_repeat('0',17), 'electrical' => str_repeat('0',17)
		, 'environmental' => str_repeat('0',17), 'industrial' => str_repeat('0',17), 'material' => str_repeat('0',17), 'mechanical' => str_repeat('0',17), 'nuclear' => str_repeat('0',17)), 
	array('aerospace' => str_repeat('0',17), 'biomedical' => str_repeat('0',17), 'biosystems' => str_repeat('0',17), 'chemical' => str_repeat('0',17), 'civil' => str_repeat('0',17), 'computer' => str_repeat('0',17), 'electrical' => str_repeat('0',17)
		, 'environmental' => str_repeat('0',17), 'industrial' => str_repeat('0',17), 'material' => str_repeat('0',17), 'mechanical' => str_repeat('0',17), 'nuclear' => str_repeat('0',17)),
	array('aerospace' => str_repeat('0',17), 'biomedical' => str_repeat('0',17), 'biosystems' => str_repeat('0',17), 'chemical' => str_repeat('0',17), 'civil' => str_repeat('0',17), 'computer' => str_repeat('0',17), 'electrical' => str_repeat('0',17)
		, 'environmental' => str_repeat('0',17), 'industrial' => str_repeat('0',17), 'material' => str_repeat('0',17), 'mechanical' => str_repeat('0',17), 'nuclear' => str_repeat('0',17)), 
	array('aerospace' => str_repeat('0',17), 'biomedical' => str_repeat('0',17), 'biosystems' => str_repeat('0',17), 'chemical' => str_repeat('0',17), 'civil' => str_repeat('0',17), 'computer' => str_repeat('0',17), 'electrical' => str_repeat('0',17)
		, 'environmental' => str_repeat('0',17), 'industrial' => str_repeat('0',17), 'material' => str_repeat('0',17), 'mechanical' => str_repeat('0',17), 'nuclear' => str_repeat('0',17)), 
	array('aerospace' => str_repeat('0',17), 'biomedical' => str_repeat('0',17), 'biosystems' => str_repeat('0',17), 'chemical' => str_repeat('0',17), 'civil' => str_repeat('0',17), 'computer' => str_repeat('0',17), 'electrical' => str_repeat('0',17)
		, 'environmental' => str_repeat('0',17), 'industrial' => str_repeat('0',17), 'material' => str_repeat('0',17), 'mechanical' => str_repeat('0',17), 'nuclear' => str_repeat('0',17)), 
	array('aerospace' => str_repeat('0',17), 'biomedical' => str_repeat('0',17), 'biosystems' => str_repeat('0',17), 'chemical' => str_repeat('0',17), 'civil' => str_repeat('0',17), 'computer' => str_repeat('0',17), 'electrical' => str_repeat('0',17)
		, 'environmental' => str_repeat('0',17), 'industrial' => str_repeat('0',17), 'material' => str_repeat('0',17), 'mechanical' => str_repeat('0',17), 'nuclear' => str_repeat('0',17)), 
	array('aerospace' => str_repeat('0',17), 'biomedical' => str_repeat('0',17), 'biosystems' => str_repeat('0',17), 'chemical' => str_repeat('0',17), 'civil' => str_repeat('0',17), 'computer' => str_repeat('0',17), 'electrical' => str_repeat('0',17)
		, 'environmental' => str_repeat('0',17), 'industrial' => str_repeat('0',17), 'material' => str_repeat('0',17), 'mechanical' => str_repeat('0',17), 'nuclear' => str_repeat('0',17))
);
$ambTimes = array( /*this one too*/
	array('jclark74' => str_repeat('0',17), 'jbryan5' => str_repeat('0',17), 'dwoods4' => str_repeat('0',17), 'ssathana' => str_repeat('0',17), 'lcrabtr3' => str_repeat('0',17), 'jharri46' => str_repeat('0',17)
		, 'gconklin' => str_repeat('0',17), 'cdavis75' => str_repeat('0',17), 'kpeay' => str_repeat('0',17), 'abunch' => str_repeat('0',17), 'athoma47' => str_repeat('0',17), 'chunter8' => str_repeat('0',17)
		, 'csliger' => str_repeat('0',17), 'sfly' => str_repeat('0',17), 'rkidd2' => str_repeat('0',17), 'eleturno' => str_repeat('0',17), 'emorin' => str_repeat('0',17), 'bprimm' => str_repeat('0',17)
		, 'trowe2' => str_repeat('0',17), 'jscobey1' => str_repeat('0',17), 'sstrick9' => str_repeat('0',17), 'btaylo38' => str_repeat('0',17)),
	array('jclark74' => str_repeat('0',17), 'jbryan5' => str_repeat('0',17), 'dwoods4' => str_repeat('0',17), 'ssathana' => str_repeat('0',17), 'lcrabtr3' => str_repeat('0',17), 'jharri46' => str_repeat('0',17)
		, 'gconklin' => str_repeat('0',17), 'cdavis75' => str_repeat('0',17), 'kpeay' => str_repeat('0',17), 'abunch' => str_repeat('0',17), 'athoma47' => str_repeat('0',17), 'chunter8' => str_repeat('0',17)
		, 'csliger' => str_repeat('0',17), 'sfly' => str_repeat('0',17), 'rkidd2' => str_repeat('0',17), 'eleturno' => str_repeat('0',17), 'emorin' => str_repeat('0',17), 'bprimm' => str_repeat('0',17)
		, 'trowe2' => str_repeat('0',17), 'jscobey1' => str_repeat('0',17), 'sstrick9' => str_repeat('0',17), 'btaylo38' => str_repeat('0',17)),
	array('jclark74' => str_repeat('0',17), 'jbryan5' => str_repeat('0',17), 'dwoods4' => str_repeat('0',17), 'ssathana' => str_repeat('0',17), 'lcrabtr3' => str_repeat('0',17), 'jharri46' => str_repeat('0',17)
		, 'gconklin' => str_repeat('0',17), 'cdavis75' => str_repeat('0',17), 'kpeay' => str_repeat('0',17), 'abunch' => str_repeat('0',17), 'athoma47' => str_repeat('0',17), 'chunter8' => str_repeat('0',17)
		, 'csliger' => str_repeat('0',17), 'sfly' => str_repeat('0',17), 'rkidd2' => str_repeat('0',17), 'eleturno' => str_repeat('0',17), 'emorin' => str_repeat('0',17), 'bprimm' => str_repeat('0',17)
		, 'trowe2' => str_repeat('0',17), 'jscobey1' => str_repeat('0',17), 'sstrick9' => str_repeat('0',17), 'btaylo38' => str_repeat('0',17)),
	array('jclark74' => str_repeat('0',17), 'jbryan5' => str_repeat('0',17), 'dwoods4' => str_repeat('0',17), 'ssathana' => str_repeat('0',17), 'lcrabtr3' => str_repeat('0',17), 'jharri46' => str_repeat('0',17)
		, 'gconklin' => str_repeat('0',17), 'cdavis75' => str_repeat('0',17), 'kpeay' => str_repeat('0',17), 'abunch' => str_repeat('0',17), 'athoma47' => str_repeat('0',17), 'chunter8' => str_repeat('0',17)
		, 'csliger' => str_repeat('0',17), 'sfly' => str_repeat('0',17), 'rkidd2' => str_repeat('0',17), 'eleturno' => str_repeat('0',17), 'emorin' => str_repeat('0',17), 'bprimm' => str_repeat('0',17)
		, 'trowe2' => str_repeat('0',17), 'jscobey1' => str_repeat('0',17), 'sstrick9' => str_repeat('0',17), 'btaylo38' => str_repeat('0',17)),
	array('jclark74' => str_repeat('0',17), 'jbryan5' => str_repeat('0',17), 'dwoods4' => str_repeat('0',17), 'ssathana' => str_repeat('0',17), 'lcrabtr3' => str_repeat('0',17), 'jharri46' => str_repeat('0',17)
		, 'gconklin' => str_repeat('0',17), 'cdavis75' => str_repeat('0',17), 'kpeay' => str_repeat('0',17), 'abunch' => str_repeat('0',17), 'athoma47' => str_repeat('0',17), 'chunter8' => str_repeat('0',17)
		, 'csliger' => str_repeat('0',17), 'sfly' => str_repeat('0',17), 'rkidd2' => str_repeat('0',17), 'eleturno' => str_repeat('0',17), 'emorin' => str_repeat('0',17), 'bprimm' => str_repeat('0',17)
		, 'trowe2' => str_repeat('0',17), 'jscobey1' => str_repeat('0',17), 'sstrick9' => str_repeat('0',17), 'btaylo38' => str_repeat('0',17)),
	array('jclark74' => str_repeat('0',17), 'jbryan5' => str_repeat('0',17), 'dwoods4' => str_repeat('0',17), 'ssathana' => str_repeat('0',17), 'lcrabtr3' => str_repeat('0',17), 'jharri46' => str_repeat('0',17)
		, 'gconklin' => str_repeat('0',17), 'cdavis75' => str_repeat('0',17), 'kpeay' => str_repeat('0',17), 'abunch' => str_repeat('0',17), 'athoma47' => str_repeat('0',17), 'chunter8' => str_repeat('0',17)
		, 'csliger' => str_repeat('0',17), 'sfly' => str_repeat('0',17), 'rkidd2' => str_repeat('0',17), 'eleturno' => str_repeat('0',17), 'emorin' => str_repeat('0',17), 'bprimm' => str_repeat('0',17)
		, 'trowe2' => str_repeat('0',17), 'jscobey1' => str_repeat('0',17), 'sstrick9' => str_repeat('0',17), 'btaylo38' => str_repeat('0',17)),
	array('jclark74' => str_repeat('0',17), 'jbryan5' => str_repeat('0',17), 'dwoods4' => str_repeat('0',17), 'ssathana' => str_repeat('0',17), 'lcrabtr3' => str_repeat('0',17), 'jharri46' => str_repeat('0',17)
		, 'gconklin' => str_repeat('0',17), 'cdavis75' => str_repeat('0',17), 'kpeay' => str_repeat('0',17), 'abunch' => str_repeat('0',17), 'athoma47' => str_repeat('0',17), 'chunter8' => str_repeat('0',17)
		, 'csliger' => str_repeat('0',17), 'sfly' => str_repeat('0',17), 'rkidd2' => str_repeat('0',17), 'eleturno' => str_repeat('0',17), 'emorin' => str_repeat('0',17), 'bprimm' => str_repeat('0',17)
		, 'trowe2' => str_repeat('0',17), 'jscobey1' => str_repeat('0',17), 'sstrick9' => str_repeat('0',17), 'btaylo38' => str_repeat('0',17)),
);


$files = array();
$majorTimes = str_repeat('0', strlen($majors));
exec('ls /home/coeamb/public_html/schedules/', $files, $returnVal);
foreach($files as $file){
  $hourData = file("/home/coeamb/public_html/schedules/$file");
  $file = strstr($file,'.', true);
  $major = $_ambInfo["$file"];
  for($i=0;$i<5;$i++){
    for($j=0;$j<17;$j++){
      $x=$i+1;
      if($hourData["$i"]["$j"]=='1'){
	 $tourTimes["$x"]["$major"]["$j"]='1';
	 $majorTimes[$_engLookup[$major]]='1';
      }
      $ambTimes["$x"]["$file"]["$j"] = $hourData["$i"]["$j"];
    }
  }
}

require_once('/home/coeamb/public_html/calendar/holidays.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>PHPCalendar</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function SetCookie(cookieName, cookieData, expireDate) {
        document.cookie = cookieName + "=" + escape(cookieData) + "; expires=" + expireDate.toGMTString()+"; path=/; domain=web.utk.edu";
}  
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
function setCharAt(str,index,chr) {
	if(index > str.length-1) return str;
	return str.substr(0,index) + chr + str.substr(index+1);
}
function setMajor(number){
  var cookieData = readCookie('majors');
  if(cookieData.charAt(number)=='0'){
    cookieData = setCharAt(cookieData, number, '1');
  } else {
    cookieData = setCharAt(cookieData, number, '0');
  }
  var date = new Date();
  date.setTime(date.getTime()+24*60*60*1000);
  SetCookie('majors', cookieData, date);
  location.reload();
}
function setUndecided(){
  var cookieData = readCookie('undecided');
  if(cookieData=='0'){
    cookieData = '1';
  } else {
    cookieData = '0';
  }
  var date = new Date();
  date.setTime(date.getTime()+24*60*60*1000);
  SetCookie('undecided', cookieData, date);
  location.reload();
}
function confirmTour(tourid,time,major,ambs){
  var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  var engs = ['an Aerospace Engineering', 'a Chemical Engineering', 'a Computer Science', 'a Materials Science', 'a Biomedical Engineering', 'a Civil Engineering',
	'an Environmental Engineering', 'a Mechanical Engineering', 'a Biosystems Engineering', 'a Computer Engineering', 'an Industrial and Information Engineering', 'a Nuclear Engineering', 'an Undecided Engineering'];
  var tempDate = new Date();
  var offset = tempDate.getTimezoneOffset()*60*1000;
  var knoxTime = 5*60*60*1000;

  var dstStart = new Date();
  dstStart.setMonth(2);
  dstStart.setDate(1);
  var day = dstStart.getDay();
  if(day == 0){
    dstStart.setDate(15);
  } else {
    dstStart.setDate(15 - day);
  }
  var dstEnd = new Date();
  dstEnd.setMonth(10);
  dstEnd.setDate(1);
  day = dstEnd.getDay();
  if(day == 0){
    dstEnd.setDate(1);
  } else {
    dstEnd.setDate(8 - day);
  }
  var dstOffset = 0;
  if((tempDate >= dstStart) && (tempDate <= dstEnd)){
    dstOffset = 60*60*1000;
  }

  var myDate = new Date(parseInt(time) + offset - knoxTime + dstOffset);
  var newDate = new Date(parseInt(time) + offset - knoxTime + dstOffset);
  var actualDate = new Date(parseInt(time));
  newDate.setHours(newDate.getHours()+1);
  var ampm1 = " A.M.", ampm2 = " A.M.";
  if(myDate.getHours() > 11){
    ampm1 = ampm2 = " P.M.";
  }
  if(myDate.getHours() == 11){
    ampm2 = " P.M.";
  }
  var hour1 = myDate.getHours() % 12, hour2 = newDate.getHours() % 12, mins = myDate.getMinutes();
  if(hour1 == 0) {hour1 = 12};
  if(hour2 == 0) {hour2 = 12};
  var answer = confirm("You have selected "+engs[major]+" tour on "+months[myDate.getMonth()]+" "+myDate.getDate()+", "+myDate.getFullYear()+" from "+hour1+":"+((mins==0)?'00':'30')+ampm1+" to "+hour2+":"+((mins==0)?'00':'30')+ampm2+" Is this correct?");
  if(answer){
	var date = new Date();
 	date.setTime(date.getTime()+24*60*60*1000);
	var string = "tourID="+tourid+";tourTime="+(parseInt(actualDate.getTime())/1000)+";major="+major+";ambs="+ambs;
	SetCookie('tour',string,date);
	alert("Please click the 'Submit' button at the bottom of the page to submit your tour request.");
	//window.location = 'index.php';
  }
}

function enableSimilarMajors(newStatus){
  var cookieData = readCookie('similarMajors');
  if(cookieData.charAt(0)=='0'){
    cookieData = setCharAt(cookieData, 0, '1');
  } else {
    cookieData = setCharAt(cookieData, 0, '0');
  }
  var date = new Date();
  date.setTime(date.getTime()+24*60*60*1000);
  SetCookie('similarMajors', cookieData, date);
  location.reload();
}

function jumpToMonth(year, day){
  var obj = document.getElementById('pickAMonth');
  var index = obj.selectedIndex + 1;
  location.href = 'index.php?year='+year+'&month='+index+'&day='+day;
}

function jumpToYear(month, day){
  var obj = document.getElementById('pickAYear');
  var index = obj.selectedIndex;
  var newYear = obj.options[index].value;
  location.href = 'index.php?year='+newYear+'&month='+month+'&day='+day;
}

function jumpToDay(year, month){
  var obj = document.getElementById('pickADay');
  var index = obj.selectedIndex + 1;
  location.href = 'index.php?year='+year+'&month='+month+'&day='+index;
}

//-->
</script>
</head>
<body>
<form>
<table width="<? echo 7*$colWidth ?>" "border="1" align="center" cellpadding="0" cellspacing="0">
  <tr width="100%" border="1">
    <td align="left" colspan="4" class="dayboxes" border="1"><p><span style="font-weight: bold;" >Interested
	Areas of Study (check as many as applicable):</span><br />

	Tours are given on weekdays only by the College of Engineering Student Ambassadors. When you select a major from the list below, 
	the tour times with an Ambassador from that major department are added to the calendar.  To sign up for a tour, please click on a time.  
	For more information about a specific Engineering major, click on the major.</p>
    </td>
  </tr>
  <tr width="100%" border="1" cellpadding="0" cellspacing="0">
    <?
    for($i=0;$i<3;$i++){ //somehow work in the existing tours in here too
      echo "<tr>\n";
      for($j=0;$j<4;$j++){
	 $a=($i*4)+$j;
	 $check = ($majors["$a"]=="1")?'CHECKED':'';
        echo "<td><input value=\"yes\" name=\"$_engTypes[$a]\" type=\"checkbox\" $check onclick=\"setMajor($a)\" class=\"dayboxes\" border=\"1\"><a href=\"$websites[$a]\" target=\"_blank\">$_engNames[$a]</a></td>\n";
      }
      echo "</tr>\n";
    }
    ?>
  </tr>
  <tr width="100%" border="1" cellpadding="0" cellspacing="0">
    <td align="left" colspan="4" class="dayboxes" border="1">
      <p><b><input value="yes" name="undecided" type="checkbox" <?echo ($undecided=='1'?'CHECKED':''); ?> class="dayboxes" border="1" onclick="setUndecided()" >Undecided Engineering:</b><br />&nbsp;Select Undecided Engineering if you have no preference on the major covered by the tour.</p>
    </td>
  </tr>
  <tr>
	<td align="left" colspan="4"><p><input value="yes" name="similarMajorsEnabled" type="checkbox" <?php echo (($similarMajorsEnabled)?'CHECKED':'') ?> onclick="enableSimilarMajors('<?php echo !$similarMajorsEnabled ?>')" class="dayboxes">
		<b>Search for similar majors</b> - Recommends majors that are similar to the ones selected above. Recommended majors show up on the calendar in italics. (This is useful if there are not many tours available for a certain major.)</input></td>
  </tr>
</table>
<br />

<?php

//easier way of doing similar majors
if($similarMajorsEnabled == 1){ //if similar majors are enabled, put them on the calendar, no need to say whether or not that specific major is available
  $tempMajors = $majors;
  $i=0;
  while($i<strlen($majors)){
    if($majors["$i"] == '1'){
	$simArray = split(',',$_similarMajors[$_engTypes["$i"]]);
	foreach($simArray as $similar){
	  $tempMajors[$_engLookup["$similar"]] = '1';
	}
    }
    $i++;
  }
  $majors = $tempMajors;
} else { //otherwise, let the user know if a specific major is not available, and suggest similar majors
  $i=0;
  while($i<strlen($majors)){
    if(($majors["$i"]=='1')&&($majorTimes["$i"]=='0')){
      $sims = split(',',$_similarMajors[$_engTypes["$i"]]);
      $suggestedString = '';
      $suggestedMajors = '';
      $suggApp = '';
      $countMax = 0;
      $countLoop = 0;
      foreach($sims as $similar){
        $countMax++;
      }
      foreach($sims as $similar){
        $suggestedString .= (($countMax > 2)?$suggApp:' ').((($countMax-1 == $countLoop)&&($countMax>1))?' and ':'').$_fancyFullNames["$similar"];
        $suggestedMajors .= (($countMax > 2)?$suggApp:' ').((($countMax-1 == $countLoop)&&($countMax>1))?' or ':'').$_fancyFullNames["$similar"];
        $suggApp = ', ';
        $countLoop++;
      }
      if($countMax > 1){
        $suggestedString .= ' are ';
      } else {
        $suggestedString .= ' is ';
      }
      echo "<p><span style=\"color:#FF0000\"><b>You have selected a tour in ".$_engFullNames["$i"].". Unfortunately, we do not currently have any Ambassadors majoring in ".$_engFullNames["$i"].". ";
      echo "$suggestedString similar to ".$_engFullNames["$i"].". Try looking for a tour in $suggestedMajors.</b></span></p>\n";
    }
    $i++;
  }
}

if($day=='-1'){
?>

<div align="center"><span class="currentdate"><? echo date ("F Y", mktime(0,0,0,$month,1,$year)); ?></span><br>
</div>
<div align="center"><br>
  <table width="<?php echo 7*$colWidth ?>" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td>
        <div align="right">
          <a href="<? echo "index.php?month=$prev_month&amp;year=$prev_year&amp;day=$day"; ?>">&lt;&lt;&nbsp;<? echo date("M Y", mktime(0,0,0,$prev_month,1,$prev_year)) ?></a>
        </div>
      </td>
      <td width="200">
	 <div align="center">
	   <select name="pickAMonth" id="pickAMonth" size="1" onchange="javascript:jumpToMonth(<?=$year ?>, <?=$day ?>);">
<?php
for($i=1;$i<=12;$i++){
  $mDate = mktime(0,0,0,$i,1,2010);
  echo "\t     <option value=\"$i\" ".(($month==$i)?'selected="SELECTED"':'').">".date("F",$mDate)."</option>\n";
}
?>
	   </select>
	   <select name="pickAYear" id="pickAYear" size="1" onchange="javascript:jumpToYear(<?=$month ?>, <?=$day ?>);">
	     <option value="<?=$todays_year ?>" selected="SELECTED"><?=$todays_year ?></option>
	   </select>
        </div>
      </td>
      <td>
        <div align="left">
          <a href="<? echo "index.php?month=$next_month&amp;year=$next_year&amp;day=$day"; ?>"><? echo date("M Y", mktime(0,0,0,$next_month,1,$next_year)) ?>&nbsp;&gt;&gt;</a>
        </div>
      </td>
    </tr>
  </table>
  <br>
</div>

<table width="<?php echo 7*$colWidth ?>" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#000000">
  <tr>
    <td><table width="100%" border="1" cellpadding="0" cellspacing="0">
        <tr class="topdays"> 
      	   <td><div align="center">Sunday</div></td>
          <td><div align="center">Monday</div></td>
          <td><div align="center">Tuesday</div></td>
      	   <td><div align="center">Wednesday</div></td>
          <td><div align="center">Thursday</div></td>
      	   <td><div align="center">Friday</div></td>
          <td><div align="center">Saturday</div></td>
        </tr>
		<tr valign="top" bgcolor="#FFFFFF"> 
		<?
		for ($i = 1; $i <= $first_day_of_month-1; $i++) {
			$days_so_far = $days_so_far + 1;
			$count_boxes = $count_boxes + 1;
			echo "<td width=\"$colWidth\" height=\"$colWidth\" class=\"beforedayboxes\" border=\"1\"></td>\n";
		}
		for ($i = 1; $i <= $days_in_month; $i++) {
   			$days_so_far = $days_so_far + 1;
    			$count_boxes = $count_boxes + 1;
			$numEvents=0;
			$stopEvents=0;
			$printedMajor=array();
			foreach($_engTypes as $type){
				$printedMajor["$type"] = 0;
			}
			IF($month == $todays_month){
				IF($i == $todays_date){
					$class = "highlighteddayboxes";
				} ELSE {
					$class = "dayboxes";
				}
			} ELSE {
				IF($i == 1){
					$class = "highlighteddayboxes";
				} ELSE {
					$class = "dayboxes";
				}
			}
			echo "<td width=\"$colWidth\" height=\"$colWidth\" class=\"$class\" border=\"1\">\n";
			echo "<div align=\"right\"><span class=\"toprightnumber\">\n<a href=\"index.php?month=$month&amp;year=$year&amp;day=$i\">$i</a>&nbsp;</span></div>\n";
			foreach($tourData as $tour){
				list($u,$tourID,$tourtime,$major,$ms,$na,$pa,$num,$sc,$y,$c,$st,$e,$ph,$ambs,$status) = explode(';',$tour,16);
				if($tourtime < $timestamp){
					continue;
				}
				if(($status == -1) && (date('dmY',$tourtime) == date('dmY',mktime(0,0,0,$month,$i,$year))) && (($majors["$major"] == '1'))){
					if($numEvents<$maxEventsShown){
						$text = substr('Tour - '.$_engNames[$major],0,10);
						if($text!='Tour - '.$_engNames[$major]){
							$text .= '...';
						}
						echo "&nbsp;<a href=\"index.php?month=$month&amp;year=$year&amp;day=$i\" title=\"Tour - $_engNames[$major]\">$text</a>\n<br>\n";
						$printedMajor[$_engTypes[$major]] = 1;
						$numEvents++;
					} elseif($numEvents==$maxEventsShown){
						if($stopEvents==0){
							echo "&nbsp;<a href=\"index.php?month=$month&amp;year=$year&amp;day=$i\">...</a>\n<br>\n";
							$stopEvents=1;
						}
					}
				}
			}
			echo "<div align=\"left\"><span class=\"eventinbox\">\n";
			$isAHoliday=0;
			foreach($_holidayList as $holiday){
				if(($holiday[0] == $month) && ($holiday[1] == $i) && ($holiday[2] == $year)){
					$isAHoliday = 1;
					break;
				}
			}
			$breakStart = mktime(0,0,0,$_breakList[0][0],$_breakList[0][1],$_breakList[0][2]);
			$breakEnd = mktime(0,0,0,$_breakList[1][0],$_breakList[1][1],$_breakList[1][2]);
			$calcDate = mktime(0,0,0,$month,$i,$year);
			if(($calcDate >= $breakStart) && ($calcData <= $breakEnd)){
				$isAHoliday = 1;
			}
			if(!$isAHoliday){
				$dayOffset = ($i+$first_day_of_month-2)%7;
				if($undecided=='1'){
					$tempDate = mktime(0,0,0,$month, $i,$year);
					$somethingToday=0;
					$blankMajor = str_repeat('0',17);
					foreach($tourTimes[$dayOffset] as $major => $timeSlots){
						if($timeSlots != $blankMajor){
							$somethingToday = 1;
						}
					}
					if(($tempDate >= $noToursUntil) && ($somethingToday==1)){
						echo "&nbsp;<a href=\"index.php?month=$month&amp;year=$year&amp;day=$i\" title=\"Undecided Engineering\">Undecided Engineering</a>\n<br>\n";
					}
				} else {
					foreach($tourTimes[$dayOffset] as $major => $timeSlots){
						if(mktime(0,0,0,$month, $i,$year) >= $noToursUntil){
							$similarMajorFound = 0;
							$posArray = array();
							$strPos = strpos($majors, '1');
							while($strPos !== FALSE){
								$posArray[] = $_engTypes["$strPos"];
								$strPos = strpos($majors, '1', $strPos+1);
							}
							for($x=0;$x<17;$x++){
								if(($timeSlots["$x"]=='1') && ($majors[array_search($major, $_engTypes, true)]=='1')){
									if($printedMajor["$major"] == 1){
										continue;
									}
									if($numEvents<$maxEventsShown){
										$text = substr($_fancyNames[$major],0,12);
										if($text!=$_fancyNames[$major]){
											$text .= '...';
										}
										echo "&nbsp;<a href=\"index.php?month=$month&amp;year=$year&amp;day=$i\" title=\"$_fancyNames[$major]\">$text</a>\n<br>\n";
										$printedMajor["$major"] = 1;
										$numEvents++;
									} elseif($numEvents==$maxEventsShown){
										if($stopEvents==0){
											echo "&nbsp;<a href=\"index.php?month=$month&amp;year=$year&amp;day=$i\">...</a>\n<br>\n";
											$stopEvents=1;
										}
									} 
								}
							}
						}
					}
				}
			}
			echo "</span></div>\n";

			echo "</td>\n";
			IF(($count_boxes == 7) AND ($days_so_far != (($first_day_of_month-1) + $days_in_month))){
				$count_boxes = 0;
				echo "</TR><TR valign=\"top\">\n";
			}
		}
		$extra_boxes = 7 - $count_boxes;
		for ($i = 1; $i <= $extra_boxes; $i++) {
			echo "<td width=\"$colWidth\" height=\"$colWidth\" class=\"afterdayboxes\" border=\"1\"></td>\n";
		}
		?>
        </tr>
      </table></td>
  </tr>
</table>
<br />
<?
} else {
?>
<div align="center"><span class="currentdate"><? echo date("l, F jS, Y", mktime(0,0,0,$month,$day,$year)); ?></span><br>
</div>
<div align="center"><br>
  <table width="<?php echo 7*$colWidth ?>" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td>
	 <div align="right">
	   <a href="<? echo "index.php?month=$prev_month&amp;year=$prev_year&amp;day=$prev_day"; ?>">&lt;&lt;&nbsp;<? echo date("M j", mktime(0,0,0,$prev_month,$prev_day,$prev_year)) ?></a>
	 </div>
      </td>
      <td width="200">
        <div align="center">
	   <select name="pickAMonth" id="pickAMonth" size="1" onchange="javascript:jumpToMonth(<?=$year ?>, <?=$day ?>);">
<?php
//make it so the javascript refreshes the page every time the user changes the month
for($i=1;$i<=12;$i++){
  $mDate = mktime(0,0,0,$i,1,2010);
  echo "\t     <option value=\"$i\" ".(($month==$i)?'selected="SELECTED"':'').">".date("F",$mDate)."</option>\n";
}
?>
	   </select>
	   <select name="pickADay" id="pickADay" size="1" onchange="javascript:jumpToDay(<?=$year ?>, <?=$month ?>);">
<?php
$numDays = date("t", mktime(0,0,0,$month,1,$year));
for($i=1;$i<=$numDays;$i++){
  echo "\t     <option value=\"$i\" ".((($day==$i)&&($month==$month))?'selected="SELECTED"':'').">$i</option>\n";
}
?>
	   </select>
        </div>
      </td>
      <td>
	 <div align="left">
	   <a href="<? echo "index.php?month=$next_month&amp;year=$next_year&amp;day=$next_day"; ?>"><? echo date("M j", mktime(0,0,0,$next_month,$next_day,$next_year)) ?>&nbsp;&gt;&gt;</a>
	 </div>
      </td>
    </tr>
  </table>
  <br>
</div>
<table width="<? echo 7*$colWidth ?>" border="1" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="center" width="25%">Time</td>
    <td align="center">Description</td>
  </tr>
<?php
	//read in the log files and determine who needs the tours the most, and which person has similar majors if that option has been selected
	$tempTourCountInfo = array();
	$tempMajorCountInfo = array();
	$ambSize = count($_ambLookup); //gets the number of ambassadors
	$majorSize = count($_engTypes); //gets the number of majors
	$tourCountInfo = array_pad($tempTourCountInfo, $ambSize+1, 0);
	$majorMaxInfo = array_pad($tempMajorCountInfo, $majorSize+1, 0);
	$majorAvgInfo = array_pad($tempMajorCountInfo, $majorSize+1, 0);
	$majorCountInfo = array_pad($tempMajorCountInfo, $majorSize+1, 0);
	$needsATour = array_pad($tempTourCountInfo, $ambSize+1, 0);
	$kindaNeedsATour = array_pad($tempTourCountInfo, $ambSize+1, 0);
	$isSameMajor = array_pad($tempTourCountInfo, $ambSize+1, 0);
	$isSimilarMajor = array_pad($tempTourCountInfo, $ambSize+1, 0);
	foreach($tourData as $tour){
		list($u,$tourID,$tourtime,$major,$ms,$na,$pa,$num,$sc,$y,$c,$st,$e,$ph,$amb,$status) = explode(';',$tour,16);
		if(($status == -1) && (date('dmY',$tourtime) == date('dmY',mktime(0,0,0,$month,$day,$year))) && ($majors["$major"] == '1')){
			if(($tourPrinted == 0) && ($times[$i] == date('g:i A', $tourtime))){
				$tempName = preg_replace('/ Engineering/', '', $_engNames[$major]);
				$string = "Join the previously scheduled $tempName Engineering tour for this time slot.<br />";
				echo "<a href=\"javascript:confirmTour('$tourID','$tourtime".'000'."','$major','$amb');\">$string</a>\n";
				$tourPrinted = 1;
			}
		}
		if($status == -1){
			$tourCountInfo["$amb"]++;
			$majorAvgInfo["$major"]++;
			if($tourCountInfo["$amb"] > $majorMaxInfo["$major"]){
				$majorMaxInfo["$major"] = $tourCountInfo["$amb"];
			}
		}
	}
	for($qwerty = 0; $qwerty<$ambSize; $qwerty++){
		$majorCountInfo[$_engLookup[$_ambInfo[$_ambLookup[$qwerty]]]]++;
	}
	for($qwerty = 0; $qwerty<$ambSize; $qwerty++){
		$majorAvgInfo["$qwerty"] /= $majorCountInfo["$qwerty"]; 
	}
	for($qwerty = 0; $qwerty<$ambSize; $qwerty++){
		if(($tourCountInfo["$qwerty"] < $majorAvgInfo[$_engLookup[$_ambInfo[$_ambLookup[$qwerty]]]]) || ($tourCountInfo["$qwerty"] == 0)){
			$needsATour["$qwerty"] = 1;
			$kindaNeedsATour["$qwerty"] = 1;
		} else if($tourCountInfo["$qwerty"] == $majorAvgInfo[$_engLookup[$_ambInfo[$_ambLookup[$qwerty]]]]){
			$kindaNeedsATour["$qwerty"] = 1;
		} //else both == 0
	}

	$colorSwitch=0;
	$numEvents = array();
	$dailySched = $tourTimes[date("w",mktime(0,0,0,$month,$day,$year))];
	$dailyAmb = $ambTimes[date("w",mktime(0,0,0,$month,$day,$year))];
	for($i=0;$i<17;$i++){
		$color = ((($i+$colorSwitch)%2)==1)?'#ffffff':'#e8eef5';
		$j=$i+2;
		echo "<tr valign=\"top\" bgcolor=\"$color\"><td align=\"center\">$times[$i] - $times[$j]</td>\n";
		$tourPrinted=0;
		$similarPrintedArray=array();
		$similarFound=0;
		echo "<td align=\"left\">&nbsp;";
		foreach($tourData as $tour){
			list($u,$tourID,$tourtime,$major,$ms,$na,$pa,$num,$sc,$y,$c,$st,$e,$ph,$ambs,$status) = explode(';',$tour,16);
			if($tourtime < $timestamp){
				continue;
			}
			if(($status == -1) && (date('dmY',$tourtime) == date('dmY',mktime(0,0,0,$month,$day,$year))) && (($majors["$major"] == '1'))){
				if(($tourPrinted == 0) && ($times[$i] == date('g:i A', $tourtime))){
					$tempName = preg_replace('/ Engineering/', '', $_engNames[$major]);
					$string = (($tourPrinted)?'&nbsp;':'')."Join the previously scheduled $tempName Engineering tour for this time slot.";
					echo "<a href=\"javascript:confirmTour('$tourID','$tourtime".'000'."','$major','$ambs');\" title=\"$string\">$string</a><br />\n";
					$tourPrinted = 1;
				}
			}
		}
		$isAHoliday=0;
		foreach($_holidayList as $holiday){
			if(($holiday[0] == $month) && ($holiday[1] == $day) && ($holiday[2] == $year)){
				$isAHoliday = 1;
				break;
			}
		}
		$breakStart = mktime(0,0,0,$_breakList[0][0],$_breakList[0][1],$_breakList[0][2]);
		$breakEnd = mktime(0,0,0,$_breakList[1][0],$_breakList[1][1],$_breakList[1][2]);
		$calcDate = mktime(0,0,0,$month,$i,$year);
		if(($calcDate >= $breakStart) && ($calcData <= $breakEnd)){
			$isAHoliday = 1;
		}
		if(!$isAHoliday){
			$printedStuff=0;
			$printedAnything=0;
			if($undecided==1){
				if(mktime(0,0,0,$month,$day,$year) >= $noToursUntil){
					$ambArray = array();
					$ambList = '';
					$comma = '';
					foreach($dailyAmb as $amb => $timeSlots){
						if($timeSlots["$i"] == '1'){
							$ambArray[$_ambNums[$amb]] = $tourCountInfo[$_ambNums[$amb]];
						}
					}
					if($ambArray){
						asort($ambArray, SORT_NUMERIC);
						foreach($ambArray as $amb => $numTours){
							$ambList .= $comma.$amb;
							$comma = ',';
						}
						$tourID = $timestamp;
						$hour = floor($i/2)+9;
							$min = ($i%2)*30;
						$myTime = mktime($hour,$min,0,$month,$day,$year)."000";
						echo "<a href=\"javascript:confirmTour('$tourID','$myTime','12','$ambList');\">Undecided Engineering tour available to sign up for!<br /></a>\n";
					}
				}
			} else {
				if(!$tourPrinted){
				foreach($dailySched as $major => $timeSlots){
					if($similarPrintedArray["$major"]){
						continue;
					}
					if(mktime(0,0,0,$month,$day,$year) >= $noToursUntil){
						$ambList='';
						$ambMatch = array();
						foreach($dailyAmb as $amb => $timeSlots2){
							$ambMatchTemp = 0;
							if(($timeSlots2["$i"]=='1') && ($majors[array_search($_ambInfo["$amb"], $_engTypes, true)]=='1')){
								if($major==$_ambInfo["$amb"]){ //same major
									$ambMatchTemp += 25;
								} else if(($similarMajorsEnabled)){ //similar major, work on this later
									$ambMatchTemp += 15;
								} else {
									$ambMatch[$_ambNums[$amb]] = 0;
									continue;
								}
								if($needsATour[$_ambNums[$amb]]){
									$ambMatchTemp += 50;
								} 
								if($kindaNeedsATour[$_ambNums[$amb]]){
									$ambMatchTemp += 20;
								}
							}
							$ambMatch[$_ambNums[$amb]] = $ambMatchTemp;
						}
						arsort($ambMatch, SORT_NUMERIC);
						$append = '';
						foreach($ambMatch as $orderedAmbNum => $value){
							if($value != 0){
								$ambList .= $append.$orderedAmbNum;
								$append = ',';
							}
						}
						if(($timeSlots[$i]=='1') && ($majors[array_search($major, $_engTypes, true)]=='1')){
							$spacer = ($printedAnything == 1)?'&nbsp;':'';
							$name = $_fancyNames["$major"];
							$string = "$spacer$name Engineering tour available to sign up for!<br />";
							$string = preg_replace('/Engineering Engineering/', 'Engineering', $string);
							$printedStuff=1;
							$printedAnything = 1;
						} else {
							$string = '';
						}
						if($printedStuff==1){
							$number = array_search($major,$_engTypes,1);
							$tourID = $timestamp;
							$hour = floor($i/2)+9;
			    				$min = ($i%2)*30;
							$myTime = mktime($hour,$min,0,$month,$day,$year)."000";
							echo "<a href=\"javascript:confirmTour('$tourID','$myTime','$number','$ambList');\">$string</a>\n"; // finish this somehow so that it selects the tour that they picked last
							$similarPrintedArray["$major"] = 1;
							$printedStuff=0;
						}
					}
				}
			}
		}
	}
	echo "</td></tr>\n";
}
?>
</table>
<br />
<div align="right">
  <a href="index.php?month=<?php echo $month ?>&amp;year=<?php echo $year ?>">Return to Month View</a>
</div>
<?
}
?>
</form>
</body>
</html>
