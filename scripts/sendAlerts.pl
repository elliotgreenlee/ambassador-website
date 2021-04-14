#!/soft/script/bin/perl

# This script sends out reminder emails for tours that are occurring the next day.
# If a crontab entry is set up for this script, it can be used automatically send out emails every day.
# A log of whether nor not the emails were successfully sent is emailLog.txt in the logs directory.
# If the email failed, the script is piped to emailErrors.txt in the script directory (set this up in the crontab file).

@ambLookup = ('jclark74', 'chunter8', 'sfly', 'rkidd2', 'eleturno', 'emorin',
	'bprimm', 'trowe2', 'jscobey1', 'sstrick9', 'btaylo38', 'lbryan10', 
	'sfervan', 'mhutton', 'cjulson', 'tkiste', 'mprice23', 'dseeman');

@ambNames = ('Joshua Clark', 'Caroline Hunter', 'Stephen Fly', 'Rachel Kidd', 'Emily Leturno', 'Emily Morin',
	'Bailey Primm', 'Tyler Rowe', 'John Scobey', 'Scott Strickler',	'Bradford Taylor', 'Lauren Bryant',
	'Sarah Fervan', 'Mike Hutton', 'Catherine Julson', 'Tyler Kiste', 'Matt Price', 'Dave Seeman');

@engFullNames = ('Aerospace Engineering', 'Chemical Engineering', 'Computer Science', 'Materials Science',
'Biomedical Engineering', 'Civil and Environmental Engineering', 'Electrical Engineering', 'Mechanical Engineering',
'Biosystems Engineering', 'Computer Engineering', 'Industrial Engineering', 'Nuclear Engineering', 'Undecided Engineering');

@monthNames = qw(January February March April May June July August September October November December);
@dayNames = qw(Sunday Monday Tuesday Wednesday Thursday Friday Saturday);

$sendMail = "/usr/sbin/sendmail -t";
$reply_to = "Reply-To: jclark74\@utk.edu\n";
$BCC = "BCC: coeamb\@utk.edu\n";
$from = "From: webmaster\@utk.edu\n";

$theTimeThatIsTheTimeOfNow = localtime;
open(EMAILLOG, '>> /home/coeamb/public_html/logs/emailLog.txt');
print EMAILLOG "$theTimeThatIsTheTimeOfNow - Email Script Starting\n";
close(EMAILLOG);

open(LOGFILE, '/home/coeamb/public_html/logs/tourlogdata.txt') or die("Cannot access the log file");
$emailCount=0;
$errorLevel=0;
($nowsec,$nowmin,$nowhour,$nowmday,$nowmon,$nowyear,$nowwday,$nowyday,$nowisdst) = localtime();
while(<LOGFILE>){
  if(/;-1\n/){
    @data = split(/;/);
    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($data[2]);
    if(($year==$nowyear) && ($yday==$nowyday+1)){ #this will not work if there is a tour on Jan 1, but I really doubt that will happen
      $tempErrorLevel = $errorLevel;
      $year += 1900;
      $time = (($hour > 12)?$hour-12:$hour).':'.(($min==0)?'00':'30').(($hour>=12)?' P.M.':' A.M.');
      $userSubject = "Subject: A reminder of your tour with the College of Engineering Ambassadors tomorrow\n";
      $ambSubject = "Subject: A reminder that you have a tour tomorrow\n";
      $userBody = "Dear Mr./Ms. $data[6],\n\n";
      $userBody .= "This is a reminder that you have a tour tomorrow with the University of Tennessee Knoxville College of Engineering Ambassadors. ";
      $userBody .= "Your tour is at $time on $dayNames[$wday], $monthNames[$mon] $mday, $year. ";
      $userBody .= "Please meet at 101 Perkins Hall which is located on Middle Drive. ";
      $userBody .= "You can park at the University Center Garage and walk up.";
      $userBody .= "If you have any issues, contact the Engineering Advising Office at (865)974-4008.\n\n";
      $userBody .= "Thanks,\nThe UT College of Engineering Ambassadors\n\n";
      $userBody .= "***This message was automatically generated. If you have any questions about the email, feel free to contact the webmaster at jclark74\@utk.edu ***\n";
      $send_to_user = "To: $data[12]\n";
      $theTimeThatIsTheTimeOfNow = localtime;
      if(open(SENDMAIL, "|$sendMail")){
        print SENDMAIL $reply_to;
        print SENDMAIL $userSubject;
        print SENDMAIL $send_to_user;
        print SENDMAIL $from;
        print SENDMAIL $BCC;
        print SENDMAIL "Content-type: text/plain\n\n";
        print SENDMAIL $userBody;
        if(close(SENDMAIL)){
	  open(EMAILLOG, '>> /home/coeamb/public_html/logs/emailLog.txt');
	  print EMAILLOG "$theTimeThatIsTheTimeOfNow - Reminder email sent to user: $data[12] for tour $data[2]\n";
	  close(EMAILLOG);
	} else {
	  open(EMAILLOG, '>> /home/coeamb/public_html/logs/emailLog.txt');
	  print EMAILLOG "$theTimeThatIsTheTimeOfNow - An error occurred in sending a user email for tour $data[2]. Check the log file.\n";
	  close(EMAILLOG);
	  $errorLevel = $errorLevel + 1;
	}
      } else {
	open(EMAILLOG, '>> /home/coeamb/public_html/logs/emailLog.txt');
	print EMAILLOG "$theTimeThatIsTheTimeOfNow - For tour $data[2], Cannot open user $sendmail: $!\n";
	close(EMAILLOG);
	$errorLevel = $errorLevel + 1;
      }

      $ambBody = "Hey ".$ambNames[$data[14]].",\n\n";
      $ambBody .= "This is a reminder that you have a tour tomorrow. ";
      $ambBody .= "Here is the information about the tour.\n\n";
      $ambBody .= "Tour Time: $time on $dayNames[$wday], $monthNames[$mon] $mday, $year. \n";
      $ambBody .= "Major selected for Tour: ".$engFullNames[$data[3]]."\n";
      $otherMajors = '';
      $app = '';
      $i=0;
      $size = length($data[4]);
      for($i=0;$i<$size;$i++){
        if((substr($data[4],$i,1) eq '1') && ($i != $data[3])){
	  $otherMajors .= $app.$engFullNames[$i];
	  $app = ', ';
	}
      }
      if($otherMajors ne ''){
        $ambBody .= "Other Majors they are interested in: $otherMajors\n";
      }
      $ambBody .= "Parent Names: $data[6]\n";
      $ambBody .= "Student Name: $data[5]\n";
      $ambBody .= "Number of Visitors: $data[7]\n";
      $ambBody .= "High School: $data[8]\n";
      $ambBody .= "Year in High School: $data[9]\n";
      $ambBody .= "From: $data[10], $data[11]\n";
      $ambBody .= "Email Address: $data[12]\n";
      if($data[13] ne ''){
        $ambBody .= "Phone Number: $data[13]\n\n";
      }
      $ambBody .= "The family has also been sent a reminder of the tour.\n\n";
      $ambBody .= "***This message was automatically generated. If you have any questions about the email, feel free to contact the webmaster at jclark74\@utk.edu ***\n";
      $send_to_amb = "To: ".$ambLookup[$data[14]]."\@utk.edu\n";
      $theTimeThatIsTheTimeOfNow = localtime;
      if(open(SENDMAIL, "|$sendMail")){
        print SENDMAIL $reply_to;
        print SENDMAIL $ambSubject;
        print SENDMAIL $send_to_amb;
        print SENDMAIL $from;
        print SENDMAIL $BCC;
        print SENDMAIL "Content-type: text/plain\n\n";
        print SENDMAIL $ambBody;
        if(close(SENDMAIL)){
	  open(EMAILLOG, '>> /home/coeamb/public_html/logs/emailLog.txt');
	  print EMAILLOG "$theTimeThatIsTheTimeOfNow - Reminder Email sent to ambassador: ".$ambLookup[$data[14]]."\@utk.edu for tour $data[2]\n";
	  close(EMAILLOG);
	} else {
	  open(EMAILLOG, '>> /home/coeamb/public_html/logs/emailLog.txt');
	  print EMAILLOG "$theTimeThatIsTheTimeOfNow - An error occurred in sending an ambassador email for tour $data[2]. Check the log file.\n";
	  close(EMAILLOG);
	  $errorLevel = $errorLevel + 1;
	}
      } else {
	open(EMAILLOG, '>> /home/coeamb/public_html/logs/emailLog.txt');
	print EMAILLOG "$theTimeThatIsTheTimeOfNow - For tour $data[2] Cannot open ambassador $sendmail: $!\n";
	close(EMAILLOG);
	$errorLevel = $errorLevel + 1;
      }
      if($tempErrorLevel == $errorLevel){
	$emailCount = $emailCount + 1;
      }
    }
  }
}
close(LOGFILE);

$theTimeThatIsTheTimeOfNow = localtime;
open(EMAILLOG, '>> /home/coeamb/public_html/logs/emailLog.txt');
print EMAILLOG "$theTimeThatIsTheTimeOfNow - Email Script Exiting: $errorLevel errors; $emailCount emails sent\n";
close(EMAILLOG);

