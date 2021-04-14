<?
/*TODO:
 * Make the similar majors be in alphabetical order? Right now I think they are in order of similarity, but it would be more consistent if done alphabetically (optional)
 * By the end of this, I should be able to totally remove the huge arrays and have them dynamically populated from text files. Much like the way the
 * 	schedule files are read in every time they are used. It's more flexible that way. Not that the major info will likely ever change, but the other stuff will.
 * */

//looks up an ambassador's netid based on their index
$_ambLookup = array('jclark74', 'chunter8', 'sfly', 'rkidd2', 'eleturno', 'emorin',
	'bprimm', 'trowe2', 'jscobey1', 'sstrick9', 'btaylo38', 'lbryan10', 
	'sfervan', 'mhutton', 'cjulson', 'tkiste', 'mprice23', 'dseeman');

//reverse look up of an ambassador's index by their netid
$_ambNums = array('jclark74' => '0', 'chunter8' => '1', 'sfly' => '2', 'rkidd2' => '3', 'eleturno' => '4', 'emorin' => '5',
	'bprimm' => '6', 'trowe2' => '7', 'jscobey1' => '8', 'sstrick9' => '9', 'btaylo38' => '10', 'lbryan10' => '11',
	'sfervan' => '12', 'mhutton' => '13', 'cjulson' => '14', 'tkiste' => '15', 'mprice23' => '16', 'dseeman' => '17');

//looks up the full name of an ambassador based on their index
$_ambNames = array('Joshua Clark', 'Caroline Hunter', 'Stephen Fly', 'Rachel Kidd', 'Emily Leturno', 'Emily Morin',
	'Bailey Primm', 'Tyler Rowe', 'John Scobey', 'Scott Strickler',	'Bradford Taylor', 'Lauren Bryant',
	'Sarah Fervan', 'Mike Hutton', 'Catherine Julson', 'Tyler Kiste', 'Matt Price', 'Dave Seeman');

//links the list of ambassadors' netids to their respective majors
$_ambInfo = array('jclark74' => 'computer', 'chunter8' => 'mechanical', 'sfly' => 'material', 'rkidd2' => 'chemical', 'eleturno' => 'mechanical', 'emorin' => 'biomedical', 
	'bprimm' => 'material', 'trowe2' => 'nuclear', 'jscobey1' => 'civil', 'sstrick9' => 'biomedical', 'btaylo38' => 'industrial', 'lbryan10' => 'civil',
	'sfervan' => 'nuclear', 'mhutton' => 'aerospace', 'cjulson' => 'industrial', 'tkiste' => 'industrial', 'mprice23' => 'mechanical', 'dseeman' => 'mechanical');

//links the list of ambassadors' netids to their full names
$_ambFormalNames = array('sfly' => 'Stephen Fly', 'chunter8' => 'Caroline Hunter', 'rkidd2' => 'Rachel Kidd', 'eleturno' => 'Emily Leturno', 'emorin' => 'Emily Morin', 
	'bprimm' => 'Bailey Primm', 'trowe2' => 'Tyler Rowe', 'jscobey1' => 'John Scobey', 'sstrick9' => 'Scott Strickler', 'btaylo38' => 'Bradford Taylor', 'jclark74' => 'Joshua Clark',
	'lbryan10' => 'Lauren Bryant', 'sfervan' => 'Sarah Fervan', 'mhutton' => 'Mike Hutton', 'cjulson' => 'Catherine Julson', 'tkiste' => 'Tyler Kiste', 'mprice23' => 'Matt Price', 'dseeman' => 'Dave Seeman');

//looks up the common name of a major based on its index
$_engTypes = array('aerospace', 'chemical', 'compsci', 'material', 'biomedical', 'civil',
	'electrical', 'mechanical', 'biosystems', 'computer', 'industrial', 'nuclear', 'undecided');

//reverse look up of a major's index by its common name
$_engLookup = array('aerospace' => '0', 'chemical' => '1', 'compsci' => '2', 'material' => '3', 'biomedical' => '4', 'civil' => '5',
	'electrical' => '6', 'mechanical' => '7', 'biosystems' => '8', 'computer' => '9', 'industrial' => '10', 'nuclear' => '11', 'undecided' => '12');


//looks up the 'nice' version of majors based on index
$_engNames = array('Aerospace', 'Chemical', 'Computer Science', 'Materials Science', 'Biomedical', 'Civil and Environmental',
	'Electrical', 'Mechanical', 'Biosystems', 'Computer Engineering', 'Industrial', 'Nuclear', 'Undecided');

//links the common name of a major to their 'nice' name
$_fancyNames = array('aerospace' => 'Aerospace', 'chemical' => 'Chemical', 'electrical' => 'Electrical', 'material' => 'Materials Science', 'biomedical' => 'Biomedical', 'civil' => 'Civil',
	'compsci' => 'Computer Science', 'mechanical' => 'Mechanical', 'biosystems' => 'Biosystems', 'computer' => 'Computer Engineering', 'industrial' => 'Industrial', 'nuclear' => 'Nuclear', 'undecided' => 'Undecided');

//links the common name of a major to the common name of majors similar to it
$_similarMajors = array('aerospace' => 'mechanical,biomedical', 'chemical' => 'biomedical,biosystems,material,nuclear', 'electrical' => 'computer',
	'material' => 'chemical,nuclear,biomedical', 'biomedical' => 'chemical,biosystems,material', 'civil' => 'industrial',
	'compsci' => 'computer,electrical', 'mechanical' => 'aerospace,biomedical','biosystems' => 'biomedical,chemical,industrial',
	'computer' => 'electrical,compsci', 'industrial' => 'civil,biosystems', 'nuclear' => 'chemical,material', 'undecided' => 'undecided');

//because I'm tired of writing all these out
$_engFullNames = array('Aerospace Engineering', 'Chemical Engineering', 'Computer Science', 'Materials Science', 'Biomedical Engineering', 'Civil and Environmental Engineering',
	'Electrical Engineering', 'Mechanical Engineering', 'Biosystems Engineering', 'Computer Engineering', 'Industrial Engineering', 'Nuclear Engineering', 'Undecided Engineering');

//same as above
$_fancyFullNames = array('aerospace' => 'Aerospace Engineering', 'chemical' => 'Chemical Engineering', 'electrical' => 'Electrical Engineering',
	'material' => 'Materials Science', 'biomedical' => 'Biomedical Engineering', 'civil' => 'Civil Engineering',
	'compsci' => 'Computer Science', 'mechanical' => 'Mechanical Engineering', 'biosystems' => 'Biosystems Engineering',
	'computer' => 'Computer Engineering', 'industrial' => 'Industrial Engineering', 'nuclear' => 'Nuclear Engineering', 'undecided' => 'Undecided Engineering');

 class MajorManager {
	private $majorInfoArray; //holds all the MajorInfo objects in a numerically indexed array. access with the functions below
	private $majorCount = 0; //number of majors. indices go from 0 to majorCount-1
	public $nullMajor; //default value for a major that does not exist. often returned on error when the user requests something that is not found
	
	/* constructor: sets up the variables. called automataically with 'new'. Don't call it ever */
	public function __construct(){
		$this->majorInfoArray = array();
		$this->nullMajor = new MajorInfo(-1, 'invalid', 'Invalid Major', array());
	}
	
	/*getMajorByIndex takes the index of a major as the parameter and returns the major object associated with it. returns nullMajor if
	 * the index is out of bounds or invalid, which the use must deal with*/
	public function getMajorByIndex($index){
		if(is_numeric($index)){
			if(($index < $this->majorCount) && ($index >= 0)){
				return $this->majorInfoArray[$index];
			} else {
				die("Argument $index is out of bounds\n");
				return $this->nullMajor;
			}
		} else {
			die("Argument $index is not a number\n");
			return $this->nullMajor;
		}
	}
	
	/*getMajorByShortName takes the short name of a major as the parameter and returns the major object associated with it. returns nullMajor if
	 * the index is out of bounds or invalid, which the use must deal with*/
	public function getMajorByShortName($name){
		if(is_string($name)){
			foreach($this->majorInfoArray as $major){
				if($major->shortName == $name){
					return $major;
				}
			}
			return $this->nullMajor;
		} else {
			return $this->nullMajor;
		}
	}
	
	/* loadAllMajors loads all the major information into this structure from the $_xxxxx arrays above. Always the first function of this class to call*/
	public function loadAllMajors(){
		global $_engTypes, $_engFullNames, $_similarMajors;
		$size = count($_engTypes);
		for($x=0; $x<$size; $x++){
			$this->majorInfoArray[$x] = new MajorInfo($x, $_engTypes[$x], $_engFullNames[$x], $_similarMajors[$_engTypes[$x]]);
		}
		$this->majorCount = $x;
	}
	
	/* loadMajor loads the major information of a single major into this structure from the parameters here. Shouldn't be needed but I'll throw it in anyways*/
	public function loadMajor($mindex, $major, $fancyName, $similarMajors){
		$this->majorInfoArray[] = new MajorInfo($mindex, $major, $fancyName, $similarMajors);
		$this->majorCount++;
	}
	
	/* parseSimilarMajors reads the similar majors info from the arrays above and turns that data into numericall indexed data. should be easier to work with/faster
	 * than trying to mess with searching them by major name as was done previously*/
	public function parseSimilarMajors(){
		for($y=0; $y<$this->majorCount; $y++){
			$major = $this->majorInfoArray[$y];
			$majorList = $major->tempSimilarMajors;
			$majorArray = explode(',', $majorList);
			$size = count($majorArray);
			for($x=0; $x<$size; $x++){
				$majorObj = $this->getMajorByShortName($majorArray[$x]);
				if($majorObj != $this->nullMajor){
					$major->similarMajors[] = $majorObj->index;
				} else {
					//should never happen, so I don't know what to do here. crash?
					die("Error processing Major ".($major->shortName)." similarMajors index $x. Stopping execution.");
				}
			}
		}
	}
	
	public function getMajorCount(){
		return $this->majorCount;
	}
	
}

class MajorInfo{
	//basically the same as the amb info class. link the major, fancy names, and similar majors in here
	public $index = -1; //numerical index of this major, also the index of this major in the array inside the majorManager
	public $shortName = 'invalid'; //informal but short name describing the major ie 'compsci'
	public $longName = 'Invalid Name'; //formal, formatted name of the major ie 'Computer Science'
	public $similarMajors; //array of numerical values that relate to the indices of the majors that are similar to this major
	public $tempSimilarMajors; //string containing the short names of majors that are similar to this major. only used as a quick fix.
	
	/* constructor: sets up the variables. called automataically with 'new'. Don't call it ever */
	public function __construct($mindex, $major, $fancyName, $similarMajors){
		$this->index = $mindex;
		$this->shortName = $major;
		$this->longName = $fancyName;
		$this->similarMajors = array();
		$this->tempSimilarMajors = $similarMajors;
	}
}
 
//use this class for looking up 
class AmbassadorManager{
	private $ambInfoArray; //holds all the AmbassadorInfo objects in a numerically indexed array. access with the functions below
	private $ambassadorCount = 0; //number of ambassadors. indices for ambs go from 0 to ambassadorCount-1
	public $nullAmb; //default value for an ambassador that does not exist. often returned on error when the user requests something that is not found
	
	/* constructor: sets up the variables. called automataically with 'new'. Don't call it ever */
	public function __construct(){
		$this->ambInfoArray = array();
		$this->nullAmb = new AmbassadorInfo(-1, 'invalid', 'Invalid Name', -1);
	}
	
	/* loadAllMajors loads all the major information into this structure from the $_xxxxx arrays above. Always the first function of this class to call..
	 * DOES DEPEND ON THE MAJORS OBJECTS BEING SET UP ALREADY.*/
	public function loadAllAmbassadors(MajorManager $MajMag){
		global $_ambInfo, $_ambLookup, $_ambNames;
		$size = count($_ambLookup);
		for($x=0; $x<$size; $x++){
			$maj = $MajMag->getMajorByShortName($_ambInfo[$_ambLookup[$x]]);
			$this->ambInfoArray[$x] = new AmbassadorInfo($x, $_ambLookup[$x], $_ambNames[$x], $maj->index);
			$this->ambassadorCount++;
		}
	}
	
	/*loadAmbassador: takes an the ambassador info as the arguments, creates an object and adds it to the internal array. increments the amb count.
		Shouldn't need this one when you have the one above, but I'll put it in here anyways*/
	public function loadAmbassador($aindex, $UTID, $longName, $majorIndex){
		$this->ambInfoArray[] = new AmbassadorInfo($aindex, $UTID, $longName, $majorIndex);
		$this->ambassadorCount++;
	}
	
	/*findAmbassadorByIndex: takes the index of an ambassador as the argument. returns the AmbassadorInfo object associated with that index.
	  If the index is out of bounds of the array of ambs, returns nullAmb. User must deal with this accordingly*/
	public function findAmbassadorByIndex($index){
		if(is_numeric($index)){
			if(($index < $this->ambassadorCount) && ($index >= 0)){
				return $this->ambInfoArray[$index];
			} else {
				die("Find Amb Index: Argument $index out of bounds\n");
				return $this->nullAmb;
			}
		} else {
			die("Find Amb Index: Argument $index is not numeric\n");
			return $this->nullAmb;
		}
	}
	
	/*findAmbassadorByUTid: takes the UTid of an ambassador as the argument. returns the AmbassadorInfo object associated with that index.
	  If no ambassador with the given UTid is found, returns nullAmb. User must deal with this accordingly*/
	public function findAmbassadorByUTid($UTid){
		if(is_string($UTid)){
			foreach($this->ambInfoArray as $amb){
				if($amb->UTid == $UTid){
					return $amb;
				}
			}
			die("Find Amb UTID: Argument $UTid not found\n");
			return $this->nullAmb;
		} else {
			die("Find Amb UTID: Argument $UTid not a string\n");
			return $this->nullAmb;
		}
	}
	
	/*findAmbassadorByMajor: takes the index of an major as the argument. returns all the ambassadors that have that major as an array of AmbassadorInfo's.
	 * If no ambassador with the given major is found, returns an empty array. User must deal with this accordingly*/
	public function findAmbassadorsByMajor($majorIndex){
		if(is_numeric($majorIndex)){
			$ambArray = array();
			foreach($this->ambInfoArray as $amb){
				if($amb->major == $majorIndex){
					$ambArray[] = $amb;
				}
			}
			return $ambArray;
		} else {
			return array();
		}
	}
	
	public function getArrayOfUTIDs(){
		$ambArray = array();
		$size = count($this->ambInfoArray);
		for($x=0; $x<$size; $x++){
			$ambArray[$x] = $this->ambInfoArray[$x]->UTid;
		}
		return $ambArray;
	}
	
	public function getAmbassadorCount(){
		return $this->ambassadorCount;
	}
	
	public function writeAllAmbassadorInfoToFile(){
		//put stuff here later
	}
	
	public function loadAllAmbassadorInfoFromFile($file){
		//put more stuff here
	}
} 
 
class AmbassadorInfo{
	//properties for each ambassador. everything else can be looked up in a table, but individual info needs
	//to be contained in this class for convenience
	public $index = -1; //numerical index of this ambassador, also the index of this ambassador in the array inside the AmbassadorManager
	public $UTid = 'invalid'; //UTID of the ambassador. Used for email, log-in stuff, logging, etc
	public $name = 'Invalid Name'; //Preferred name of the ambassador. Used for notifications directly to each ambassador
	public $major = -1; //numerical index of the major of the ambassador
	//probably put something in here to do with the schedule, other information in the profile too
	public $schedule; //not implemented yet (same for any other properties below here)
	public $phoneNumber;
	public $coop;
	public $intern;
	public $studyAbroad;
	public $honors;
	public $etc; //add more fields as needed, there might be an easy way to do this, but for now this works
	
	public function __construct($aindex, $UTID, $longName, $majorIndex){
		$this->index = $aindex;
		$this->UTid = $UTID;
		$this->name = $longName;
		$this->major = $majorIndex;
		$this->schedule = -1;
		$this->phoneNumber = -1;
		$this->coop = -1;
		$this->intern = -1;
		$this->studyAbroad = -1;
		$this->honors = -1;
		$this->etc = -1;
	}
}

class ScheduleManager{
	public $ambassadorSchedules;
	public $majorSchedules;
	public $totalAmbassadorSchedules;
	public $totalMajorSchedules;
	
	public function __construct(){
		$this->ambassadorSchedules = array(array());
		$this->majorSchedules = array(array());
		$this->totalAmbassadorSchedules = array(array());
		$this->totalAmbassadorSchedules = array(array());
	}
	
	public function loadAllSchedulesFromFile(AmbassadorManager $ambMan, MajorManager $majMan){
		//variables used in the whole function
		$blankDaySchedule = array_fill(0,18,0);/*this 18 is the only thing im not sure about. be sure that this value stays current. probably won't change but if it does, it will mess things up*/
		
		/*first, we load the schedule for each ambassador. If an ambassador does not have a schedule file,
		 * fill their spot in the array with 0's to prevent 'holes' in the array later on*/
		$ambCount = $ambMan->getAmbassadorCount();
		for($ambNum=0; $ambNum<$ambCount; $ambNum++){
			$ambObj = $ambMan->findAmbassadorByIndex($ambNum);
			$ambName = $ambObj->UTid;
			if(!file_exists("/home/coeamb/public_html/schedules/{$ambName}.txt")){ //if the ambassador does not have a schedule file ***
				for($day=0; $day<7; $day++){ /*ok this here is bad too. I don't like hardcoding these values but otherwise 
									you would have gaps in the resulting array. Be sure to change this if I ever change the data format*/
					$this->ambassadorSchedules[$ambNum][$day] = $blankDaySchedule;
				}// *** fill the ambassador's schedule with 0's and continue to the next ambassador
				continue;
			}
			$hourData = file("/home/coeamb/public_html/schedules/{$ambName}.txt");
			$numDays = count($hourData);
			$this->ambassadorSchedules[$ambNum][0] = $blankDaySchedule; //yep, more stuff that might need to be changed ---
			for($day=0; $day<$numDays; $day++){
				$timeString = rtrim($hourData[$day]);
				$timeArray = str_split($timeString,1);
				$dayOfWeek = $day+1; //file data only goes from mon-fri. week days go from sun-sat
				$this->ambassadorSchedules[$ambNum][$dayOfWeek] = $timeArray;
			}
			$this->ambassadorSchedules[$ambNum][6] = $blankDaySchedule; // --- in case I ever update the data layout
		}
		
		/*now, we use the data from each of the ambassadors to determine when each of the majors has tour availability
		 * Same as above, if a major is not found, it is filled with 0's to prevent future problems */ 
		$numMajors = $majMan->getMajorCount();
		for($majorNum=0; $majorNum<$numMajors; $majorNum++){
			$ambObjArray = $ambMan->findAmbassadorsByMajor($majorNum);
			$ambsPerMajor = count($ambObjArray);
			for($day=0; $day<7; $day++){//HARDCODED VALUE HERE. BE CAREFUL OF CHANGES
				$this->majorSchedules[$majorNum][$day] = $blankDaySchedule;
			}
			if($ambsPerMajor == 0){ //if no ambassadors are in this major
				continue;
			}
			for($ambNum=0; $ambNum<$ambsPerMajor; $ambNum++){
				$numDays = count($this->ambassadorSchedules[$ambObjArray[$ambNum]->index]);
				for($day=0; $day<$numDays; $day++){
					$numTimeSlots = count($this->ambassadorSchedules[$ambObjArray[$ambNum]->index][$day]);
					for($timeSlot=0; $timeSlot<$numTimeSlots; $timeSlot++){
						if($this->ambassadorSchedules[$ambObjArray[$ambNum]->index][$day][$timeSlot] == 1){
							$this->majorSchedules[$majorNum][$day][$timeSlot] = 1;
						}
					}
				}
			}
		}

		/*last we take the total data and condense it like the other version does into the totalX arrays*/
	}
	
	public function loadScheduleFromFile($file){
		
	}
}

class ScheduleInfo{
	public $scheduleData;
	
	public function __construct(){
		$this->scheduleData = array(array());
	}
}

$majorManager = new MajorManager();
$majorManager->loadAllMajors();
$majorManager->parseSimilarMajors();

$ambManager = new AmbassadorManager();
$ambManager->loadAllAmbassadors($majorManager);

//print_r($ambManager);

$schedManager = new ScheduleManager();
$schedManager->loadAllSchedulesFromFile($ambManager, $majorManager);

?>