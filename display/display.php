<?php

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
    return $ipaddress;
}

function updateDisplayHeartBit($ip)
{
    $sql = "SELECT * FROM `".MYSQL_TABLE_DISPLAY."` WHERE `ip`='$ip'";
    $req = @mysql_query($sql);
    if (!$req) return mysql_error();
    
    $row = mysql_fetch_assoc($req);
    if (!$row) {   
        $sql =  "INSERT INTO `".MYSQL_TABLE_DISPLAY."` (`ip`) VALUES ('$ip')";    
        if (!@mysql_query($sql)) return mysql_error();
    } else {
        $sql =  "UPDATE `".MYSQL_TABLE_DISPLAY."` SET `date` = now() WHERE `ip`='$ip'";    
        if (!@mysql_query($sql)) return mysql_error();
    }
    
    return NULL;
}

function getDisplayAssetId($ip)
{
    $currentAssetID = NULL;
    $sql = "SELECT * FROM `".MYSQL_TABLE_DISPLAY."` WHERE `ip`='$ip'";
    $req = @mysql_query($sql);
    if (!$req) return $currentAssetID;
    $row = mysql_fetch_assoc($req);
    if ($row) $currentAssetID = $row['assetId'];    
    return $currentAssetID;
}

function getDisplayNextAssetId($ip)
{
    $currentAssetID = getDisplayAssetId($ip);
    $sql = 'SELECT * FROM `'.MYSQL_TABLE_ASSETS.'` WHERE `status`='.STATUS_LIVE.' ORDER BY `id` ASC';
    $req = @mysql_query($sql);
    if (!$req) return NULL;
    
    $nextId = NULL;    
    $tmpId = NULL;
    while ($row = mysql_fetch_assoc($req)) {
        $id = $row['id'];
        if ($id==$currentAssetID) $nextId = $tmpId;
        $tmpId = $id;
    }
    if ($nextId == NULL) $nextId=$tmpId;
    return $nextId;
}

function setDisplayAssetId($id, $assetId) {
    $sql = "UPDATE `".MYSQL_TABLE_DISPLAY."` SET `assetId` = $assetId WHERE `ip`='$ip'";
    if (!@mysql_query($sql)) return mysql_error(); 
}
?>