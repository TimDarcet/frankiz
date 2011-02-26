
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

################################################################################
# global targets

all: build

build: core conf

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
## conf
##

conf: spool/templates_c spool/mails_c classes/frankizglobals.php htdocs/.htaccess spool/conf spool/tmp spool/sessions

spool/templates_c spool/mails_c spool/uploads spool/conf spool/tmp spool/sessions htdocs/css:
	mkdir -p $@
	chmod ug+w $@

htdocs/.htaccess: htdocs/.htaccess.in Makefile
	@REWRITE_BASE="/~$$(id -un)"; \
	test "$$REWRITE_BASE" = "/~web" && REWRITE_BASE="/"; \
	sed -e "s,@REWRITE_BASE@,$$REWRITE_BASE,g" $< > $@

##
## clean
##

clean_dir: 
	[ ! -d spool/templates_c ] || ( chmod 775 spool/templates_c;  rm -rf spool/templates_c/* )
	[ ! -d spool/mails_c ] || ( chmod 775 spool/mails_c && rm -rf spool/mails_c/* )
	[ ! -d spool/conf ] || ( chmod 775 spool/conf && rm -rf spool/conf/* )
	[ ! -d spool/tmp ] || ( chmod 775 spool/tmp && rm -rf spool/tmp/* )
	[ ! -d spool/sessions ] || ( chmod 775 spool/sessions && rm -rf spool/sessions/* )

clean_files:
	[ ! -f htdocs/.htaccess ] || rm htdocs/.htaccess
	[ ! -f classes/frankizglobals.php ] || rm classes/frankizglobals.php

clean: clean_dir clean_files

delete_dir: 
	[ ! -d spool/templates_c ] || rm -rf spool/templates_c
	[ ! -d spool/mails_c ] || rm -rf spool/mails_c
	[ ! -d spool/conf ] || rm -rf spool/conf
	[ ! -d spool/tmp ] || rm -rf spool/tmp
	[ ! -d spool/sessions ] || rm -rf spool/sessions

distclean: delete_dir clean_files

################################################################################

.PHONY: build dist clean http*

