<?php

// TODO: need to make this updateable by the master user

//$_holidayList lists specific dates that will not be available for giving tours
$_holidayList = array(
	array(1, 16, 2012), /* MLK Holiday */
	array(3, 2, 2012), /* TSA Teams */
	array(3, 14, 2012), /* Min Kao Dedication */
	array(3, 19, 2012),
	array(3, 20, 2012),
	array(3, 21, 2012), /* Spring Break */
	array(3, 22, 2012),
	array(3, 23, 2012),
	array(4, 6, 2012), /* Spring Recess (Good Friday) */
	array(4, 20, 2012) /* Sneak Peek */
);

//$breakList lists the start and endpoints of long breaks (between semesters)
$_breakList = array(
	array(4, 27, 2012), /*first study day before exams, no tours today*/
	array(1, 16, 2015), /*arbitrary date far away*/
);

?>