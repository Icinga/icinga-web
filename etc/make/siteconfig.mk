# Install site configuration templates
inc-install-siteconfig:
	$(INSTALL) -m 755 $(INSTALL_OPTS) etc/sitecfg/icinga-io.site.xml $(DESTDIR)$(prefix)/app/modules/AppKit/config/icinga-io.site.xml
	$(INSTALL) -m 755 $(INSTALL_OPTS) etc/sitecfg/auth.site.xml $(DESTDIR)$(prefix)/app/modules/AppKit/config/auth.site.xml
	$(INSTALL) -m 755 $(INSTALL_OPTS) etc/sitecfg/cronks.site.xml $(DESTDIR)$(prefix)/app/modules/Cronks/config/cronks.site.xml
	$(INSTALL) -m 755 $(INSTALL_OPTS) etc/sitecfg/databases.site.xml $(DESTDIR)$(prefix)/app/config/databases.site.xml
	$(INSTALL) -m 755 $(INSTALL_OPTS) etc/sitecfg/icinga.site.xml $(DESTDIR)$(prefix)/app/config/icinga.site.xml