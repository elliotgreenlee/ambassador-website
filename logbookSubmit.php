<?
/*

*/

//TODO: Make sure this is includeed on all the *submit pages
include('login.php'); 

$loginInfo = login(); //included from login.php
$userName = $loginInfo['userName'];
$password = $loginInfo['password'];

$lastPage = $_POST['lastPage'];

date_default_timezone_set('America/New_York'); //so that dates will show up as knoxville time instead of time local to the user

function getMYSQLLogResult(){
	$rowsChanged = mysql_affected_rows();
	if($rowsChanged == 0){
		$result = 'ERR: No changes';
	} elseif($rowsChanged == 1){
		$result = 'Log added';
	} elseif($rowsChanged == -1){
		$result = 'ERR: Query Failed';
	} else {
		$result = 'ERR: Inserted multiple logs';
	}
	return $result;
}

//not sure if this is necessary
function returnToLastPage(){
	//here we redirect back to the calendar page
	$lastPage = $_POST['lastPage'];
	$lastPageClean = str_replace(array('/', '\\'), array('', ''), $lastPage); 
	//header("Location: http://web.utk.edu/~coeamb/".$lastPageClean);
	header("Location: http://www.engr.utk.edu/ambassador/$lastPageClean");
	//just to make sure that nothing after this gets executed somehow
	exit();
}

//if(($password == 1) || ($password == 2)){

//<doc>Note: If you don't understand POST/GET, this section is what gets executed if the user clicks one of the buttons at the bottom of the page. The names are pretty self-explanatory.</doc>
switch($_POST['submit'])  
	{
	case 'logout': // logs the user out if the log out button is clicked
		require_once('/home/coeamb/public_html/mysqlFunctions.php');
		require_once('/home/coeamb/database/dbInfo.php');
		$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
		mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
		
		logout($userName); //included from login.php
		break; // logs the user out
	case 'My login info isn\'t working':
		require_once('/home/coeamb/public_html/mysqlFunctions.php');
		require_once('/home/coeamb/database/dbInfo.php');
		$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
		mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
		
		logout($userName, TRUE); //just clears the cookies, lets the user try to log in again
		break;
	//TODO: Why is this here? 11/13/13
	/*case 'Download Tour Log': // downloads the tour log in excel format, new way of doing it
//<doc>Note: This is how the excel logs are created. Check out this package for extended info on how to format the pages. The version on the website is a little older, but has the same functionality.</doc>
//<doc>	http://pear.php.net/package/Spreadsheet_Excel_Writer</doc>
		require_once('excel/Worksheet.php');
		require_once('excel/Workbook.php');
		function HeaderingExcel($filename) {
				header("Content-type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$filename" );
				header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}

		// HTTP headers
		HeaderingExcel('tourlog.xls');

		// Creating a workbook
		$workbook = new Workbook("-");
		$worksheet =& $workbook->add_worksheet('Tour Data');
		$worksheet->write_string(0,1,"Student Name");
		$worksheet->write_string(0,2,"Parent Name");
		$worksheet->write_string(0,3,"Number of Visitors");
		$worksheet->write_string(0,4,"High School");
		$worksheet->write_string(0,5,"School Year");
		$worksheet->write_string(0,6,"City");
		$worksheet->write_string(0,7,"State");
		$worksheet->write_string(0,8,"Email");
		$worksheet->write_string(0,9,"Phone Number");
		$worksheet->write_string(0,10,"Tour Date/Time");
		$worksheet->write_string(0,11,"Major Selected");
		$worksheet->write_string(0,12,"Other Majors Selected");
		$worksheet->write_string(0,13,"Status");

		$worksheet->set_column(0,0,3);
		$worksheet->set_column(1,3,17);
		$worksheet->set_column(4,5,14);
		$worksheet->set_column(6,7,13);
		$worksheet->set_column(8,8,14);
		$worksheet->set_column(9,9,13);
		$worksheet->set_column(10,10,25);
		$worksheet->set_column(11,11,20);
		$worksheet->set_column(12,12,25);
		$worksheet->set_column(13,13,65);

		//TODO: Replace this with the new TourManager stuff, maybe add a toExcel function
		if(!file_exists('/home/coeamb/public_html/logs/tourlogdata.txt')){
			$tourData = file('/home/coeamb/public_html/logs/tourlog.txt', FILE_IGNORE_NEW_LINES);
			$newData = fopen('/home/coeamb/public_html/logs/tourlogdata.txt', 'w') or die("can't open tour log data 01");
			if($tourData !== FALSE){
				fwrite($newData, $tourData);
			} else {
				fwrite($newData, '');
			}
		} else {
			$newData = file('/home/coeamb/public_html/logs/tourlog.txt', FILE_IGNORE_NEW_LINES);
			$oldData = file('/home/coeamb/public_html/logs/tourlogdata.txt', FILE_IGNORE_NEW_LINES);
			foreach($newData as $newTour){
				list($userID,$tourID,$time,$major,$majors,$name,$parent,$school,$schoolyear,$city,$state,$email,$phone,$ambs,$status) = explode(';',$newTour,16);
				$newTours["$time"] = $newTour;
			}
			foreach($oldData as $oldTour){
				list($userID,$tourID,$time,$major,$majors,$name,$parent,$school,$schoolyear,$city,$state,$email,$phone,$ambs,$status) = explode(';',$oldTour,16);
				$oldTours["$time"] = $oldTour;
				$tourData[] = $oldTour;
			}
			if(count($oldData) != count($newData)){
				foreach($newTours as $newTourTime => $newTour){
					if(!isset($oldTours["$newTourTime"])){
						$tourData[] = $newTour;
					}
				}
			}
		}
		foreach($tourData as $key => $line){
			list($userID,$tourID,$time,$major,$majors,$name,$parent,$num,$school,$schoolyear,$city,$state,$email,$phone,$ambs,$status) = explode(';',$line);
			$worksheet->write_number($key+1,0,$key+1);
			$worksheet->write_string($key+1,1,$name);
			$worksheet->write_string($key+1,2,$parent);
			$worksheet->write_string($key+1,3,$num);
			$worksheet->write_string($key+1,4,$school);
			$worksheet->write_string($key+1,5,$schoolyear);
			$worksheet->write_string($key+1,6,$city);
			$worksheet->write_string($key+1,7,$state);
			$worksheet->write_string($key+1,8,$email);
			$worksheet->write_string($key+1,9,$phone);
			$worksheet->write_string($key+1,10,date("D, M j Y \a\\t g:i A", $time));
			$worksheet->write_string($key+1,11,$_engNames[$major]);
			$moreMajors='';
			$spacerComma='';
			$maxSize = count($_engNames);
			for($i=0;$i<$maxSize;$i++){
				if($majors[$i]=='1'){
					$moreMajors .= $spacerComma.$engNames[$i];
					$spacerComma = ', ';
				}
			}
			if($moreMajors == '') $moreMajors = 'None Specified';
			$worksheet->write_string($key+1,12,$moreMajors);
			$ambList = explode(',', $ambs);
			switch($status){
				case -1: $worksheet->write_string($key+1,13,"Tour has been accepted by $_ambNames[$ambs]."); break;
				case -2: $worksheet->write_string($key+1,13,"Tour has been denied by all Ambassadors assigned to it. The tour managaer will have to reassign it."); break;
				case -3: $worksheet->write_string($key+1,13,"Tour has been accepted by $_ambNames[$ambs] and the date of the tour has passed. The followup email was sent through the calendar page."); break;
				case -4: $worksheet->write_string($key+1,13,"Tour has been accepted by $_ambNames[$ambs] and the date of the tour has passed. The family did not show up for the tour."); break;
				case -5: $worksheet->write_string($key+1,13,"Tour was deleted for some reason."); break;
				default: $worksheet->write_string($key+1,13,"Tour has not yet been accepted yet. ".$_ambNames[$ambList[$status]]." is the next Ambassador on the list."); break;
			}
		}
		$workbook->close();
		fwrite($masterLog, date('m/d/Y G:i:s')." - $logbook downloaded the tour log.\n");
		break;*/

/*	case 'Download Tour Log': // downloads the tour log in excel format, old way of doing it
//<doc>Note: This is how the excel logs are created. Check out this package for extended info on how to format the pages. The version on the website is a little older, but has the same functionality.</doc>
//<doc>	http://pear.php.net/package/Spreadsheet_Excel_Writer</doc>
		require_once('excel/Worksheet.php');
		require_once('excel/Workbook.php');
		function HeaderingExcel($filename) {
				header("Content-type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$filename" );
				header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}

		// HTTP headers
		HeaderingExcel('tourlog.xls');

		// Creating a workbook
		$workbook = new Workbook("-");
		$worksheet =& $workbook->add_worksheet('Tour Data');
		$worksheet->write_string(0,1,"Student Name");
		$worksheet->write_string(0,2,"Parent Name");
		$worksheet->write_string(0,3,"High School");
		$worksheet->write_string(0,4,"School Year");
		$worksheet->write_string(0,5,"City");
		$worksheet->write_string(0,6,"State");
		$worksheet->write_string(0,7,"Email");
		$worksheet->write_string(0,8,"Phone Number");
		$worksheet->write_string(0,9,"Tour Date");
		$worksheet->write_string(0,10,"Tour Time");
		$worksheet->write_string(0,11,"Interests");

		$worksheet->set_column(0,0,3);
		$worksheet->set_column(1,3,17);
		$worksheet->set_column(4,6,14);
		$worksheet->set_column(7,7,23);
		$worksheet->set_column(8,8,14);
		$worksheet->set_column(9,9,20);
		$worksheet->set_column(10,10,14);
		$worksheet->set_column(11,11,40);

		$infile = 'logs/tourlog.txt';
		$data = file($infile);
		$matches = array();
		foreach($data as $key => $line){
			$tempdata = explode('|',$line);
			$worksheet->write_number($key+1,0,$key+1);
			foreach($tempdata as $col => $field){
				if($col == 8){
					$lastCol = $field;
					continue;
				}
				if($col == 9){
					if(preg_match_all("/(Monday|Tuesday|Wednesday|Thursday|Friday)-(\d+:\d+-\d+:\d+)/", $field, $matches)){
						$worksheet->write_string($key+1, $col, $matches[1][0].", ".$lastCol);
						$worksheet->write_string($key+1, $col+1, $matches[2][0]);
					} else {
						$worksheet->write_string($key+1,$col,$lastCol);
						$worksheet->write_string($key+1,$col+1,$field);
					}
				} else {
					$worksheet->write_string($key+1,$col+1,$field);
				}
			}
		}
		$workbook->close();
		break; */

	/*case 'Download Sneak Peek Log': // downloads the sneak peek participant log in excel format
		require_once('excel/Worksheet.php');
		require_once('excel/Workbook.php');
		function HeaderingExcel($filename) {
				header("Content-type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$filename" );
				header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}

		// HTTP headers
		HeaderingExcel('sneakpeeklog.xls');

		// Creating a workbook
		$workbook = new Workbook("-");
		$worksheet =& $workbook->add_worksheet('Sneak Peek Sign Ups');
		$worksheet->write_string(0,1,"Name");
		$worksheet->write_string(0,2,"Email Address");
		$worksheet->write_string(0,3,"Street Address");
		$worksheet->write_string(0,4,"City");
		$worksheet->write_string(0,5,"State");
		$worksheet->write_string(0,6,"Zip Code");
		$worksheet->write_string(0,7,"Allergies");
		$worksheet->write_string(0,8,"# Attending");
		$worksheet->write_string(0,9,"Desired Major");
		$worksheet->write_string(0,10,"Staying Overnight");
		$worksheet->write_string(0,11,"Registered for summer Orientation");

		$worksheet->set_column(0,0,3);
		$worksheet->set_column(1,1,20);
		$worksheet->set_column(2,2,30);
		$worksheet->set_column(3,3,25);
		$worksheet->set_column(4,7,12);
		$worksheet->set_column(8,8,15);
		$worksheet->set_column(9,9,20);
		$worksheet->set_column(10,10,15);
		$worksheet->set_column(11,11,30);

		//I have to do this because I messed up the format for the first couple sneak peek entries.
		//The old entries are in the oops file, and the new ones will be treated normally.
		$infile = 'logs/sneakpeekoops.txt';
		$dataOops = file($infile);
		$infile = 'logs/sneakpeek.txt';
		$newData = file($infile);
		$data = array_merge($dataOops, $newData);
		foreach($data as $key => $line){
			$tempdata = explode('|',$line);
			$worksheet->write_number($key+1,0,$key+1);
			foreach($tempdata as $col => $field){
				$worksheet->write_string($key+1,$col+1,$field);
			}
		}
		$workbook->close();
		fwrite($masterLog, date('m/d/Y G:i:s')." - $logbook downloaded the sneak peek log.\n");
		break;
		
	case 'Download Ambassador Info': // downloads the ambassador information files in excel format
		require_once('excel/Worksheet.php');
		require_once('excel/Workbook.php');
		function HeaderingExcel($filename) {
				header("Content-type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=$filename" );
				header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}

		// HTTP headers
		HeaderingExcel('ambassadorinfo.xls');

		// Creating a workbook
		//TODO: This needs to be replaced with the AmbassadorManager stuff
		$workbook = new Workbook("-");
		$worksheet =& $workbook->add_worksheet('Ambassador Information');
		$worksheet->write_string(0,1,"Name");
		$worksheet->write_string(0,2,"Student ID#");
		$worksheet->write_string(0,3,"Campus Address");
		$worksheet->write_string(0,4,"City");
		$worksheet->write_string(0,5,"State");
		$worksheet->write_string(0,6,"Zip Code");
		$worksheet->write_string(0,7,"Hometown");
		$worksheet->write_string(0,8,"Phone Number");
		$worksheet->write_string(0,9,"Email Address");
		$worksheet->write_string(0,10,"Honors Program");
		$worksheet->write_string(0,11,"Honors/Engage Communities");
		$worksheet->write_string(0,12,"Major");
		$worksheet->write_string(0,13,"Co-op/Intern");
		$worksheet->write_string(0,14,"Company");

		$worksheet->set_column(0,0,3);
		$worksheet->set_column(1,2,15);
		$worksheet->set_column(3,3,20);
		$worksheet->set_column(4,4,12);
		$worksheet->set_column(5,5,5);
		$worksheet->set_column(6,7,8);
		$worksheet->set_column(7,8,15);
		$worksheet->set_column(9,9,20);
		$worksheet->set_column(10,10,15);
		$worksheet->set_column(11,12,25);
		$worksheet->set_column(13,14,15);

		$filesToRead = scandir('/home/coeamb/public_html/userdata');
		$actualCount = 0;
		foreach($filesToRead as $infile){
			if(!is_file("/home/coeamb/public_html/userdata/".$infile)){continue;}
			$actualCount++;
			$data = file("/home/coeamb/public_html/userdata/".$infile);
			$worksheet->write_number($actualCount,0,$actualCount);
			foreach($data as $key => $field){
				$worksheet->write_string($actualCount,$key+1,$field);
			}
		}
		$workbook->close();
		fwrite($masterLog, date('m/d/Y G:i:s')." - $logbook downloaded the ambassador info.\n");
		break;*/
		
	case 'Submit Log': //submitting a tour log
		if(($password == 1) || ($password == 2)){
			require_once('/home/coeamb/public_html/mysqlFunctions.php');
			require_once('/home/coeamb/database/dbInfo.php');
			$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword) or die('Could not connect: '.mysql_error());
			mysql_select_db($dbName) or die('Could not select database: '.mysql_error());
				
			$safeNetID = mysql_real_escape_string($userName);
			$ambInfo = mysqlQuerySingleRow("SELECT id FROM ambassadorInfo WHERE netID='$safeNetID' LIMIT 1");
			$safeAmbID = mysql_real_escape_string($ambInfo['id']);
		
			$eventName = mysql_real_escape_string($_POST['eventName']);
			if(isset($_POST['tourID'])){
				$tourID = mysql_real_escape_string($_POST['tourID']);
			} else {
				$tourID = 0;
			}
			$eventDate = mysql_real_escape_string($_POST['eventDate']);
			$eventHours = mysql_real_escape_string($_POST['eventHours']);
			$numberPeople = mysql_real_escape_string($_POST['numberPeople']);
			$otherSchools = mysql_real_escape_string($_POST['otherSchools']);
			$experience = mysql_real_escape_string($_POST['experience']);
			$questions = mysql_real_escape_string($_POST['questions']);
			$jobBetter = mysql_real_escape_string($_POST['jobBetter']);
			$improvements = mysql_real_escape_string($_POST['improvements']);

			$safeUserName = mysql_real_escape_string($userName);
			$query = "INSERT INTO logbook SET ambassador='$safeAmbID', logTime=NOW(), eventName='$eventName', tourID='$tourID', eventDate='$eventDate', hours='$eventHours', peopleInteracted='$numberPeople', otherSchools='$otherSchools', experience='$experience', questions='$questions', madeJobBetter='$jobBetter', improvements='$improvements'";
			mysqlQueryErrorCheck($query);
			$safeResult = mysql_real_escape_string(getMYSQLLogResult());
			mysqlQueryErrorCheck("INSERT INTO actionLog SET eventTime=NOW(), userName='$safeUserName', action='Created log', result='$safeResult'");
		}
		break;
}
//}

returnToLastPage();

?>
