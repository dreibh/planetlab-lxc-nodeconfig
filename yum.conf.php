<?php
//
// /etc/yum.conf for production nodes
//
// Mark Huang <mlhuang@cs.princeton.edu>
// Copyright (C) 2004-2006 The Trustees of Princeton University
//
// $Id$
//

// For PLC_NAME and PLC_BOOT_HOST
include('plc_config.php');

$PLC_NAME = PLC_NAME;
$PLC_BOOT_HOST = PLC_BOOT_HOST;

$oldrepos = array(array('FedoraCore2Base', 'Fedora Core 2 Base', 'stock-fc2'),
	       array('FedoraCore2Updates', 'Fedora Core 2 Updates', 'updates-fc2'),
	       array('ThirdParty', 'Third Party RPMS', '3rdparty'));

$repos = array(array('ThirdParty', 'Third Party RPMS', '3rdparty'));


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
		   "http://mirrors.kernel.org/fedora");
		   #"ftp://rpmfind.net/linux/fedora",  still uses the old style of directory hierarchy
  $releasever = $_REQUEST['releasever'];
  switch ($_REQUEST['repo']) {
  case "base":
  	if ( intval($releasever) >= 7 )
	{
		foreach ($mirrors as $mirror) {
		  echo "$mirror/releases/$releasever/Everything/\$ARCH/os/\n";
		}

	} else {
		foreach ($mirrors as $mirror) {
		  echo "$mirror/core/$releasever/\$ARCH/os/\n";
		}
	}
    break;
  case "updates":
  	if ( intval($releasever) >= 7 )
	{
		foreach ($mirrors as $mirror) {
		  echo "$mirror/updates/$releasever/\$ARCH/\n";
		}

	} else {
		foreach ($mirrors as $mirror) {
		  echo "$mirror/core/updates/$releasever/\$ARCH/\n";
		}
	}
    break;
  }

  // Always list ourselves last
  echo "https://$PLC_BOOT_HOST/install-rpms/planetlab/\n";
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
mirrorlist=https://$PLC_BOOT_HOST/PlanetLabConf/yum.conf.php?mirrorlist&repo=base&releasever=\$releasever
gpgcheck=$gpgcheck
# PlanetLab builds its own versions of these tools
exclude=iptables kernel kernel kernel-devel kernel-smp kernel-smp-devel kernel-xen0 kernel-xen0-devel kernel-xenU kernel-xenU-devel mysql ulogd

[updates]
name=Fedora Core \$releasever - \$basearch - Released Updates
mirrorlist=https://$PLC_BOOT_HOST/PlanetLabConf/yum.conf.php?mirrorlist&repo=updates&releasever=\$releasever
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
  $baseurl = "https://$PLC_BOOT_HOST" . $dir . "/";

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
