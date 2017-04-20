<?php
include('../common.php');
$db = @mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW); 
if (!$db)  die('Could not connect: ' . mysql_error());
if(!mysql_select_db(MYSQL_DB,$db)) die('Could not connect db: ' . mysql_error());

$arr = array();
$arr['time']  = date('Y-m-d H:i:s', time());
$arr['curId'] = GetCurrentBroadcastedAssetId();
$arr['nextId'] = GetNextBroadcastedAssetId();
$arr['displays'] = array();

$sql = "SELECT ip, UNIX_TIMESTAMP(date) FROM `".MYSQL_TABLE_DISPLAY."` WHERE 1 ORDER BY `date` DESC";
$req = @mysql_query($sql) or sqldie($sql);
$idx=0;
while ($row = mysql_fetch_assoc($req)) { 
    $arr['displays'][$idx] = array();
    $arr['displays'][$idx]['ip']   = $row['ip'];
    $arr['displays'][$idx]['date'] = $row['UNIX_TIMESTAMP(date)'];
    $arr['displays'][$idx]['diff'] = time()-$row['UNIX_TIMESTAMP(date)'];
    $idx++;
}

echo json_encode($arr);
?>