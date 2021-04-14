<h2>Main Navigation:</h2> <!-- Does not display in standard browsers -->
			<ul>
			    <li><a href="http://web.utk.edu/~coeamb/tours.php">Campus Tours</a></li>
				<li><a href="http://web.utk.edu/~coeamb/virtualTours.php">Virtual Tours</a></li>
				<li><a href="http://web.utk.edu/~coeamb/selfGuidedTours.php">Self Guided Tours</a></li>
			    <li><a href="http://web.utk.edu/~coeamb/people.php">Ambassadors</a></li>
			    <li><a href="http://web.utk.edu/~coeamb/life.php">College Life</a></li>
			    <li><a href="http://web.utk.edu/~coeamb/faqs.php">FAQ's</a></li>
			    <!-- <li><a href="http://web.utk.edu/~coeamb/events.php">Events</a></li> -->
			    <li><a href="http://web.utk.edu/~coeamb/links.php">Useful Links</a></li>
<?php
	$now = time();
	$then = mktime(13, 0, 0, 2, 14, 2014);
	if($now < $then){
?>
				<!-- remove or comment out from here down to ... -->
				<li>
					<ul>
						<li><a href="http://web.utk.edu/~coeamb/Ambassador_Application_2014-2015.doc">Apply to become an Engineering Ambassador</a></li>
						<!--<li><a href="http://web.utk.edu/~coeamb/Engage_and_Riser_LLC_Peer_Mentor.docx">Information on being an Engage and Riser LLC Peer Mentor</a></li>-->
					</ul>
				</li>
				<!-- ...here when you want the application to not be up-->
<?
	}
?>
			</ul>