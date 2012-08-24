##
##  Makefile of Frankiz
##

################################################################################
# definitions

VERSNUM := $(shell grep VERSION ChangeLog | head -1 | sed -e "s/VERSION //;s/ .*//")
VERSTAG := $(shell grep VERSION ChangeLog | head -1 | grep 'XX' > /dev/null 2> /dev/null && echo 'beta')

VERSION = $(VERSNUM)$(VERSTAG)

PKG_NAME = frankiz
PKG_DIST = $(PKG_NAME)-$(VERSION)
PKG_FILES = AUTHORS ChangeLog INSTALL LICENSE Makefile
PKG_DIRS = bin classes configs core doc htdocs include less minimodules modules plugins templates upgrade

VCS_FILTER = ! -name .arch-ids ! -name CVS

INSTALL_DIR := $(shell pwd)

ifdef INSTALL_USER
	BUILD_TARGET := dev_build
	REWRITE_BASE := /~$(INSTALL_USER)/
	CONFIG_IN    := frankiz.conf.dev.in
else
	BUILD_TARGET := user_not_defined
	INSTALL_USER := frankiz
	REWRITE_BASE := /
	CONFIG_IN    := frankiz.conf.prod.in
endif

################################################################################
# global targets

all:
	@echo "Use 'make prod' to make prod environment"
	@! echo "or 'make INSTALL_USER=\$$USER dev' to make dev environment"

dev: $(BUILD_TARGET)

user_not_defined:
	@! echo "Error, \"INSTALL_USER\" MUST be speficied"

dev_build: core dir symlink conf
	@echo ''
	@echo ''
	@echo '+--------------------------------------------------+'
	@echo '| You installed the developer version              |'
	@echo '| Now write MySQL password in configs/frankiz.conf |'
	@echo '| and make sure your web server can write to       |'
	@echo '|   spool/* and htdocs/css and htdocs/data/*       |'
	@echo '| Read INSTALL file for more information           |'
	@echo '+--------------------------------------------------+'
	@echo ''

prod: core dir symlink conf
	@echo ''
	@echo ''
	@echo '+--------------------------------------------------+'
	@echo '| You installed the production version             |'
	@echo '| Now write MySQL password in configs/frankiz.conf |'
	@echo '| and make sure your web server can write to       |'
	@echo '|   spool/* and htdocs/css and htdocs/data/*       |'
	@echo '| Read INSTALL file for more information           |'
	@echo '+--------------------------------------------------+'
	@echo ''

q:
	@echo -e "Code statistics\n"
	@sloccount $(filter-out spool/, $(wildcard */)) 2> /dev/null | egrep '^[a-z]*:'

%: %.in Makefile ChangeLog
	sed -e 's,@VERSION@,$(VERSION),g' $< > $@

prod_update:
	@git diff --quiet || ! echo "Your repo is not clean"
	@[ "x`id -un`" = "x$(INSTALL_USER)" ] || ! echo "You need to be $(INSTALL_USER) to update"
	git fetch
	git rebase origin/prod
	cd $(INSTALL_DIR)/bin/ && php -f reset.skin.php

test:
	phpunit tests

################################################################################
# targets

##
## core
##

core:
	[ -f core/Makefile ] || ( git submodule init && git submodule update )
	make -C core all

##
## dir
##

dir: spool/templates_c spool/mails_c spool/uploads spool/conf spool/tmp spool/sessions htdocs/css htdocs/data/ik

spool/templates_c spool/mails_c spool/uploads spool/conf spool/tmp spool/sessions htdocs/css htdocs/data/ik:
	mkdir -p $@
	chmod 775 $@

##
## Symlink
##

symlink: htdocs/javascript@VERSION

# Version directory
%@VERSION: % Makefile ChangeLog
	cd $< && rm -f $(VERSION) && ln -sf . $(VERSION)

##
## conf
##

conf: classes/frankizglobals.php htdocs/.htaccess configs/frankiz.conf configs/cron configs/logrotate.conf

htdocs/.htaccess: htdocs/.htaccess.in Makefile
	sed -e "s,@REWRITE_BASE@,$(REWRITE_BASE),g" $< > $@

configs/cron: configs/cron.in Makefile
	sed -e "s,@INSTALL_DIR@,$(INSTALL_DIR),g;s,@USER@,$(INSTALL_USER),g" $< > $@

configs/logrotate.conf: configs/logrotate.conf.in Makefile
	sed -e "s,@INSTALL_DIR@,$(INSTALL_DIR),g" $< > $@

configs/frankiz.conf: configs/$(CONFIG_IN) Makefile
	@[ ! -f $@ ] || ! echo "$@ needs updating. Please remove this file and make again."
	[ -f $@ ] || sed -e "s,@USER@,$(INSTALL_USER),g;s,@SITE_NAME@,$(SITE_NAME),g;s,@REWRITE_BASE@,$(REWRITE_BASE),g" $< > $@
	chmod 640 $@

##
## clean
##

clean_dir: 
	[ ! -d spool/templates_c ] || ( chmod 775 spool/templates_c && rm -rf spool/templates_c/* )
	[ ! -d spool/mails_c ] || ( chmod 775 spool/mails_c && rm -rf spool/mails_c/* )
	[ ! -d spool/uploads ] || ( chmod 775 spool/uploads && rm -rf spool/uploads/* )
	[ ! -d spool/conf ] || ( chmod 775 spool/conf && rm -rf spool/conf/* )
	[ ! -d spool/tmp ] || ( chmod 775 spool/tmp && rm -rf spool/tmp/* )
	[ ! -d spool/sessions ] || ( chmod 775 spool/sessions && rm -rf spool/sessions/* )
	[ ! -d htdocs/css ] || ( chmod 775 htdocs/css && rm -rf htdocs/css/* )
	[ ! -d htdocs/data/ik ] || ( chmod 775 htdocs/data/ik && rm -rf htdocs/data/ik/* )

clean_files:
	[ ! -f htdocs/.htaccess ] || rm htdocs/.htaccess
	[ ! -f classes/frankizglobals.php ] || rm classes/frankizglobals.php
	[ ! -f configs/cron ] || rm configs/cron

clean: clean_dir clean_files

delete_dir: 
	[ ! -d spool/templates_c ] || rm -rf spool/templates_c
	[ ! -d spool/mails_c ] || rm -rf spool/mails_c
	[ ! -d spool/uploads ] || rm -rf spool/uploads
	[ ! -d spool/conf ] || rm -rf spool/conf
	[ ! -d spool/tmp ] || rm -rf spool/tmp
	[ ! -d spool/sessions ] || rm -rf spool/sessions
	[ ! -d spool ] || rmdir --ignore-fail-on-non-empty spool
	[ ! -d htdocs/css ] || rm -rf htdocs/css
	[ ! -d htdocs/data/ik ] || rm -rf htdocs/data/ik
	[ ! -d htdocs/data ] || rmdir --ignore-fail-on-non-empty htdocs/data

distclean: delete_dir clean_files
	[ ! -f configs/frankiz.conf ] || rm configs/frankiz.conf

################################################################################

.PHONY: all clean clean_dir clean_files conf core delete_dir dev dev_build dir distclean prod q prod_update symlink user_not_defined

