#
# $Id$
#
%define url $URL: svn+ssh://thierry@svn.planet-lab.org/svn/WWW/trunk/PLCWWW.spec $

%define name nodeconfig
%define version 4.3
%define taglevel 9

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
URL: %(echo %{url} | cut -d ' ' -f 2)

# We use set everywhere
Requires: php >= 5.0
Requires: PLCAPI >= 4.3
# need the apache user at install-time
Requires: httpd 

%description

The nodeconfig package provides various php scripts that are used to
generate configuration files for nodes. It is taken out of the PLCWWW
module because it has crucial impact on nodes, while PLCWWW can and
does have several implementations at this moment. For historical
reasons these scripts get installed in /var/www/html/PlanetLabConf.

%prep
%setup -q

%build
pushd nodeconfig/yum

KEXCLUDE="exclude=$(../../build/getkexcludes.sh)"

# expand list of kexcludes
for filein in $(find . -name '*.in') ; do
    file=$(echo $filein | sed -e "s,\.in$,,")
    sed -e "s,@KEXCLUDE@,$KEXCLUDE,g" $filein > $file
done

# scan fcdistros and catenate all repos in 'stock.repo' so db-config can be distro-independant

for fcdistro in $(ls); do
    [ -d $fcdistro ] || continue
    pushd $fcdistro/yum.myplc.d
    rm -f stock.repo
    cat *.repo > stock.repo
    popd
done

popd

%install
rm -rf $RPM_BUILD_ROOT

pushd nodeconfig

echo "* nodeconfig: Installing PlanetLabConf pages"

for dir in boot PlanetLabConf PLCAPI ; do
    mkdir -p $RPM_BUILD_ROOT/var/www/html/$dir
    rsync -a --exclude .svn ./$dir/ $RPM_BUILD_ROOT/var/www/html/$dir/
done

# the yum area -- se db-config
# expose (fixed) myplc.repo.php as				            https://<plc>/yum/myplc.repo.php
install -D -m 644 ./yum/myplc.repo.php			     $RPM_BUILD_ROOT/var/www/html/yum/myplc.repo.php
# expose the fcdistro-dependant yum.conf as				    https://<plc>/yum/yum.conf
install -D -m 644 ./yum/%{distroname}/yum.conf		     $RPM_BUILD_ROOT/var/www/html/yum/yum.conf
# expose the (fcdistro-dependant) stock.repo as				    https://<plc>/yum/stock.repo
install -D -m 644 ./yum/%{distroname}/yum.myplc.d/stock.repo $RPM_BUILD_ROOT/var/www/html/yum/stock.repo

popd

%clean
rm -rf $RPM_BUILD_ROOT

%post
# the boot manager upload area
mkdir -p /var/log/bm
chown apache:apache /var/log/bm
chmod 700 /var/log/bm

%files
%defattr(-,root,root,-)
/var/www/html/boot
/var/www/html/PlanetLabConf
/var/www/html/PLCAPI
/var/www/html/yum

%changelog
* Wed Mar 16 2011 S.Çağlar Onur <caglar@verivue.com> - nodeconfig-4.3-9
- cherry-pick fixes from master

* Fri Mar 11 2011 S.Çağlar Onur <caglar@verivue.com> - nodeconfig-4.3-8
- add sl6 support

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


%define module_current_branch 4.2
