#
# /etc/planetlab/proxies
#
# post: service vnet restart
#
# Proxy (a.k.a. network telescope a.k.a. honeypot) interface configuration
#
# Aaron Klingaman <alk@cs.princeton.edu>
# Mark Huang <mlhuang@cs.princeton.edu>
# Copyright (C) 2004 The Trustees of Princeton University
#
# $Id$
#

<?php
// Get admin API handle
require_once 'plc_api.php';
global $adm;

// Look up the node
$interfaces = $adm->GetInterfaces(array('ip' => $_SERVER['REMOTE_ADDR']));
if (!empty($interfaces)) {
  $nodes = $adm->GetNodes(array($interfaces[0]['node_id']));
  if (!empty($nodes)) {
    $node = $nodes[0];
  }
}

if (!isset($node)) {
  exit();
}

$interfaces = $adm->GetInterfaces($node['interface_ids']);

foreach ($interfaces as $interface) {
  // XXX PL2896: need interfaces.device
  switch ($interface['method']) {
  case 'tap':
    $dev = "tap0";
    $types['taps'][$dev][0] = $interface['ip'] . "/" . $interface['gateway'];
    break;
  case 'proxy':
    $dev = "proxy0";
    $types['proxies'][$dev][] = $interface['ip'];
    break;
  }
}

// taps="tap0 tap1 ..."
// tap0="1.2.3.4/5.6.7.8"
// tap1="9.10.11.12/13.14.15.16"
// ...
// proxies="proxy0 proxy1 ..."
// proxy0="1.2.3.4 5.6.7.8 ..."
// proxy1="9.10.11.12 13.14.15.16 ..."
// ...
if (isset($types)) {
  foreach ($types as $type => $devs) {
    print("$type=\"" . implode(" ", array_keys($devs)) . "\"\n");
    foreach ($devs as $dev => $ips) {
      print("$dev=\"" . implode(" ", $ips) . "\"\n");
    }
  }
}

?>
