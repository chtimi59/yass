<?php
function doDelete()
{
    if ($_GET['id'] == NULL){            
        $GLOBALS['USERMSG_TYPE'] = 'error';
        $GLOBALS['USERMSG_STR'] = 'Invalid id';
        return false;
    }
    
    /* 1- delete asset file */
    $sql = 'SELECT * FROM `'.MYSQL_TABLE_ASSETS.'` WHERE `id`='.$_GET['id'];
    $req = @mysql_query($sql) or sqldie($sql);  ;
    $row = @mysql_fetch_assoc($req);
    if ($row) {
        deleteDir(ASSET_PATH.$row['assetId']); 
    } else {
        $GLOBALS['USERMSG_TYPE'] = 'error';
        $GLOBALS['USERMSG_STR']  = 'Invalid id';
        return false;
    }
    
    /* 2- delete db entry */
    $sql = 'DELETE FROM `'.MYSQL_TABLE_ASSETS.'` WHERE `id`='.$_GET['id'];
    $req = @mysql_query($sql) or sqldie($sql);  ;
    $row = @mysql_fetch_assoc($req);
    return true;
}
?>