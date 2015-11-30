<?php
//
// .ssh/authorized_keys generator
// Node Manager should manage user keys.
// This script remains current for special cases like root, site_admin, and monitor
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

// Get admin API handle
require_once 'plc_api.php';
global $adm;

$persons = array();
$keys = array();

if (!empty($_REQUEST['role'])) {
  // API cannot filter on role_ids nor roles 
  // $persons = $adm->GetPersons(array('roles' => array($_REQUEST['role'])));
  $all_persons = $adm->GetPersons();
  foreach ($all_persons as $person) {
    if (in_array($_REQUEST['role'], $person['roles'])) {
      $persons[] = $person;
    }
  }
}

//
// NOTE: this user is not used by the monitor for the moment.
//
/* // {ple,pl}_monitor user is created on the nodes by the monitor-runlevelagent init script. */
/* if (isset($_REQUEST[PLC_SLICE_PREFIX . '_monitor'])) { */
/*   $user = $adm->GetPersons(array('first_name' => 'Site', 'last_name' => 'Assistant')); */
/*   if (!empty($user)) { */
/*     $persons[] = $user[0]; */
/*   } */
/* } */

if (isset($_REQUEST['site_admin']) && isset($_REQUEST['node_id'])) {
  $nodes = $adm->GetNodes(array(intval($_REQUEST['node_id'])));
  if (!empty($nodes)) {
    $node = $nodes[0];
  }
  if (isset($node)) {
    // Look up the site
    $sites = $adm->GetSites(array($node['site_id']));
    // Can't filter on roles so have to brute force through entire userlist of site.
    if ($sites && $sites[0]['person_ids']) {
      $all_persons = $adm->GetPersons($sites[0]['person_ids']);
      if (!empty($all_persons))
	foreach ($all_persons as $person)
	  if ((in_array('pi', $person['roles']) || in_array('tech', $person['roles'])) && $person['enabled']) 
	    $persons[] = $person; 
    }
  }
}

// Thierry on July 20 2015
// this has ceased to work ages ago with php5, as /etc/planetlab is not
// allowed to php - which is right
// exposing only admin's public keys is quite enough
// which will be a relief for /var/log/php.log where this message is all over the place
// if (isset($_REQUEST['root']))
//  $keys[] = array('key' => file_get_contents(PLC_ROOT_SSH_KEY_PUB));


if (!empty($persons)) {
  $key_ids = array();
  foreach ($persons as $person) {
      if ($person['key_ids']) {
          $person_key_ids = $person['key_ids'];
          foreach ($person_key_ids as $person_key_id) {
              $key_ids[] = $person_key_id;
          }
      }
  }
  if (!empty($key_ids))
    $keys = $adm->GetKeys($key_ids);
}

foreach ($keys as $key)
  print $key['key']. "\n";

?>
