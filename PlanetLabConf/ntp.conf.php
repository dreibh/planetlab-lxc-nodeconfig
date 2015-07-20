<?php
//
// /etc/ntp.conf generator
//
// Marc Fiuczynski <mef@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
// 

// Get admin API handle
require_once 'plc_api.php';
global $adm;

$config_directory = "/var/www/html/PlanetLabConf/ntp/";
$file_prefix = "ntp.conf.";
$default_name = "default";
$file_name = $config_directory . $file_prefix . $default_name;

// Look up the node
$nodes = $adm->GetNodes(array('node_id' => (int)$_GET['node_id']));
if (!empty($nodes)) {
  $node = $nodes[0];
}

if (!isset($node)) {
  readfile($file_name); 
  exit();
}


$problem_models = array("Dell Precision 340", "Unknown");

$hostname= trim($node['hostname']);
$model= trim($node['model']);

/* pull LatLong data */
$sites = $adm->GetSites(array($node['site_id']));
if (!empty($sites)) {
  $site_name= $sites[0]['name'];
  $mylat= $sites[0]['latitude'];
  $mylong= $sites[0]['longitude'];
} else {
  $site_name= "Unknown";
  $mylat= 0;
  $mylong= 0;
}

/* typical NTP settings */

print( "# node $hostname site $site_name $mylat $mylong $model\n");
print( "driftfile /var/lib/ntp/ntp.drift\n" );
print( "statsdir /var/log/ntpstats/\n" );
print( "disable monitor\n" );
print( "# Permit time synchronization with our time source, but do not\n");
print( "# permit the source to query or modify the service on this system.\n");
print( "restrict default kod nomodify notrap nopeer noquery\n");
print( "restrict -6 default kod nomodify notrap nopeer noquery\n");
print( "restrict 127.0.0.1\n");
print( "restrict -6 ::1\n");

if (is_numeric(array_search($model, $problem_models))) {
  print( "tinker stepout 0\n" );
}

/* Look for config file */

$hostname_bits = explode('.', $hostname);
$chunk_counter = sizeof ($hostname_bits);
$compare_chunk = $hostname ;
$found_file = 0;

/* look for host specific overrides */
$file_name = $config_directory . "host/" . $file_prefix . $compare_chunk ;
if (is_file($file_name)) {
  $chunk_counter = 0;
  $found_file = 1;
}

/* look for domain specific overrides */
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
  readfile($file_name);
} 
else {
  if (is_numeric(array_search($model,$problem_models))) {
    $file_name = $config_directory . $file_prefix . "prob." . $default_name ;
  }
  else {
    $file_name = $config_directory . $file_prefix . $default_name ;
  }
  readfile($file_name); 
} 

?>
