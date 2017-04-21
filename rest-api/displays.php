<?php
include('../defines.php');
if(!@include("../conf.php")) {
	$errorMsg = "Setup missing";
	break;
}
include('../display/display.php');
include('../scheduler.php');

/* 1. database connection */
if ($GLOBALS['CONFIG']['sql_isPW']) {
	$db = @mysql_connect($GLOBALS['CONFIG']['sql_host'], $GLOBALS['CONFIG']['sql_login'], $GLOBALS['CONFIG']['sql_pw']); 
} else {
	$db = @mysql_connect($GLOBALS['CONFIG']['sql_host'], $GLOBALS['CONFIG']['sql_login']); 
}        
if (!$db) { $errorCode=1; $errorMsg=mysql_error(); break; }
if(!mysql_select_db($GLOBALS['CONFIG']['sql_db'],$db)) { $errorCode=2; $errorMsg=mysql_error(); break; }

/* 2. Update global scheduler */
updateScheduler();
	
$arr = array();
$arr['time']  = date('Y-m-d H:i:s', time());
$arr['displays'] = array();

$sql = "SELECT *, UNIX_TIMESTAMP(date) FROM `".MYSQL_TABLE_DISPLAY."` WHERE 1 ORDER BY `date` DESC";
$req = @mysql_query($sql) or sqldie($sql);
$idx=0;
while ($row = mysql_fetch_assoc($req)) {
    $arr['displays'][$idx] = array();
    $arr['displays'][$idx]['ip']   = $row['ip'];
    $arr['displays'][$idx]['date'] = $row['UNIX_TIMESTAMP(date)'];
    $arr['displays'][$idx]['diff'] = time()-$row['UNIX_TIMESTAMP(date)'];
	$arr['displays'][$idx]['assetId'] = array();
	$id = $row['assetId'];
	if ($id!=NULL) {
		$t = 0;
		do {
			$arr['displays'][$idx]['assetId'][$t++] = $id;
			$id = getNextAssetId($id);
		} while(($id!=$row['assetId']) && ($t<30));
	}
    $idx++;
}

echo json_encode($arr);
?>