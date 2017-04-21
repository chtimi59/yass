<?php

/* Return a unique identifier per Display's Client (i.e. a computer)
   the trick is done by using a 10 years expiration cookie !
*/
function getDisplayId()
{
    $cookie_name = 'clientUUID';    
    if(isset($_COOKIE[$cookie_name])) {
        if (function_exists('com_create_guid')){
            $uuid = trim(com_create_guid(), '{}');
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12);
        }
        setcookie($cookie_name, $uuid, time() + (10 * 365 * 24 * 60 * 60), '/');
    }
    
    $uuid = NULL;
    if(isset($_COOKIE[$cookie_name])) $uuid = $_COOKIE[$cookie_name];
    return $uuid;
}

function getDisplayIp()
{
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
    if ($ipaddress=="::1") $ipaddress="127.0.0.1";    
    return $ipaddress;
}

function updateDisplayHeartBit($id)
{
    $ip = getDisplayIp();
    $sql = "SELECT * FROM `".MYSQL_TABLE_DISPLAY."` WHERE `id`='$id'";
    $req = @mysql_query($sql);
    if (!$req) return mysql_error();
    
    $row = mysql_fetch_assoc($req);
    if (!$row) {   
        $sql =  "INSERT INTO `".MYSQL_TABLE_DISPLAY."` (`id`,`ip`) VALUES ('$id','$ip')";    
        if (!@mysql_query($sql)) return mysql_error();
    } else {
        $sql =  "UPDATE `".MYSQL_TABLE_DISPLAY."` SET `date` = now(), `ip` = '$ip' WHERE `id`='$id'";    
        if (!@mysql_query($sql)) return mysql_error();
    }
    
    return NULL;
}

function getDisplayAssetId($id)
{
    $currentAssetID = NULL;
    $sql = "SELECT * FROM `".MYSQL_TABLE_DISPLAY."` WHERE `id`='$id'";
    $req = @mysql_query($sql);
    if (!$req) return $currentAssetID;
    $row = mysql_fetch_assoc($req);
    if ($row) $currentAssetID = $row['assetId'];    
    return $currentAssetID;
}

function setDisplayAssetId($id, $assetId) {
    $sql = "UPDATE `".MYSQL_TABLE_DISPLAY."` SET `assetId` = $assetId WHERE `id`='$id'";
    if (!@mysql_query($sql)) return mysql_error(); 
}
?>