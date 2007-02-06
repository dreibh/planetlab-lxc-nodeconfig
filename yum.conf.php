<?php
//
// /etc/yum.conf for production nodes
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2004-2006 The Trustees of Princeton University
//
// $Id: yum.conf.php,v 1.3 2007/02/06 19:00:57 mlhuang Exp $
//

// For PLC_NAME and PLC_BOOT_HOST
include('plc_config.php');

$PLC_NAME = PLC_NAME;
$PLC_BOOT_HOST = PLC_BOOT_HOST;

$repos = array(array('FedoraCore2Base', 'Fedora Core 2 Base', 'stock-fc2'),
	       array('FedoraCore2Updates', 'Fedora Core 2 Updates', 'updates-fc2'),
	       array('ThirdParty', 'Third Party RPMS', '3rdparty'));

if (isset($_REQUEST['alpha'])) {
  $repos[] = array('PlanetLabAlpha', 'PlanetLab Alpha RPMS', 'planetlab-alpha');
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

// Requesting a mirror list. Yum bombs out completely if a repository
// is (even temporarily) unavailable, so if CoBlitz is down, provide a
// few more options. Make sure that gpgcheck remains enabled.  Last
// chance option is ourselves so that yum never fails.
if (isset($_REQUEST['mirrorlist']) &&
    isset($_REQUEST['repo']) &&
    isset($_REQUEST['releasever'])) {
  $mirrors = array("http://coblitz.planet-lab.org/pub/fedora/linux",
		   "http://fedora.gtlib.cc.gatech.edu/pub/fedora.redhat/linux",
		   "http://download.fedoraproject.org/pub/fedora/linux",
		   "ftp://rpmfind.net/linux/fedora",
		   "http://mirrors.kernel.org/fedora");
  $releasever = $_REQUEST['releasever'];
  switch ($_REQUEST['repo']) {
  case "base":
    foreach ($mirrors as $mirror) {
      echo "$mirror/core/$releasever/\$ARCH/os/\n";
    }
    break;
  case "updates":
    foreach ($mirrors as $mirror) {
      echo "$mirror/core/updates/$releasever/\$ARCH/\n";
    }
    break;
  case "extras":
    foreach ($mirrors as $mirror) {
      echo "$mirror/extras/$releasever/\$ARCH/\n";
    }
    break;
  }

  // Always list ourselves last
  echo "http://$PLC_BOOT_HOST/install-rpms/planetlab/\n";
  exit;
}

// Requesting yum.conf. See above for the mirrorlist definition.
echo <<<EOF
[main]
# Do not scan /etc/yum.repos.d/
reposdir=/dev/null
cachedir=/var/cache/yum
debuglevel=2
logfile=/var/log/yum.log
pkgpolicy=newest
gpgcheck=$gpgcheck

[base]
name=Fedora Core \$releasever - \$basearch - Base
mirrorlist=http://$PLC_BOOT_HOST/PlanetLabConf/yum.conf.php?mirrorlist&repo=base&releasever=\$releasever
gpgcheck=$gpgcheck
# PlanetLab builds its own versions of these tools
exclude=iptables kernel kernel kernel-devel kernel-smp kernel-smp-devel kernel-xen0 kernel-xen0-devel kernel-xenU kernel-xenU-devel mysql ulogd

[updates]
name=Fedora Core \$releasever - \$basearch - Released Updates
mirrorlist=http://$PLC_BOOT_HOST/PlanetLabConf/yum.conf.php?mirrorlist&repo=updates&releasever=\$releasever
gpgcheck=$gpgcheck
# PlanetLab builds its own versions of these tools
exclude=iptables kernel kernel kernel-devel kernel-smp kernel-smp-devel kernel-xen0 kernel-xen0-devel kernel-xenU kernel-xenU-devel mysql ulogd

[extras]
name=Fedora Extras \$releasever - \$basearch
mirrorlist=http://$PLC_BOOT_HOST/PlanetLabConf/yum.conf.php?mirrorlist&repo=extras&releasever=\$releasever
gpgcheck=$gpgcheck
# PlanetLab builds its own versions of these tools
exclude=iptables kernel kernel kernel-devel kernel-smp kernel-smp-devel kernel-xen0 kernel-xen0-devel kernel-xenU kernel-xenU-devel mysql ulogd


EOF;

// Figure out which repositories we actually have on this
// machine. MyPLC installations, for instance, generally only have
// PlanetLab RPMS installed.
foreach ($repos as $repo) {
  $id = $repo[0];
  $name = $repo[1] . " -- " . "$PLC_NAME Central";
  $dir = "/install-rpms/" . $repo[2];
  $baseurl = "http://$PLC_BOOT_HOST" . $dir . "/";

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