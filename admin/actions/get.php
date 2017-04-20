<?php
function doGet()
{    
    if ($_GET['id'] == NULL){   
        $GLOBALS['USERMSG_TYPE'] = 'error';   
        $GLOBALS['USERMSG_STR']  = 'Invalid id';
        return false;
    }
    
    /* 1- get db entry */
    $sql = 'SELECT * FROM `'.MYSQL_TABLE_ASSETS.'` WHERE `id`='.$_GET['id'];
    $req = @mysql_query($sql) or sqldie($sql);  ;
    $row = @mysql_fetch_assoc($req);
    if ($row) {
        $_POST = $row;
    } else {
        $GLOBALS['USERMSG_TYPE'] = 'error';
        $GLOBALS['USERMSG_STR']  = 'Invalid id';
        return false;
    }    
    
    return true;
}
?>