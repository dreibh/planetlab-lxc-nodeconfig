# RootResources/pl_conf

PL_CONF_VINIT = """#!/bin/sh

# crond needs syslogd running
/sbin/chkconfig syslog on

# use crond to check for updates
/sbin/chkconfig crond on

cat <<EOF >/etc/cron.daily/yum-upgrade
#!/bin/sh

rm -f /var/lib/rpm/__db*
yum -y upgrade
EOF
chmod a+x /etc/cron.daily/yum-upgrade

PACKAGE=sidewinder-PlanetLab-SCS
rm -f /var/lib/rpm/__db*
yum -y install $PACKAGE
"""

try:
    pl_conf = RootSlice(nm_vserver_flags = "static",
                        nm_cpu_share = 32,
                        nm_initscript = PL_CONF_VINIT)
except:
    pass
