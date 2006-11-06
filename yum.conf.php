<?php
//
// /etc/yum.conf for production nodes
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2004-2006 The Trustees of Princeton University
//
// $Id: yum.conf.php,v 1.3 2006/05/18 23:09:43 mlhuang Exp $
//

// For PLC_NAME and PLC_BOOT_HOST
include('plc_config.php');

$PLC_NAME = PLC_NAME;
$BOOT_BASE = PLC_BOOT_HOST;

$repos = array(array('FedoraCore2Base', 'Fedora Core 2 Base', 'stock-fc2'),
	       array('FedoraCore2Updates', 'Fedora Core 2 Updates', 'updates-fc2'),
	       array('ThirdParty', 'Third Party RPMS', '3rdparty'));

if (isset($_REQUEST['alpha'])) {
  $repos[] = array('PlanetLabAlpha', 'PlanetLab Alpha RPMS', 'planetlab-alpha');
  $repos[] = array('FedoraCore2Testing', 'Fedora Core 2 Testing', 'testing-fc2');
} elseif (isset($_REQUEST['beta'])) {
  $repos[] = array('PlanetLabBeta', 'PlanetLab Beta RPMS', 'planetlab-beta');
} elseif (isset($_REQUEST['rollout'])) {
  $repos[] = array('PlanetLab', 'PlanetLab RPMS', 'planetlab-rollout');
} else {
  $repos[] = array('PlanetLab', 'PlanetLab RPMS', 'planetlab');
}

if (isset($_REQUEST['gpgcheck'])) {
  $gpgcheck = $_REQUEST['gpgcheck'];
} else {
  $gpgcheck = 0;
}

echo <<<EOF
[main]
### for yum-2.4 in fc4 (this will be ignored by yum-2.0)
### everything in here, do not scan /etc/yum.repos.d/
reposdir=/dev/null
cachedir=/var/cache/yum
debuglevel=2
logfile=/var/log/yum.log
pkgpolicy=newest
gpgcheck=$gpgcheck


EOF;

// Figure out which repositories we actually have on this
// machine. MyPLC installations, for instance, generally only have
// PlanetLab RPMS installed.
foreach ($repos as $repo) {
  $id = $repo[0];
  $name = $repo[1] . " -- " . "$PLC_NAME Central";
  $dir = "/install-rpms/" . $repo[2];
  $baseurl = "http://$BOOT_BASE" . $dir . "/";

  if (is_dir(realpath($_SERVER['DOCUMENT_ROOT'] . $dir))) {
    echo <<<EOF
[$id]
name=$name
baseurl=$baseurl
gpgcheck=$gpgcheck


EOF;
  }
}

?>