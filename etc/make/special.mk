# Install special structures with other rights
inc-install-special:
	# Cache directories"
	$(INSTALL) -m 775 $(INSTALL_OPTS_CACHE) -d $(DESTDIR)$(prefix)/app/cache
	$(INSTALL) -m 775 $(INSTALL_OPTS_CACHE) -d $(DESTDIR)$(prefix)/app/cache/config
	$(INSTALL) -m 775 $(INSTALL_OPTS_CACHE) -d $(DESTDIR)$(prefix)/app/cache/content

	# Writable by webserver for logfiles
	$(INSTALL) -m 775 $(INSTALL_OPTS_WEB) -d $(DESTDIR)$(prefix)/app/data/log
	
	# Binaries
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/agavi $(DESTDIR)$(prefix)/bin/agavi
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/console.php $(DESTDIR)$(prefix)/bin/console.php
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/create-changelog.py $(DESTDIR)$(prefix)/bin/create-changelog.py
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/create-makefile.sh $(DESTDIR)$(prefix)/bin/create-makefile.sh
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/create-rescuescheme.sh $(DESTDIR)$(prefix)/bin/create-rescuescheme.sh
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/doctrinemodels.php $(DESTDIR)$(prefix)/bin/doctrinemodels.php
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/getopts.php $(DESTDIR)$(prefix)/bin/getopts.php
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/loc-create-catalog.pl $(DESTDIR)$(prefix)/bin/loc-create-catalog.pl
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/loc-create-json.sh $(DESTDIR)$(prefix)/bin/loc-create-json.sh
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/loc-create-mo.sh $(DESTDIR)$(prefix)/bin/loc-create-mo.sh
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/loc-merge-template.sh $(DESTDIR)$(prefix)/bin/loc-merge-template.sh
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/loc-merge-template.sh $(DESTDIR)$(prefix)/bin/make-tarball
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/phing $(DESTDIR)$(prefix)/bin/phing
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/rmtmp-files.sh $(DESTDIR)$(prefix)/bin/rmtmp-files.sh
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/testdeps.php $(DESTDIR)$(prefix)/bin/testdeps.php
	$(INSTALL) -m 755 $(INSTALL_OPTS) bin/clearcache.sh $(DESTDIR)$(prefix)/bin/clearcache.sh

	# PHING BUILD Properties
	$(INSTALL) -m 600 $(INSTALL_OPTS) etc/build.properties $(DESTDIR)$(prefix)/etc/build.properties
