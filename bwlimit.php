<?php
//
// Deprecated. Node Manager manages the node bandwidth limit.
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
//
// $Id: bwlimit.php,v 1.1 2006/11/06 22:02:17 mlhuang Exp $
//

// Get admin API handle
require_once 'plc_api.php';
global $adm;

// Look up the node
$nodenetworks = $adm->GetNodeNetworks(array('ip' => $_SERVER['REMOTE_ADDR']));
if (!empty($nodenetworks)) {
  if ($nodenetworks[0]['bwlimit'] !== NULL) {
    $rate = $nodenetworks[0]['bwlimit'];
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