<?php
//
// /etc/ntp/step-tickers generator
//
// Marc Fiuczynski <mef@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
// 
// $Id: step-tickers.php,v 1.1 2006/11/06 22:02:17 mlhuang Exp $
//

// Get admin API handle
require_once 'plc_api.php';
global $adm;

$config_directory= "/var/www/html/PlanetLabConf/ntp/";
$file_prefix= "ntp.conf.";
$default_name = "default";
$file_name = $config_directory . $file_prefix . $default_name;

// Look up the node
$nodenetworks = $adm->GetNodeNetworks(array('ip' => $_SERVER['REMOTE_ADDR']));
if (!empty($nodenetworks)) {
  $nodes = $adm->GetNodes(array($nodenetworks[0]['node_id']));
  if (!empty($nodes)) {
    $node = $nodes[0];
  }
}

if (!isset($node)) {
  readfile($file_name); 
  exit();
}

$hostname= trim($node['hostname']);

/* Look for config file */

$hostname_bits = explode('.', $hostname);
$chunk_counter = sizeof ($hostname_bits);
$compare_chunk = $hostname ;
$found_file = 0;

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