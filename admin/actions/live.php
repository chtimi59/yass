<?php

function doLive()
{
    if ($_GET['id'] == NULL){            
        $GLOBALS['USERMSG_TYPE'] = 'error';
        $GLOBALS['USERMSG_STR'] = 'Invalid id';
        return false;
    }
    
    $sql = "SELECT * FROM `".MYSQL_TABLE_ASSETS."` WHERE `id`=".$_GET['id'];
    $req = @mysql_query($sql) or sqldie($sql);
    if (!updateAssetStatus(mysql_fetch_assoc($req), true)) {
        $GLOBALS['USERMSG_TYPE'] = 'error';
        $GLOBALS['USERMSG_STR'] = 'db error';
        return false;
    }    
    return true;
} 

?>