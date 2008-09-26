<?php
//
// part of yum config on nodes
//
// Thierry Parmentelat 
// Copyright (C) 2008 INRIA
//
// $Id: myplc.repo.php 9818 2008-07-04 07:54:06Z thierry $
//

// For PLC_NAME and PLC_BOOT_HOST
include('plc_config.php');

$PLC_NAME = PLC_NAME;
$PLC_BOOT_HOST = PLC_BOOT_HOST;

// Get admin API handle
require_once 'plc_api.php';
global $adm;

if (isset($_REQUEST['gpgcheck'])) {
  $gpgcheck = $_REQUEST['gpgcheck'];
} else {
  $gpgcheck = 0;
}

# get node family
if ( ! isset($_REQUEST['slicefamily'])) {
  # legacy : use the old naming scheme
  $nodefamily="planetlab";
  $pldistro="planetlab";
 } else {
  $slicefamily = $_REQUEST['slicefamily'];
  echo "# slicefamily $slicefamily \n" ;
  list( $pldistro, $fcdistro, $arch) = split ("-", $slicefamily);
  $nodefamily = "$pldistro-$arch";
  echo "# nodefamily $nodefamily \n" ;
 }

# the nodegroups the node is part of
$nodegroup_names=array();

if ( ! isset($_REQUEST['node_id'])) {
  print "# Warning : node_id not set\n";
 } else {
  $node_id=intval($_REQUEST['node_id']);
  echo "# node_id $node_id \n";
  # compute nodegroups
  $nodes = $adm->GetNodes(array('node_id'=>$node_id));
  $nodegroup_ids = $nodes[0]['nodegroup_ids'];
  $nodegroups = $adm->GetNodeGroups($nodegroup_ids);
  foreach ($nodegroups as $nodegroup) {
    $nodegroup_name = $nodegroup['name'];
    $nodegroup_names [] = $nodegroup_name;
    echo "# in nodegroup $nodegroup_name \n" ;
  }
 }

$topdir=$_SERVER['DOCUMENT_ROOT'] . "/install-rpms/";
$topurl="https://$PLC_BOOT_HOST" . "/install-rpms/";


# locate the planetlab repo for this node family & nodegroup
$repo=NULL;
foreach ($nodegroup_names as $nodegroup_name) {
  $groupdir = "$nodefamily-$nodegroup_name";
  # have we got a repo like /install-rpms/planetlab-i386-alpha ?
  echo "# trying " . $topdir . $groupdir . "\n";
  if (is_dir (realpath($topdir . $groupdir))) {
    $repo=array($pldistro, $groupdir, "$PLC_NAME $nodefamily $nodegroup_name");
    break;
  }
}

# if not found yet
if ( ! $repo) {
  echo "# trying default " . $topdir . $nodefamily . "\n";
  if (is_dir (realpath($topdir . $nodefamily))) {
    $repo = array($pldistro, $nodefamily, "$PLC_NAME $nodefamily regular");
  }
 }

# default: if we're here it's probably very wrong
if ( ! $repo) {
  echo "# WARNING: could not find a decent planetlab repo for this node\n";
  $repo = array ($pldistro, "planetlab", "default probably wrong");
  # don't define the repo in this case
  exit;
 }

list( $id, $dir, $name) = $repo;

echo <<< __PLC_REPO__
[$id]
name=$name
baseurl=$topurl/$dir
gpgcheck=$gpgcheck

__PLC_REPO__;

?>
