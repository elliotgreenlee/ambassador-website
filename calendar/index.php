<?
/* notes/todo
-possibly work on cookies to make it more robust
-possibly make confirming the date/time less awkward
-work on integrating the undecided engineering stuff into the rest of the code, make it so that the first major selected would show up? (with a note to say that it might change)
-the javascript confirm function will report improper times if DST is in effect in Knoxville and the user is in an area that does not follow DST. Or at least I think it will. There's really no way to check as far as I know
-get rid of that stupid scroll bar, figure out how to dynamically resize the size of the iframe. I guess thats on the tours.php page. but figure something out here.
-update parts of the page to use the $_engFullNames and $_engFancyNames where it would be easier to do so

*/

$fileName = 'index.php';

$timestamp = time();
date_default_timezone_set('America/New_York'); //so that dates will show up as knoxville time instead of time local to the user
$maxEventsShown=4;
$colWidth = 90;
$daysNoticeForTour = 1; //only 1 day's notice for signing up for tours

if(!isset($_GET['year'])){
    $_GET['year'] = date("Y", $timestamp);
}
if(!isset($_GET['month'])){
    $_GET['month'] = date("n", $timestamp);
}
if(!isset($_GET['day'])){
	$_GET['day'] = '-1';
}

$month = $_GET['month'];
$year = $_GET['year'];
$day = $_GET['day'];

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

$following_year = ($day!=-1) ? date("Y", mktime(0,0,0,$month,$day+1,$year)) : date("Y", mktime(0,0,0,$month,1,$year+1));

$noToursUntil = mktime(0,0,0,$todays_month,$todays_date+$daysNoticeForTour,$todays_year);
	
require('/home/coeamb/public_html/mysqlFunctions.php');

require('/home/coeamb/database/dbInfo.php');

$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword)
	or die('Could not connect: '.mysql_error());

mysql_select_db($dbName)
	or die('Could not select database: '.mysql_error());

$tourDates = mysqlQuerySingleRow('SELECT firstDayOfTours, lastDayOfTours from generalInfo');
$startDT = new DateTime($tourDates['firstDayOfTours']);
$endDT = new DateTime($tourDates['lastDayOfTours']);
$firstDayOfTours = $startDT->format('U');
$lastDayOfTours = $endDT->format('U');

//get a list of timestamps of holidays
$holidays = mysqlQueryToListIndexBy('SELECT * FROM holidayInfo', 'holidayDate');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>PHPCalendar</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="calendarScripts.js"></script>
</head>
<body>
<form>

<br />

<?php
 
//if day==-1 then there is no day selected, and we are in the month view

if($day=='-1'){

/**********************************************************************************************
 * 		Month View Section
 * The month view shows whether or not tours are available to be signed up for, only showing if a 
 * tour in each selected major is available. If there is already a tour on that day, then the
 * option to create a new tour is not shown in this view. Most of this logic involves printing out
 * HTML tables in the shape of a calendar. There is also a small section for fast jumping to 
 * different months or years.
 */
?>

<div align="center"><span class="currentdate"><? echo date ("F Y", mktime(0,0,0,$month,1,$year)); ?></span><br>
</div>
<div align="center"><br>
  <table width="<?php echo 7*$colWidth ?>" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td>
        <div align="right">
          <a href="<? echo "$fileName?month=$prev_month&amp;year=$prev_year&amp;day=$day"; ?>">&lt;&lt;&nbsp;<? echo date("M Y", mktime(0,0,0,$prev_month,1,$prev_year)) ?></a>
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
       <?php
for ($N=$todays_year; $N<=2016; $N++) {
    echo "<option";
    if ($N==pickAYear) echo " SELECTED";
    echo ">$N</option>";
    }
    ?> 
  </select>
  
        </div>
      </td>
      <td>
        <div align="left">
          <a href="<? echo "$fileName?month=$next_month&amp;year=$next_year&amp;day=$day"; ?>"><? echo date("M Y", mktime(0,0,0,$next_month,1,$next_year)) ?>&nbsp;&gt;&gt;</a>
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
		$wDayTourTimes = array_fill(0, 7, array());
		$checkedWDay = array_fill(0, 7, False);
		for ($i = 1; $i <= $first_day_of_month-1; $i++) {
			$days_so_far++;
			$count_boxes++;
			echo "<td width=\"$colWidth\" height=\"$colWidth\" class=\"beforedayboxes\" border=\"1\"></td>\n";
		}
		for ($i = 1; $i <= $days_in_month; $i++) {
   			$days_so_far = $days_so_far + 1;
    		$count_boxes = $count_boxes + 1;
			$numEvents=0;
			$stopEvents=0;
			unset($printedMajor);
			$printedMajor = array_fill(0, $majCount, 0);
			$dayOfThisBox = date('dmY',mktime(0,0,0,$month,$i,$year));
			if($month == $todays_month){
				if($i == $todays_date){
					$class = "highlighteddayboxes";
				} else {
					$class = "dayboxes";
				}
			} else {
				if($i == 1){
					$class = "highlighteddayboxes";
				} else {
					$class = "dayboxes";
				}
			}
			echo "<td width=\"$colWidth\" height=\"$colWidth\" class=\"$class\" border=\"1\">\n";
			echo "<div align=\"right\"><span class=\"toprightnumber\">\n<a href=\"$fileName?month=$month&amp;year=$year&amp;day=$i\">$i</a>&nbsp;</span></div>\n";
			
			$currentDate = mktime(0,0,0, $month, $i, $year);
			$dateString = strftime('%Y-%m-%d', $currentDate); //TODO: there's probably a better way to do this but whatever
			if(($currentDate >= $noToursUntil) && ($currentDate >= $firstDayOfTours) && ($currentDate <= $lastDayOfTours) && !isset($holidays[$dateString])){
				$wDay = date("w", $currentDate);
				$safeWDay = intval(mysql_real_escape_string($wDay));
				if(!$checkedWDay["$wDay"]){
					$wDayTourTimes["$wDay"] = mysqlQueryToList("SELECT * FROM scheduleInfo WHERE dayOfWeek='$safeWDay' ORDER BY timeSlot", "Error: ");
					$checkedWDay["$wDay"] = True;
				}
				foreach($wDayTourTimes["$wDay"] as $time){
					$tourTime = date('Y-m-d', $currentDate).' '.$time['timeSlot'];
					$safeTourTime = mysql_real_escape_string($tourTime);
					$res = mysqlQuerySingleRow("SELECT COUNT(id) from tourInfo WHERE tourTime='$safeTourTime'");
					$toursAtThisTime = intval($res['COUNT(id)']);
					if($toursAtThisTime < intval($time['groupsAvailable'])){
						//$tourDT = date_parse_from_format('Y-m-d H:i:s', $tourTime);
						//$tourTimeStamp = mktime($tourDT['hour'], $tourDT['minute'], $tourDT['second'], $tourDT['month'], $tourDT['day'], $tourDT['year']);
                                                //echo $tourTime;
                                                $tourTimeStamp = strtotime($tourTime);
                                                //echo $tourTimeStamp;
/*************************************************************HERE****************************************************/
						if(mktime(13, 0, 0, 4, 4, 2014) == $tourTimeStamp) continue;
						$text = date('g:i A', $tourTimeStamp);
						echo "&nbsp;<a href=\"$fileName?month=$month&amp;year=$year&amp;day=$i\" title=\"Tour at $text\">$text</a>\n<br>\n";
					}
				}
			} else if(isset($holidays[$dateString])) {
				echo '&nbsp;'.$holidays[$dateString]['description'];
			}
			
			echo "<div align=\"left\"><span class=\"eventinbox\">\n";
			echo "</span></div>\n";
			echo "</td>\n";
			if(($count_boxes == 7) AND ($days_so_far != (($first_day_of_month-1) + $days_in_month))){
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

/*
 * 		End of Month View Section
 **********************************************************************************************/
 
//otherwise, a day is selected, and we are in the Day view section
} else {
	
/**********************************************************************************************
 * 		Day View Section
 * The day view shows when tours are available to be signed up for. Unlike in month view, if there
 * is a tour in one major on a day, you can still pick a different time to sign up for a new tour.
 * Most of this logic involves printing out HTML tables in the form of an agenda. There is also a 
 * small section for fast jumping to different days or months.
 */

?>
<div align="center"><span class="currentdate"><? echo date("l, F jS, Y", mktime(0,0,0,$month,$day,$year)); ?></span><br>
</div>
<div align="center"><br>
  <table width="<?php echo 7*$colWidth ?>" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td>
	 <div align="right">
	   <a href="<? echo "$fileName?month=$prev_month&amp;year=$prev_year&amp;day=$prev_day"; ?>">&lt;&lt;&nbsp;<? echo date("M j", mktime(0,0,0,$prev_month,$prev_day,$prev_year)) ?></a>
	 </div>
      </td>
      <td width="200">
        <div align="center">
	   <select name="pickAMonth" id="pickAMonth" size="1" onchange="javascript:jumpToMonth(<?=$year ?>, <?=$day ?>);">
<?php
//the javascript refreshes the page every time the user changes the month
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
	   <a href="<? echo "$fileName?month=$next_month&amp;year=$next_year&amp;day=$next_day"; ?>"><? echo date("M j", mktime(0,0,0,$next_month,$next_day,$next_year)) ?>&nbsp;&gt;&gt;</a>
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
	
	//only run the analysis if we are looking at today or later
	$selectedDayTimestamp = mktime(0,0,0, $month, $day, $year);
	$wDay = date("w", $selectedDayTimestamp);
	$safeWDay = intval(mysql_real_escape_string($wDay));
	$dateString = strftime('%Y-%m-%d', $selectedDayTimestamp);
	if(($selectedDayTimestamp >= $noToursUntil) && ($selectedDayTimestamp >= $firstDayOfTours) && ($selectedDayTimestamp <= $lastDayOfTours) && !isset($holidays[$dateString])){
		$tourTimes = mysqlQueryToList("SELECT * FROM scheduleInfo WHERE dayOfWeek='$safeWDay' ORDER BY timeSlot", "Error: ");
		if(count($tourTimes) == 0){
			echo '<tr><td colspan="2" align="center">Sorry, No Tours Available Today. Please select a different date for a tour.</td></tr>';
		} else {
			foreach($tourTimes as $tour){
				$tourTime = date('Y-m-d ', $selectedDayTimestamp).$tour['timeSlot'];
				//$tourDT = date_parse_from_format('Y-m-d H:i:s', $tourTime);
				//$tourTimeStamp = mktime($tourDT['hour'], $tourDT['minute'], $tourDT['second'], $tourDT['month'], $tourDT['day'], $tourDT['year']);
				//echo $tourTime;
                                $tourTimeStamp = strtotime($tourTime);
                                //echo $tourTimeStamp;
                                $text = date('g:i A', $tourTimeStamp);
				$printDate = date('l, F jS Y', $tourTimeStamp);
				$safeTourTime = mysql_real_escape_string($tourTime);
				$res = mysqlQuerySingleRow("SELECT COUNT(id) FROM tourInfo WHERE tourTime='$safeTourTime'");
				$toursAtThisTime = intval($res['COUNT(id)']);
				$toursAvailable = intval($tour['groupsAvailable']);
				if($toursAtThisTime < $toursAvailable){
					$numSlotsOpen = $toursAvailable - $toursAtThisTime;
					$s = (($numSlotsOpen == 1) ? '':'s');
/********************************************************HERE************************************************/
					if(mktime(13, 0, 0, 4, 4, 2014) == $tourTimeStamp) continue;
					echo "<tr><td align=\"center\">$text</td><td><a href=\"javascript:confirmTourNew('${tourTimeStamp}000', '$text', '$printDate');\">&nbsp;Sign up for a tour at $text! There is still room for $numSlotsOpen tour group$s.</a></td></tr>\n";
				} else {
					echo "<tr><td align=\"center\">$text</td><td>&nbsp;Sorry, but this tour is full. Please select a different time for a tour.</td></tr>\n";
				}
			}
		}
	} elseif($selectedDayTimestamp < $noToursUntil){
		echo '<tr><td colspan="2" align="center">Sorry, but it is too late to sign up for tours on this day. We only allow for tours with at least seven day\'s notice.</td></tr>';
	} elseif($selectedDayTimestamp < $firstDayOfTours){
		//TODO: Do I need this? Tour website will be down until we bring it up. People should not be going back to look for tours.
		echo '<tr><td colspan="2" align="center">Sorry, but it is too late to sign up for tours on this day. We only allow for tours with at least seven day\'s notice.</td></tr>';
	} elseif($selectedDayTimestamp > $lastDayOfTours){
		echo '<tr><td colspan="2" align="center">Sorry, but there will be no tours after the last day of classes. Either select a time before the day that you have selected, or wait until next semester and request a tour then.</td></tr>';
	} elseif(isset($holidays[$dateString])){
		echo '<tr><td colspan="2" align="center">'.$holidays[$dateString]['description'].'</td></tr>';
	}
?>
</table>

<br />
<div align="right">
  <a href="<?=$fileName?>?month=<?php echo $month ?>&amp;year=<?php echo $year ?>">Return to Month View</a>
</div>
<?
/*
 * 		End of Day View Section
 **********************************************************************************************/
}
mysql_close($db);
?>
</form>
</body>
</html>
