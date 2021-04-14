<?php
/*todo
	*fix this up, make it actually work real good and stuff, track ips, visit time and shit
*/
$count_my_page = ("logs/hits.txt");
$hits = file($count_my_page);
$hits[0] ++;
$fp = fopen($count_my_page , "w") or die("can't open log 1");
fputs($fp , "$hits[0]");
fclose($fp);
?>