<?php
//
// SFA Component Manager configuration   
//
// $Id:
//

$config_directory = "/etc/sfa/";
$default_name = "sfa_component_config";
$file_name = $config_directory . $default_name;
if (file_exists($filename)) {
    readfile($file_name); 
}
exit();

?>
