<?php
function getNextAssetId($assetId)
{
    $rows = [];
    $sql = 'SELECT * FROM `'.MYSQL_TABLE_ASSETS.'` WHERE `status`='.STATUS_LIVE.' ORDER BY `priorityKey`, `positionKey` ASC';
    $req = @mysql_query($sql);
    if (!$req) return NULL;    
    while ($row = mysql_fetch_assoc($req)) array_push($rows, $row);
    $count = count($rows);
    if ($count == 0) return NULL;
    
    $rowsIds = (array_column($rows, 'id'));
    $currentIdx = array_search($assetId, $rowsIds);    

    $higherPriority = $rows[$count-1]['priorityKey'];    
    $idx = ($currentIdx + 1) % $count;
    $remaining = $count;
    while($remaining > 0)
    {
        if ($higherPriority == $rows[$idx]['priorityKey']){
          return $rows[$idx]['id'];      
        } 
        $remaining--;
        $idx = ($idx+1) % $count;    
    }

    return NULL;
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
    $startDate    = strtotime($row['startDate']);
    $stopDate     = strtotime($row['stopDate']); 
    $current_date = strtotime("now");
    
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
        if ($startDate != false) {
           if ($startDate >= $current_date) {
               $newStatus = STATUS_PENDING;
               break;
           }
        }
        
        /* NULL means there is no stopDate */
        if ($stopDate != false) {
           if ($current_date >= $stopDate) {
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