#
# how to push a working directoy to a running plc
#

tags:
	find . -type f | grep -v '/\.svn/' | egrep -v '/(uudecode.gz|tags|TAGS)$$' | xargs etags

.PHONY: tags

########## make sync PLCHOST=hostname VSERVER=vservername
ifdef PLCHOST
ifdef VSERVER
PLCSSH:=root@$(PLCHOST):/vservers/$(VSERVER)
endif
endif

LOCAL_RSYNC_EXCLUDES	:= --exclude '*.pyc' 
RSYNC_EXCLUDES		:= --exclude .svn --exclude CVS --exclude '*~' --exclude TAGS $(LOCAL_RSYNC_EXCLUDES)
RSYNC_COND_DRY_RUN	:= $(if $(findstring n,$(MAKEFLAGS)),--dry-run,)
RSYNC			:= rsync -a -v $(RSYNC_COND_DRY_RUN) $(RSYNC_EXCLUDES)

sync:
ifeq (,$(PLCSSH))
	echo "sync: You must define PLCHOST and VSERVER on the command line"
	echo " e.g. make sync PLCHOST=private.one-lab.org VSERVER=myplc01" ; exit 1
else
	+$(RSYNC) PlanetLabConf boot PLCAPI $(PLCSSH)/var/www/html/
	ssh root@$(PLCHOST) vserver $(VSERVER) exec apachectl graceful
endif

