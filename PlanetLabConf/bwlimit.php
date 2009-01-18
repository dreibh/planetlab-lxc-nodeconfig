<?php
//
// Deprecated. Node Manager manages the node bandwidth limit.
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
//
// $Id$
//

// Get admin API handle
require_once 'plc_api.php';
global $adm;

// Look up the node
// backwards compatibility with the old 4.2 API
global $__PLC_API_VERSION;
if ( ! method_exists ($adm,"GetInterfaces"))
  $__PLC_API_VERSION = 4.2;
else
  $__PLC_API_VERSION = 4.3;

if ($__PLC_API_VERSION==4.2)
  $interfaces = $adm->GetNodeNetworks(array('ip' => $_SERVER['REMOTE_ADDR']));
else
  $interfaces = $adm->GetInterfaces(array('ip' => $_SERVER['REMOTE_ADDR']));

if (!empty($interfaces)) {
  if ($interfaces[0]['bwlimit'] !== NULL) {
    $rate = $interfaces[0]['bwlimit'];
    if ($rate >= 1000000000 && ($rate % 1000000000) == 0) {
      printf("%.0fgbit", ($rate / 1000000000.));
    } elseif ($rate >= 1000000 && ($rate % 1000000) == 0) {
      printf("%.0fmbit", ($rate / 1000000.));
    } elseif ($rate >= 1000) {
      printf("%.0fkbit", ($rate / 1000.));
    } else {
      printf("%.0fbit", $rate);
    }
  } else {
    print "-1";
  }
}

?>
