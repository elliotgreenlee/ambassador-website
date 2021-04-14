function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function SetCookie(cookieName, cookieData, expireDate) {
        document.cookie = cookieName + "=" + escape(cookieData) + "; expires=" + expireDate.toGMTString()+"; path=/; domain=www.engr.utk.edu";
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
function setUndecided() {
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
function confirmTourNew(tourTime,printTourTime,printTourDate) {
	var answer = confirm("You have selected a tour at "+printTourTime+" on "+printTourDate+". Is this correct?");
	if(answer){
		var date = new Date();
		date.setTime(date.getTime()+24*60*60*1000);
		SetCookie('tour', tourTime, date);
		alert("Please click the 'Submit' button at the bottom of the page to submit your tour request.");
	}
}
/*function confirmTour(tourid,time,major,ambs) {
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
}*/

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

function enableCompactDisplay(newStatus){
  var cookieData = readCookie('compact');
  if(cookieData.charAt(0)=='0'){
    cookieData = setCharAt(cookieData, 0, '1');
  } else {
    cookieData = setCharAt(cookieData, 0, '0');
  }
  var date = new Date();
  date.setTime(date.getTime()+24*60*60*1000);
  SetCookie('compact', cookieData, date);
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