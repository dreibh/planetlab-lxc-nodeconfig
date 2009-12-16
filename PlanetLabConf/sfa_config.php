<?php
//
// SFA Component Manager configuration   
//
// $Id:
//

$config_directory = "/etc/sfa/";
$default_name = "sfa_component_config";
$file_name = $config_directory . $default_name;
readfile($file_name); 
exit();

?>
