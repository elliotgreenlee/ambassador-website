<? /* todo
	*put a logout button on here or something
*/ ?>
<ul>
	<li>
		<ul>
			<li><a href="http://web.utk.edu/~coeamb/profile.php">Profile</a></li>
			<li><a href="http://web.utk.edu/~coeamb/calendar.php">Tour Schedule</a></li>
			<li><a href="http://web.utk.edu/~coeamb/logbook.php">Logbook</a></li>
			<?php
			if($password == 2){
			?>
				<li><a href="http://web.utk.edu/~coeamb/manager.php">Manager</a></li>
			<?
			}
			?>
			<!-- <li><a href="http://web.utk.edu/~coeamb/logmeout.php?logmeout=yesplease&redirect=<?=$redirect;?>">Logout</a></li> -->
		</ul>
	</li>
</ul>