<?php
session_start();

//unset($_SESSION['clientUUID']);
    
include('../defines.php');
include('display.php');
include('../scheduler.php');

$errorCode = '';
$errorMsg = '';
$date = date('m/d/Y h:i:s a', time());
$displayIp = getDisplayIp();
$displayId = '';
$assetUrl = '';

$cookiesAreDisabled = false;

    
do
{    
	if(!@include("../conf.php")) {
        $errorCode = 'a01';
		$errorMsg = "Setup missing";
		break;
	}
    
    /* 0. get Display id */
    $displayId = getDisplayId();
    if ($displayId == NULL) {
        $cookiesAreDisabled = true;
        $errorCode = 'a01';
		$errorMsg = "Couldn't get/set an ID for this display (does cookies are desactivated ?)";
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
    
	/* 2. Update global scheduler */
    updateScheduler();
	        
    /* 3. update db with Client-Display */
    $ret = updateDisplayHeartBit($displayId);
    if ($ret!=NULL) { $errorCode='c01'; $errorMsg=$ret; break; }
    
    /* 4- find next assetId */
	$assetId = getNextAssetId(getDisplayAssetId($displayId));
    if ($assetId==NULL) { $errorCode='d01'; $errorMsg='Playlist is empty'; break; }        
    
    /* 5- Apply */
    setDisplayAssetId($displayId, $assetId);        
    $sql = "SELECT * FROM `".MYSQL_TABLE_ASSETS."` WHERE `id`=$assetId";
    $req = @mysql_query($sql);
    if (!$req) { $errorCode='e01'; $errorMsg=mysql_error(); break; }
    $row = mysql_fetch_assoc($req);
    if (!$row) { $errorCode='e02'; $errorMsg='Playlist is empty'; break; }    
    if (!isset($row['path']))  { $errorCode='e03'; $errorMsg='Bad db formating'; break; }
    if (!isset($row['duration'])) { $errorCode='e04'; $errorMsg='Bad db formating'; break; }    
    $assetUrl = ASSET_URL_BASE.$row['path'].'/index.php?t='.$row['duration'];        
    
    /* 6- Actual redirection */
    if (!isset ($_GET['debug'])) {
        header('Location: '.$assetUrl);      
        exit();    
    }
    break;
    
} while(0);

/* fail so auto-refresh (10s) */
if ((!$cookiesAreDisabled) && isset ($_GET['debug'])) {
    header("Refresh:10; url='index.php'", true, 303);
}
?>

<html>
<body>
<h1>
<?php echo $date ?><br>
</h1>
<ul style="list-style-type: none;">
<li>Ip address: <?php echo $displayIp ?></li>
<li>Client UUID: <?php echo $displayId ?></li>
<?php 
if ($errorCode!='') {
    echo "<li>&nbsp;</li>\n";
    echo "<li>Error code: $errorCode</li>\n";
    echo "<li>$errorMsg</li>\n";
} ?>
</ul>
<?php if (isset ($_GET['debug'])) echo $assetUrl; ?>
</body>
</html>
