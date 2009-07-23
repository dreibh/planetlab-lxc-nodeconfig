<?php

require_once 'plc_api.php';
global $adm;

if (isset($_REQUEST['node_id'])) {
    $node_id = intval($_REQUEST['node_id']);
    print $adm->GetNodeExtensions($node_id);
}

?>

