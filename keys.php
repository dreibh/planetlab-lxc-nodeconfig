<?php
//
// Deprecated. Node Manager should manage keys.
//
// .ssh/authorized_keys generator
//
// Basic usage:
// keys.php?role=admin (all PlanetLab administrators)
// keys.php?root (PlanetLab root and users allowed root on the querying node)
// keys.php?site_admin (PIs and tech contacts at the querying node's site)
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Aaron Klingaman <alk@cs.princeton.edu>
// Copyright (C) 2004 The Trustees of Princeton University
//
// $Id: keys.php,v 1.1 2006/11/06 22:02:17 mlhuang Exp $
//

// Get admin API handle
require_once 'plc_api.php';
global $adm;

$persons = array();
$keys = array();

if (!empty($_REQUEST['role'])) {
  // XXX Implement API query filters
  // $persons = $adm->GetPersons(array('roles' => array($_REQUEST['role'])));
  $all_persons = $adm->GetPersons();
  foreach ($all_persons as $person) {
    if (in_array($_REQUEST['role'], $person['roles'])) {
      $persons[] = $person;
    }
  }
}

if (isset($_REQUEST['site_admin'])) {
  // Look up the node
  $nodenetworks = $adm->GetNodeNetworks(array('ip' => $_SERVER['REMOTE_ADDR']));
  if (!empty($nodenetworks)) {
    $nodes = $adm->GetNodes(array($nodenetworks[0]['node_id']));
    if (!empty($nodes)) {
      $node = $nodes[0];
    }
  }
  
  if (isset($node)) {
    // Look up the site
    $sites = $adm->GetSites(array($node['site_id']));
    if ($sites && $sites[0]['person_ids']) {
      // XXX Implement API query filters
      // $persons = $adm->GetPersons(array('person_id' => $sites[0]['person_ids'],
      //				   'roles' => array('pi')));
      // $persons += $adm->GetPersons(array('person_id' => $sites[0]['person_ids'],
      //				    'roles' => array('tech')));
      $all_persons = $adm->GetPersons($sites[0]['person_ids']);
      foreach ($all_persons as $person) {
	if (in_array('pi', $person['roles']) ||
	    in_array('tech', $person['roles'])) {
	  $persons[] = $person;
	}
      }
    }
  }
}

if (isset($_REQUEST['root'])) {
  $keys[] = array('key' => file_get_contents(PLC_ROOT_SSH_KEY_PUB));
}

if (!empty($persons)) {
  $key_ids = array();
  foreach ($persons as $person) {
    $key_ids = $key_ids + $person['key_ids'];
  }

  if (!empty($key_ids)) {
    $keys = $keys + $adm->GetKeys($key_ids);
  }
}

foreach ($keys as $key) {
  print $key['key'];
}

?>
