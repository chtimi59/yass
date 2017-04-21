<?php

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

/* create guid RFC 4122 */
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

/* investigation of upload errors
   -- upload_max_filesize = 30M
   -- post_max_size = 30M
*/
function fileErrorMessage($i) {
    switch($i) {
        case 0: return 'no error';
        case 1: return  'exceed upload_max_filesize in php.ini';
        case 2: return  'exceed MAX_FILE_SIZE in HTML';
        case 3: return  'partial upload';
        case 4: return  'no file uploaded';
        case 6: return  'tmp folder missing';
        case 7: return  'write error';
        case 8: return  'php ext off';
        default: return '??';
    }
}


/* Recursively delete a Folder */
function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        return;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}
?>
