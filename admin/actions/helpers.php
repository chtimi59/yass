<?php

/* investigation of upload errors */
/* upload_max_filesize = 30M
   post_max_size = 30M
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


/* Recursivly delete a Folder */
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
