#
# $Id$
#
%define url $URL: svn+ssh://thierry@svn.planet-lab.org/svn/WWW/trunk/PLCWWW.spec $

%define name nodeconfig
%define version 5.0
%define taglevel 1

%define release %{taglevel}%{?pldistro:.%{pldistro}}%{?date:.%{date}}

Summary: PlanetLab Central (PLC) nodes configuration files generator
Name: %{name}
Version: %{version}
Release: %{release}
License: PlanetLab
Group: System Environment/Daemons
Source0: %{name}-%{version}.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root
BuildArch: noarch

Vendor: PlanetLab
Packager: PlanetLab Central <support@planet-lab.org>
Distribution: PlanetLab %{plrelease}
URL: %(echo %{url} | cut -d ' ' -f 2)

# We use set everywhere
Requires: php >= 5.0
Requires: PLCAPI >= 5.0
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
* Wed Sep 10 2008 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-5.0-1
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
