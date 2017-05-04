<?php
function getNextAssetId($assetId)
{
	$nextAssetId = NULL;
    $sql = 'SELECT * FROM `'.MYSQL_TABLE_ASSETS.'` WHERE `status`='.STATUS_LIVE.' ORDER BY `positionKey` DESC';
    $req = @mysql_query($sql);
    if (!$req) return $nextAssetId;
    
    $tmpId = NULL;
    while ($row = mysql_fetch_assoc($req)) {
        if ($row['id']==$assetId) $nextAssetId = $tmpId;
        $tmpId = $row['id'];
    }
    if ($nextAssetId == NULL) $nextAssetId=$tmpId;
    return $nextAssetId;
}

function updateScheduler() {    
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


?>