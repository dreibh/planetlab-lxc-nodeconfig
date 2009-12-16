<?php
//
// SFA Component Manager configuration   
//
// $Id:
//

// Get admin API handle
require_once 'plc_config.php';

$config_directory = "/etc/sfa/";
$default_name = "sfa_component_config";
$file_name = $config_directory . $default_name;
readfile($file_name); 
exit();

?>
