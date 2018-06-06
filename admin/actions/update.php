<?php
function doUpdate()
{
    $isNewEntry = ($_POST['id'] == NULL);
    $newToAddlAssetFile = ((isset($_FILES['assetFile'])) && $_FILES['assetFile']['size']!=0);
    $newToAddlAssetFile = $newToAddlAssetFile || $isNewEntry;
    
    /* 1- add asset file */
    $assetPath = guid();    
    if ($newToAddlAssetFile)
    {   
        
        $filePath = ASSET_DIR_BASE.$assetPath;

        if ((!isset($_FILES['assetFile'])) || $_FILES['assetFile']['size']==0){            
            $GLOBALS['USERMSG_TYPE'] = 'error';            
            $GLOBALS['USERMSG_STR']  = "No file uploaded, error(".fileErrorMessage($_FILES['assetFile']['error']).")";
            return false;
        }
    
        $validMimes = array( 
            'application/zip' => 'zip',
            'application/x-zip-compressed' => 'zip',
            'image/jpeg' =>'jpg',
            'video/mp4' =>'mp4',
        );
        $fileTmpName = $_FILES["assetFile"]["tmp_name"];
        $fileMime = $_FILES['assetFile']['type'];
        $fileExt = array_key_exists($fileMime, $validMimes) ? $validMimes[$fileMime] : null;    
        
        if (!file_exists('$filePath')) mkdir($filePath, 0777, true);    
        copy('templates/base.php', $filePath."/index.php");
        switch($fileExt){
            case 'zip':            
                move_uploaded_file($fileTmpName, $filePath.'/file.zip');  
                $zip = new ZipArchive;
                if ($zip->open($filePath."/file.zip") === TRUE) {
                    $zip->extractTo($filePath);
                    $zip->close();
                } else {
                   $GLOBALS['USERMSG_TYPE'] = 'error';
                   $GLOBALS['USERMSG_STR']  = 'couldn\'t unzip archive';
                   deleteDir($filePath); 
                   return false;
                }
                break;
                
            case 'jpg':
                move_uploaded_file($fileTmpName, $filePath."/img.jpg");  
                copy('templates/jpg.html', $filePath."/page.html");
                break; 
                
            case 'mp4':
                move_uploaded_file($fileTmpName, $filePath."/vid.mp4");  
                copy('templates/mp4.html', $filePath."/page.html");
                break;     
                
            default:
                $GLOBALS['USERMSG_TYPE'] = 'error';
                $GLOBALS['USERMSG_STR']  = "invalid file type '$fileMime' (zip or jpeg expected)";
                deleteDir($filePath); 
                return false;
        }
    }

    /* 2- update db */    
    if ($isNewEntry) {
        /* 2a. it's a new insertion */
        $req =  "INSERT INTO `".MYSQL_TABLE_ASSETS."` (`name`, `positionKey`, `startDate`, `stopDate`, `duration`, `path`) VALUES (";    
        $req .= ($_POST['name']==NULL)        ? 'NULL, ' : "'".$_POST['name']."', ";
        $req .= ($_POST['positionKey']==NULL) ? 'NULL, ' : "'".$_POST['positionKey']."', ";
        $req .= ($_POST['startDate']==NULL)   ? 'NULL, ' : "'".$_POST['startDate']."', ";
        $req .= ($_POST['stopDate']==NULL)    ? 'NULL, ' : "'".$_POST['stopDate']."', ";
        $req .= ($_POST['duration']==NULL)    ? 'NULL, ' : "'".$_POST['duration']."', ";
        $req .= "'".$assetPath."'";        
        $req .= ")";
        @mysql_query($req) or sqldie($req);  
        
    } else {
        
        /* 2b. it's an update of an existing record */
        $sql = 'SELECT * FROM `'.MYSQL_TABLE_ASSETS.'` WHERE `id`='.$_POST['id'];
        $req = mysql_query($sql) or sqldie($sql);
        $row = @mysql_fetch_assoc($req);
        if ($row) {
            if ($newToAddlAssetFile) {
                // delete old asset file, to replace by the new $assetPath.
                deleteDir(ASSET_DIR_BASE.$row['path']); 
            } else {
                // no $assetPath specified, so read back the orginal one.
                $assetPath = $row['path'];
            }
        } else {
            $GLOBALS['USERMSG_TYPE'] = 'error';
            $GLOBALS['USERMSG_STR']  = 'Invalid id';
            return false;
        }
        
        $req = "UPDATE `".MYSQL_TABLE_ASSETS."` SET ";            
        if ($_POST['name']!=NULL)        $req .= "`name` = '".$_POST['name']."',";
        if ($_POST['positionKey']!=NULL) $req .= "`positionKey` = '".$_POST['positionKey']."',";
        if ($_POST['startDate']!=NULL)   $req .= "`startDate` = '".$_POST['startDate']."',";
        if ($_POST['stopDate']!=NULL) {
            $req .= "`stopDate` = '".$_POST['stopDate']."',";
        } else {
            $req .= "`stopDate` = NULL,";
        }
        if ($_POST['duration']!=NULL)    $req .= "`duration` = '".$_POST['duration']."',";
        $req .= "`path` = '".$assetPath."' ";
        $req .= 'WHERE `id`='.$_POST['id'];
        @mysql_query($req) or sqldie($req);      
        
    }  
    
    $_POST['id'] = NULL;
    $_POST['name'] = NULL;
    $_POST['positionKey'] = NULL;
    $_POST['startDate'] = date('Y-m-d\TH:i:s', strtotime(date('Y-m-d')));
    $_POST['stopDate'] = NULL;
    $_POST['duration'] = 10;
    return true;
}
?>