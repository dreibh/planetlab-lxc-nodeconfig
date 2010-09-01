<?php
//
// installs the sfa_component_setup cron job  
//
// $Id:
//

$default_name = "sfa_component_setup.cron";
$file_name = $default_name;
if (file_exists($file_name)) {
    readfile($file_name);
}
exit();

?>
