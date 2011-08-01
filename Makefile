
# $Id: Makefile,v 1.5 2004/11/25 20:18:39 x99laine Exp $
################################################################################
# definitions

VERSNUM := $(shell grep VERSION ChangeLog | head -1 | sed -e "s/VERSION //;s/ .*//")
VERSTAG := $(shell grep VERSION ChangeLog | head -1 | grep 'XX' > /dev/null 2> /dev/null && echo 'beta')

VERSION = $(VERSNUM)$(VERSTAG)

PKG_NAME = frankiz
PKG_DIST = $(PKG_NAME)-$(VERSION)
PKG_FILES = AUTHORS ChangeLog COPYING README Makefile
PKG_DIRS = configs htdocs include install.d plugins po scripts templates upgrade

VCS_FILTER = ! -name .arch-ids ! -name CVS

define download
@echo "Downloading $@ from $(DOWNLOAD_SRC)"
wget $(DOWNLOAD_SRC) -O $@ -q || ($(RM) $@; exit 1)
endef

INSTALL_DIR := $(shell pwd)
INSTALL_USER := frankiz

################################################################################
# global targets

all: build
	@echo ""
	@echo ""
	@echo "+----------------------------------------------+"
	@echo "| Version de prod installée, ajoutez le passwd |"
	@echo "| MySQL dans configs/frankiz.conf              |"
	@echo "+----------------------------------------------+"
	@echo ""

build: core dir conf

q:
	@echo -e "Code statistics\n"
	@sloccount $(filter-out spool/, $(wildcard */)) 2> /dev/null | egrep '^[a-z]*:'

%: %.in Makefile ChangeLog
	sed -e 's,@VERSION@,$(VERSION),g' $< > $@

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

dir: spool/templates_c spool/mails_c spool/uploads spool/conf spool/tmp spool/sessions htdocs/css

spool/templates_c spool/mails_c spool/uploads spool/conf spool/tmp spool/sessions htdocs/css:
	mkdir -p $@
	chmod 775 $@
	@echo "Need root privileges for \"sudo chgrp apache $@\""
	sudo chgrp apache $@

##
## conf
##

conf: classes/frankizglobals.php htdocs/.htaccess configs/frankiz.conf configs/cron

htdocs/.htaccess: htdocs/.htaccess.in Makefile
	@REWRITE_BASE="/~$$(id -un)"; \
	test "$$REWRITE_BASE" = "/~web" && REWRITE_BASE="/"; \
	sed -e "s,@REWRITE_BASE@,$$REWRITE_BASE,g" $< > $@

configs/cron: configs/cron.in
	[ ! -f configs/cron ] || ( echo "Need root privileges for \"sudo rm $@\"" && sudo rm $@)
	sed -e "s,@INSTALL_DIR@,$(INSTALL_DIR),g;s,@USER@,$(INSTALL_USER),g" $< > $@
	@echo "Need root privileges for \"sudo chown root:root $@\""
	sudo chown root:root $@
	@echo "Need root privileges for \"chmod 644 $@\""
	sudo chmod 644 $@

configs/frankiz.conf: configs/frankiz.conf.in
	@echo "Need root privileges for \"sudo chgrp apache $@\""
	sudo chgrp apache $@
	@echo "Need root privileges for \"chmod 640 $@\""
	sudo chmod 640 $@

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

clean_files:
	[ ! -f htdocs/.htaccess ] || rm htdocs/.htaccess
	[ ! -f classes/frankizglobals.php ] || rm classes/frankizglobals.php
	[ ! -f configs/frankiz.conf ] || rm configs/frankiz.conf
	[ ! -f configs/cron ] || rm configs/cron

clean: clean_dir clean_files

delete_dir: 
	[ ! -d spool/templates_c ] || rm -rf spool/templates_c
	[ ! -d spool/mails_c ] || rm -rf spool/mails_c
	[ ! -d spool/uploads ] || rm -rf spool/uploads
	[ ! -d spool/conf ] || rm -rf spool/conf
	[ ! -d spool/tmp ] || rm -rf spool/tmp
	[ ! -d spool/sessions ] || rm -rf spool/sessions
	[ ! -d htdocs/css ] || rm -rf htdocs/css
	[ ! -d spool ] || rmdir --ignore-fail-on-non-empty spool

distclean: delete_dir clean_files

################################################################################

.PHONY: build clean clean_dir clean_files conf core delete_dir dir distclean q http*

