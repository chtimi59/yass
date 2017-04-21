<?php
include('../defines.php');
include('display.php');
include('../scheduler.php');
switch ($_SERVER['REQUEST_METHOD'])
{
    case 'GET':
        break; 
    case 'DELETE':
    case 'PUT':
    case 'POST':
    default:
        header("HTTP/1.0 400 Bad Request");
        echo json_encode($arr);
        exit();
}

$errorCode = '';
$errorMsg = '';

do
{ 
    if(!@include("../conf.php")) {
        $errorCode = 'a01';
		$errorMsg = "Setup missing";
		break;
    }

    /* 1. database connection */
    if ($GLOBALS['CONFIG']['sql_isPW']) {
        $db = @mysql_connect($GLOBALS['CONFIG']['sql_host'], $GLOBALS['CONFIG']['sql_login'], $GLOBALS['CONFIG']['sql_pw']); 
    } else {
        $db = @mysql_connect($GLOBALS['CONFIG']['sql_host'], $GLOBALS['CONFIG']['sql_login']); 
    }        
    if (!$db) { $errorCode='b01'; $errorMsg=mysql_error(); break; }
    if(!mysql_select_db($GLOBALS['CONFIG']['sql_db'],$db)) { $errorCode='b02'; $errorMsg=mysql_error(); break; }

    
    /* 2. find sequence */
    $assetId = NULL;
    $assetCount = 0;
    $sequence = array();
    do {        
        $assetId = getNextAssetId($assetId);
        if (NULL == $assetId) break; // no asset
        if (($assetCount>0) && ($sequence[0] == $assetId)) break; //back to origin
        $sequence[$assetCount++] = $assetId;
    } while($assetCount < 100000); // for safety reason
    if ($assetCount >= 100000) {
        $errorCode='f01'; $errorMsg='Too much assets!';
        break;
    }
    
    /* 3. do json */ 
    $arr = array();
    $arr['time']  = date('Y-m-d H:i:s', time());
    $arr['sequence']  = $sequence;
    $arr['displays'] = array();

    $sql = "SELECT *, UNIX_TIMESTAMP(date) FROM `".MYSQL_TABLE_DISPLAY."` WHERE 1 ORDER BY `date` DESC";
    $req = @mysql_query($sql);
    if (!$req) { $errorCode='f02'; $errorMsg=mysql_error(); break; }
    
    $displayIdx=0;
    while ($row = mysql_fetch_assoc($req)) {
        $arr['displays'][$displayIdx] = array();
        $arr['displays'][$displayIdx]['id']   = $row['id'];
        $arr['displays'][$displayIdx]['ip']   = $row['ip'];
        $arr['displays'][$displayIdx]['date'] = $row['UNIX_TIMESTAMP(date)'];
        $arr['displays'][$displayIdx]['diff'] = time()-$row['UNIX_TIMESTAMP(date)'];
        $arr['displays'][$displayIdx]['assetId'] = $row['assetId'];
        $displayIdx++;
    }
    
    /* ok */
    header("HTTP/1.0 200 OK");
    echo json_encode($arr);
    exit();
    
} while(0);

/* error */
header("HTTP/1.0 500 Internal Server Error");
$arr = array( 'code' => $errorCode , 'msg' => $errorMsg);
echo json_encode($arr);
exit();
?>

