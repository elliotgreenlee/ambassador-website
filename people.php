<?php
include 'webpages.php';

printHeader('UT College of Engineering Ambassadors--Our Ambassadors');
printNavigation(false, false);
?>

<section class="two-thirds column">

<?php
require_once('/home/coeamb/database/dbInfo.php');

require_once('/home/coeamb/public_html/mysqlFunctions.php');

$db = mysql_connect($dbHostName.':'.$dbPortNumber, $dbUserName, $dbPassword)
	or die('Could not connect: '.mysql_error());

mysql_select_db($dbName)
	or die('Could not select database: '.mysql_error());

if(isset($_GET['amb'])){
	$amb = $_GET['amb'];
	$safeAmb = mysql_real_escape_string($amb);
	$ambInfo = mysqlQuerySingleRow("SELECT * FROM ambassadorInfo WHERE netID='$safeAmb' LIMIT 1");
	if(!isset($ambInfo['id'])){
		$ambInfo = NULL;
	}
} else {
	$ambInfo = NULL;
}

if($ambInfo == NULL){
	echo "<img src=\"images/engr-ambassadors-web-2016.jpg\" class=\"frame\">
	<p style=\"font-size:.85em\">The 2016 Engineering Ambassadors &lpar;front row&comma; from left&rpar;&colon; Samira Ibrahim&comma; Leah Stephens&comma; Alyssa Lindsey&comma; Sierra Ellis&comma; Mary McBride&comma; Tina Anjonrin-Ohu&comma; Kristen Miranda &lpar;second row&comma; from left&rpar;&colon; Camille Bergen&comma; Gillian McGlothin&comma; Katie Gipson&comma; Elliot Greenlee&comma; MaryBeth Iannuzzi&comma; Amanda Randolph&comma; Abby Link&comma; Amany Alshibli&comma; Sarah Davis&comma; Sarah Jacob &lpar;third row&comma; from left&rpar;&colon; Will Fredebeil&comma; Drew Welch&comma; Parker Tooley&comma; Brandon Lowe&comma; Braxton Brakefield&comma; Jermaine Cheairs&comma; John Dooley&comma; Will Wheeler&comma; and Stephen Kwan.</p>
	<p style=\"font-size:.85em\">Not pictured&colon; Katelyn Luthi&comma; Brooke McMurrer.</p>
	
	<table>\n<tr>\n<td colspan=\"2\">\n<h1>Ambassador List </h1>\n</td>\n</tr>\n<tr>\n<td>\n";
	$ambList = mysqlQueryToListIndexBy('SELECT * FROM ambassadorInfo ORDER BY name', 'id');
	$majList = mysqlQueryToList('SELECT * FROM majorInfo ORDER BY longName');
	foreach($majList as $maj){
		$ambArray = array();
		foreach($ambList as $amb){
			if($amb['major'] == $maj['id']){
				$ambArray[] = $amb['id'];
			}
		}
		if(count($ambArray) > 0){
			echo '<h2>'.$maj['longName']."</h2>\n";
			foreach($ambArray as $amb){
				$ambInfo = $ambList[$amb];
				echo "<p><a href=\"$fileName?amb=".$ambInfo['netID'].'">'.$ambInfo['name']."</a></p>\n";
			}
		}
	}
	echo "</td>\n</tr>\n</table>\n";
	//echo "</td>\n<td valign=\"middle\">\n<img src=\"images/display.jpg\" alt=\"Engineering Ambassadors in front of the College of Engineering Display\" class=\"right-float-photo\" height=\"230\" width=\"300\" />\n</td>\n</tr>\n</table>\n";
} else {
	//echo "<table>\n<tr>\n<td colspan=\"2\">\n<h1>View Ambassador Profile</h1>\n</td>\n</tr>\n<tr>\n<td>\n";
	echo "<h1>View Ambassador Profile</h1>\n";
	$safeMajID = mysql_real_escape_string($ambInfo['major']);
	$major = mysqlQuerySingleRow("SELECT * FROM majorInfo WHERE id='$safeMajID' LIMIT 1");
	echo "<h2>".$ambInfo['name']." -- ".$major['longName']."</h2>";
	$pictureName = "/home/coeamb/public_html/images/".$ambInfo['netID'].".jpg";
	if(file_exists($pictureName)){
		echo "<img src=\"images/".$ambInfo['netID'].".jpg\"  alt=\"Picture of ".$ambInfo['name']."\" class=\"left-float-photo\"/>\n<p>\n";
	}
	$bio = '<p>'.$ambInfo['bio'].'</p>';
	echo str_replace("\n", "</p><p>", $bio);
	echo "<a href=\"mailto:".$ambInfo['netID']."@utk.edu\">Contact ".$ambInfo['name']."</a></p>\n";
	//echo "</td>\n<td valign=\"top\">\n<img src=\"images/".$ambInfo['netID'].".jpg\" alt=\"Engineering Ambassadors in front of the College of Engineering Display\" class=\"right-float-photo\" height=\"230\" width=\"300\" />\n</td>\n</tr>\n</table>\n";
}

mysql_close($db);

echo "</section>\n";
printFooter(array('Our Ambassadors' => 'people.php'));
?>
