<?php
//
// /etc/sysctl.conf generator
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
//
// $Id$
//

// Get admin API handle
require_once 'plc_api.php';
global $adm;

$ip_forward = 0;

// Look up the node
$nodenetworks = $adm->GetNodeNetworks(array('ip' => $_SERVER['REMOTE_ADDR']));
if (!empty($nodenetworks)) {
  $nodes = $adm->GetNodes(array($nodenetworks[0]['node_id']));
  if (!empty($nodes)) {
    $node = $nodes[0];
    $nodenetworks = $adm->GetNodeNetworks($node['nodenetwork_ids']);
    foreach ($nodenetworks as $nodenetwork) {
      // Nodes with proxy socket interfaces need to be able to forward
      // between the fake proxy0 interface and the real interface.
      if ($nodenetwork['method'] == 'proxy') {
	$ip_forward = 1;
	break;
      }
    }
  }
}

?>

# Kernel sysctl configuration file for Red Hat Linux
#
# For binary values, 0 is disabled, 1 is enabled.  See sysctl(8) and
# sysctl.conf(5) for more details.

# $Id$

# Controls IP packet forwarding
net.ipv4.ip_forward = <?php echo $ip_forward; ?>

# Controls source route verification
net.ipv4.conf.default.rp_filter = 1

# Controls the System Request debugging functionality of the kernel
kernel.sysrq = 0

# Controls whether core dumps will append the PID to the core filename.
# Useful for debugging multi-threaded applications.
kernel.core_uses_pid = 1

# TCP window scaling and broken routers
net.ipv4.tcp_moderate_rcvbuf=0
net.ipv4.tcp_default_win_scale=0
net.ipv4.tcp_window_scaling=0

# Mark only out of window RST segments as INVALID. This setting, among
# other things, allows data to be sent with SYN packets.
net.ipv4.netfilter.ip_conntrack_tcp_be_liberal=1

# Fixes dst cache overflow bug
net.ipv4.route.max_size=262144
