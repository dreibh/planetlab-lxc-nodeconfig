#
# $Id: PLCWWW.spec 7881 2008-01-22 14:45:22Z thierry $
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

%description

The nodeconfig package provides various php scripts that are used to
generate configuration files for nodes. It is taken out of the PLCWWW
module because it has crucial impact on nodes, while PLCWWW can and
does have several implementations at this moment. For historical
reasons these scripts get installed in /var/www/html/PlanetLabConf.

%prep
%setup -q

%build
echo "There is no build stage for this component."
echo "All files just need to be installed as is from the codebase."

%install
rm -rf $RPM_BUILD_ROOT

echo "* nodeconfig: Installing PlanetLabConf pages"

for dir in boot PlanetLabConf PLCAPI ; do
    mkdir -p $RPM_BUILD_ROOT/var/www/html/$dir
    rsync -a --exclude .svn ./$dir/ $RPM_BUILD_ROOT/var/www/html/$dir/
done

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root,-)
/var/www/html/boot
/var/www/html/PlanetLabConf
/var/www/html/PLCAPI

%changelog
* Mon Aug 04 2008 Sapan Bhatia <sapanb@cs.princeton.edu> - nodeconfig-5.0-1
- codemux/planetflow change

* Tue Apr 22 2008 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-4.2-4
- keys.php know about monitor
- new sudoers.php script

* Thu Apr 03 2008 Faiyaz Ahmed <faiyaza@cs.princeton.edu> - nodeconfig-4.2-2 nodeconfig-4.2-3
- Added support for centralized PlanetFlow.

* Wed Mar 26 2008 Thierry Parmentelat <thierry.parmentelat@sophia.inria.fr> - nodeconfig-4.2-1 nodeconfig-4.2-2
- integrated /var/www/html/{boot,PLCAPI} from PLCWWW
- former content has moved down into PlanetLabConf


%define module_current_branch 4.2
