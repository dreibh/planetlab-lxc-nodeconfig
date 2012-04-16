#

%define name nodeconfig
%define version 5.0
%define taglevel 7

%define release %{taglevel}%{?pldistro:.%{pldistro}}%{?date:.%{date}}

Summary: PlanetLab Central (PLC) nodes configuration files generator
Name: %{name}
Version: %{version}
Release: %{release}
License: PlanetLab
Group: System Environment/Daemons
Source0: %{name}-%{version}.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root
# cannot do this as of f12
#BuildArch: noarch

Vendor: PlanetLab
Packager: PlanetLab Central <support@planet-lab.org>
Distribution: PlanetLab %{plrelease}
URL: %{SCMURL}

# We use set everywhere
Requires: php >= 5.0
Requires: PLCAPI >= 4.3

%description

The nodeconfig package provides various php scripts that are used to
generate configuration files for nodes. It is taken out of the PLCWWW
module because it has crucial impact on nodes, while PLCWWW can and
does have several implementations at this moment. For historical
reasons these scripts get installed in /var/www/html/PlanetLabConf.

%prep
%setup -q

%build

%install
rm -rf $RPM_BUILD_ROOT

echo "* nodeconfig: Installing PlanetLabConf pages"

for dir in PlanetLabConf PLCAPI ; do
    mkdir -p $RPM_BUILD_ROOT/var/www/html/$dir
    rsync -a --exclude .svn ./$dir/ $RPM_BUILD_ROOT/var/www/html/$dir/
done

# Install db-config.d files
echo "* Installing db-config.d files"
mkdir -p ${RPM_BUILD_ROOT}/etc/planetlab/db-config.d
cp db-config.d/* ${RPM_BUILD_ROOT}/etc/planetlab/db-config.d
chmod 444 ${RPM_BUILD_ROOT}/etc/planetlab/db-config.d/*

%clean
rm -rf $RPM_BUILD_ROOT

%post

%files
%defattr(-,root,root,-)
/var/www/html/PlanetLabConf
/var/www/html/PLCAPI
/etc/planetlab/db-config.d

%changelog
* Mon Apr 16 2012 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-5.0-7
- no change, just reworked make sync to cope with lxc-hosted tests

* Fri Feb 24 2012 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-5.0-6
- support for hotfixes

* Tue Feb 01 2011 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-5.0-5
- remove backward compat to GetNodeNetworks and the like

* Sun Jan 23 2011 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-5.0-4
- trashed sfa_component_setup job & conf file

* Wed Sep 01 2010 Baris Metin <Talip-Baris.Metin@sophia.inria.fr> - nodeconfig-5.0-3
- check file before reading the configuration.

* Fri Apr 02 2010 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-5.0-2
- new php scripts for configuring sfa component cron

* Fri Jan 29 2010 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-5.0-1
- first working version of 5.0:
- pld.c/, db-config.d/ and nodeconfig/ scripts should now sit in the module they belong to
- the yum/ and boot/ subdirs have moved away from here

* Wed Dec 23 2009 Marc Fiuczynski <mef@cs.princeton.edu> - nodeconfig-4.3-7
- - decompose PlanetLabConf/sysctl.php into sysctl.conf and
- sysctl-ip_forward.php. This is in prep of phasing out the latter
- altogether, as enabling ip_forwarding should be something that is
- managed by NM.
- - PlanetLabConf/ntptickers.php migrated PlanetLabConf/ntp/step-tickers.php
- - Added sfa_config.php
- - updated ntp server set for .de (german) nodes.
- - f12 related changes from Thierry/Baris

* Sun Nov 22 2009 Marc Fiuczynski <mef@cs.princeton.edu> - nodeconfig-4.3-6
- For all MyPLC nodes deployed at Polish Telecom (PLC, PLE and CoBlitz),
- use the TP local NTP servers.

* Mon Sep 07 2009 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-4.3-5
- new script for updating the exentions set
- keys.php reviewed
- cleanup useless scripts

* Sat Jul 04 2009 Stephen Soltesz <soltesz@cs.princeton.edu> - nodeconfig-4.3-4
- add two views to the PLC config data, limited and unlimited.
- unlimited view reports all values.  Requires the 'infrastructure=1' tag and
- that the call originates from the node.

* Fri May 15 2009 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-4.3-3
- changes to sysctl.conf for co* relating to tcp window scaling

* Tue Mar 24 2009 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-4.3-2
- renumbered 4.3
- new script upload-bmlog.php
- attempts to ship decent yum configs for stock repos to nodes
- cleanup old stuff
- attempts to be 4.2 compatible

* Wed Sep 10 2008 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-4.3-1
- reflects new names from the data model

* Tue Apr 22 2008 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-4.2-4
- keys.php know about monitor
- new sudoers.php script

* Thu Apr 03 2008 Faiyaz Ahmed <faiyaza@cs.princeton.edu> - nodeconfig-4.2-2 nodeconfig-4.2-3
- Added support for centralized PlanetFlow.

* Wed Mar 26 2008 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-4.2-1 nodeconfig-4.2-2
- integrated /var/www/html/{boot,PLCAPI} from PLCWWW
- former content has moved down into PlanetLabConf


%define module_current_branch 4.3
