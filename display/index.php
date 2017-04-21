<?php
include('../defines.php');
$errorCode = '';
$errorMsg = '';
$date = date('m/d/Y h:i:s a', time());

do
{
	if(!@include("../conf.php")) {
		$errorMsg = "Setup missing";
		break;
	}
	
	include('display.php');
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
	
    /* 2. Client-Display IP */
    $ip = getDisplayIp();
        
    /* 3. update db with Client-Display */
    $ret = updateDisplayHeartBit($ip);
    if ($ret!=NULL) { $errorCode=3; $errorMsg=$ret; break; }
    
    /* 4- find next assetId */
	$assetId = getNextAssetId(getDisplayAssetId($ip));
    if ($assetId==NULL) { $errorCode=4; $errorMsg='Playlist is empty'; break; }        
    
    /* 5- Apply */
    setDisplayAssetId($ip, $assetId);        
    $sql = "SELECT * FROM `".MYSQL_TABLE_ASSETS."` WHERE `id`=$assetId";
    $req = @mysql_query($sql);
    if (!$req) { $errorCode=5; $errorMsg=mysql_error(); break; }
    $row = mysql_fetch_assoc($req);
    if (!$row) { $errorCode=6; $errorMsg='Playlist is empty'; break; }    
    if (!isset($row['path']))  { $errorCode=7; $errorMsg='Bad db formating'; break; }
    if (!isset($row['duration'])) { $errorCode=8; $errorMsg='Bad db formating'; break; }    
    $assetUrl = ASSET_URL_BASE.$row['path'].'/index.php?t='.$row['duration'];        
    
    /* 6- Actual redirection */
    header('Location: '.$assetUrl);      
    exit();    
    break;
    
} while(0);

/* fail so auto-refresh (10s) */
header("Refresh:10; url='index.php'", true, 303);
?>

<html>
<body>
<h1>
<?php echo $date ?><br>
</h1>
Code: <?php echo $errorCode ?><br>
<?php echo $errorMsg ?><br>

</body>
</html>
