<?php

function mysqlQueryToList($query, $errorMsg="Error: "){
	//echo "L Query: $query\n";
	$tempList = array();
	$res = mysql_query($query) or die($errorMsg.mysql_error());
	while($row = mysql_fetch_array($res, MYSQL_ASSOC)){
		$tempList[] = $row;
	}
	mysql_free_result($res);
	return $tempList;
}

function mysqlQueryErrorCheck($query, $errorMsg="Error: "){
	//echo "E Query: $query\n";
	$res = mysql_query($query);
	if(!$res){
		die($errorMsg.mysql_error());
	}
}

function mysqlQuerySingleRow($query, $errorMsg="Error: "){
	//echo "S Query: $query\n";
	$res = mysql_query($query) or die($errorMsg.mysql_error());
	return mysql_fetch_array($res, MYSQL_ASSOC);
}

function mysqlQueryToListIndexBy($query, $indexBy, $errorMsg="Error: "){
	//echo "I Query: $query\n";
	$tempList = array();
	$res = mysql_query($query) or die($errorMsg.mysql_error());
	while($row = mysql_fetch_array($res, MYSQL_ASSOC)){
		$index = $row["$indexBy"];
		$tempList["$index"] = $row;
	}
	mysql_free_result($res);
	return $tempList;
}

?>