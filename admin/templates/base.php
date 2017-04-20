<?php
if (isset ($_GET['debug'])) {
    define('BASE_URL',  '../../admin/index.php');
} else {
    define('BASE_URL',  '../../index.php');    
}
if (isset ($_GET['t']) && is_numeric($_GET['t'])){
    $t = $_GET['t'];
    if ($t>0) header("Refresh:$t; url='".BASE_URL."'", true, 303);
}
include('page.html');
echo "\n\n<script>function exitPage() { window.location.href = '".BASE_URL."'; }</script>\n";
?>
