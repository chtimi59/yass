<?php

date_default_timezone_set('America/Montreal');

define('MYSQL_HOST',         "localhost");
define('MYSQL_USER',         "slideshow");
define('MYSQL_PW',           "abc123");
define('MYSQL_DB',           "slideshow");
define('MYSQL_TABLE_ASSETS', "assets");
define('MYSQL_TABLE_DISPLAY',"displays");
define('MYSQL_TABLE_RUN',    "run");

define('ASSET_PATH',  dirname(__FILE__) . '/assets/');

/* Asset Status */
define('STATUS_BACKSTAGE', 0);
define('STATUS_PENDING',   1);
define('STATUS_LIVE',      2);
define('STATUS_FINISHED',  3);

function updateAssetsStatus() {    
    $sql = 'SELECT * FROM `'.MYSQL_TABLE_ASSETS.'`';
    $req = @mysql_query($sql) or sqldie($sql);
    while ($row = mysql_fetch_assoc($req)) {
        updateAssetStatus($row);
    }
}

function updateAssetStatus($row, $exitBackStage=false)
{   
    if (!$row) return false;
    
    $status       = $row['status'];
    $startDate    = $row['startDate'];
    $stopDate     = $row['stopDate']; 
    $current_date = date("Y-m-d");    
    
    /* compute new status value */
    $newStatus = $status;
    do
    {
        /* There is nothing to do if asset is in backstage */
        if (($status == STATUS_BACKSTAGE) && (!$exitBackStage)) {
          $newStatus = STATUS_BACKSTAGE;
          break;        
        }
        
        /* NULL means startDate is not revelant */
        if ($startDate!=NULL) {
           // current_date < startDate ? 
           if (strcmp($current_date, $startDate) < 0) {
               $newStatus = STATUS_PENDING;
               break;
           }
        }
        
        /* NULL means there is no stopDate */
        if ($stopDate!=NULL) {
           // stopDate < current_date ? 
           if (strcmp($stopDate, $current_date) < 0) {
               $newStatus = STATUS_FINISHED;
               break;
           }
        }
        
        /* asset is live then! */
        $newStatus = STATUS_LIVE;
        
    } while(0);
    
    if ($newStatus == $status) return true; //nothing done
    $req = "UPDATE `".MYSQL_TABLE_ASSETS."` SET `status` = $newStatus WHERE `id`=".$row['id'];
    return @mysql_query($req);
}

/* Slideshow sequeduler */

function GetCurrentBroadcastedAssetId() {
    $sql = 'SELECT * FROM `'.MYSQL_TABLE_RUN.'`';
    $req = @mysql_query($sql);
    if (!$req) return NULL;
    
    if ($row = mysql_fetch_assoc($req)){
        return $row['id'];
    }
    return NULL;
}

function SetCurrentBroadcastedAssetId($id) {
    $sql = "UPDATE `".MYSQL_TABLE_RUN."` SET `id` = '".$id."' LIMIT 1";
    $req = @mysql_query($sql);
}

function GetNextBroadcastedAssetId() {
    $currId = GetCurrentBroadcastedAssetId();
    $sql = 'SELECT * FROM `'.MYSQL_TABLE_ASSETS.'` WHERE `status`='.STATUS_LIVE.' ORDER BY `id` ASC';
    $req = @mysql_query($sql);
    if (!$req) return NULL;
    
    $nextId = NULL;    
    $tmpId = NULL;
    while ($row = mysql_fetch_assoc($req)) {
        $id = $row['id'];
        if ($id==$currId)     $nextId = $tmpId;
        $tmpId = $id;
    }
    if ($nextId == NULL) $nextId=$tmpId;
    return $nextId;
}

/* client IP */
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/* reload index.php without params */
function endOk() {
    header('Location: index.php');
    exit();
}

/* Die if sql error */
function sqldie($sql) {
    echo("'$sql'<br>\n<br>\n");
    die(mysql_error());
}

/* Create a GUID */
function guid(){
    if (function_exists('com_create_guid')){
        return trim(com_create_guid(), '{}');
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
         $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
        return $uuid;
    }
}

?>