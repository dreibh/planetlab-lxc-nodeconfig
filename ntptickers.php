<?php

require_once('setup_plconf.php');

/* Look for config file */

$config_directory= "/var/www/html/PlanetLabConf/ntp/";
$file_prefix= "ntp.conf.";
$hostname_bits = explode('.', $hostname);
$chunk_counter = sizeof ($hostname_bits);
$compare_chunk = $hostname ;
$found_file = 0;
$default_name = "default";

/* look for the host specific overrides */
$file_name = $config_directory . "host/". $file_prefix . $compare_chunk ;
if (is_file($file_name)) {
	$chunk_counter = 0;
	$found_file = 1;
}

/* look for the domain specific overrides */
while ($chunk_counter > 0) {
	$file_name = $config_directory . $file_prefix . $compare_chunk ;
	if (is_file($file_name)) {
		$chunk_counter = 0;
		$found_file = 1;
	}
	else {
		array_shift($hostname_bits);
		$compare_chunk = implode('.',$hostname_bits);
		$chunk_counter--;
	}
}

if ($found_file and is_readable($file_name)) {
	$lines=file($file_name);
} 
else {
	$file_name = $config_directory . $file_prefix . $default_name ;
	$lines=file($file_name);
} 

foreach ($lines as $line_num => $line) {
	$line=rtrim($line);
	$elements=explode(' ',$line);
	if ($elements[0] == "server") {
		print ("$elements[1]\n");
	}
}

?>

