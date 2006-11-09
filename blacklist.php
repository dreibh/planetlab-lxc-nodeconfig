#
# /etc/planetlab/blacklist
#
# post: iptables-restore --noflush < /etc/planetlab/blacklist
#
# PlanetLab per-node outbound blacklist
# 
# Aaron Klingaman <alk@cs.princeton.edu>
# Mark Huang <mlhuang@cs.princeton.edu>
# Copyright (C) 2004 The Trustees of Princeton University
#
# $Id: blacklist.php,v 1.1 2006/11/06 22:02:17 mlhuang Exp $
#

*filter
-F BLACKLIST

<?php

// Get admin API handle
require_once 'plc_api.php';
global $adm;

// Look up the node
$nodenetworks = $adm->GetNodeNetworks(array('ip' => $_SERVER['REMOTE_ADDR']));
if (!empty($nodenetworks)) {
  $nodes = $adm->GetNodes(array($nodenetworks[0]['node_id']));
  if (!empty($nodes)) {
    $node = $nodes[0];
  }
}

if (isset($node)) {
  // XXX Implement generic "networks" table
  // $networks = $adm->GetNetworks();
  $networks = array();
  foreach ($networks as $network) {
    if ($network['blacklisted']) {
      $dest = $network['ip'];
      if ($network['netmask']) {
	$dest .= "/" . $network['netmask'];
      }
      print "-A BLACKLIST -d $dest -j LOGDROP\n";
    }
  }
}

print "\n";

?>

COMMIT
