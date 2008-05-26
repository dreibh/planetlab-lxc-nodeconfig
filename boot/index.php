<?php
//
// Returns node boot script
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2006 The Trustees of Princeton University
//
// $Id$ $
//

// Get admin API handle
require_once 'plc_api.php';
global $adm;

// Default bootmanager
$bootmanager = "bootmanager.sh.sgn";

// Look up the node
$interfaces = $adm->GetInterfaces(array('ip' => $_SERVER['REMOTE_ADDR']));
if (!empty($interfaces)) {
  $nodes = $adm->GetNodes(array($interfaces[0]['node_id']));
  if (!empty($nodes)) {
    $node = $nodes[0];
  }
}

if (isset($node)) {
  // Allow very old nodes that do not have a node key in their
  // configuration files to use their "boot nonce" instead. The boot
  // nonce is a random value generated by the node itself and POSTed
  // by the Boot CD when it requests the Boot Manager. This is
  // obviously not very secure, so we only allow it to be used if the
  // requestor IP is the same as the IP address we have on record for
  // the node.

  // 3.x CDs post 'version', 2.x CDs post 'id'.
  if (!empty($_REQUEST['version'])) {
    $version = trim($_REQUEST['version']);
  } elseif (!empty($_REQUEST['id'])) {
    $version = trim($_REQUEST['id']);
  } else {
    $version = "2.0";
  }

  if (empty($node['key']) && !empty($_REQUEST['nonce'])) {
    // 3.x CDs post the boot nonce in ASCII hex. 2.x CDs post it in binary.
    if (strstr($version, "2.0") === FALSE) {
      // 3.x CDs post a trailing newline...sigh
      $nonce = trim($_REQUEST['nonce']);
    } else {
      $nonce = bin2hex($_REQUEST['nonce']);
    }
    $adm->UpdateNode($node['node_id'], array('boot_nonce' => $nonce));
  }

  // Custom bootmanager for the node, e.g.
  // planetlab-1.cs.princeton.edu_bootmanager.sh.sgn
  $bootmanagers = array(strtolower($node['hostname']) . "_" . $bootmanager);

  // Custom bootmanager for the node group, e.g.
  // alpha_bootmanager.sh.sgn
  if (!empty($node['nodegroup_ids'])) {
    $nodegroups = $adm->GetNodeGroups($node['nodegroup_ids']);
    foreach ($nodegroups as $nodegroup) {
      $bootmanagers[] = strtolower($nodegroup['groupname']) . "_" . $bootmanager;
    }
  }
}

// Default bootmanager
$bootmanagers[] = $bootmanager;

foreach ($bootmanagers as $bootmanager) {
  if (file_exists($bootmanager)) {
    readfile($bootmanager);
    exit();
  }
}

?>
