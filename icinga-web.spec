# $Id$
# Authority: michael.friedrich(at)univie.ac.at
# Upstream: The icinga devel team <icinga-devel at lists.sourceforge.net>
# Needs icinga-api
# ExcludeDist: el4 el3

%if "%{_vendor}" == "suse"
%define apacheconfdir  %{_sysconfdir}/apache2/conf.d
%define apacheuser wwwrun
%endif
%if "%{_vendor}" == "redhat"
%define apacheconfdir  %{_sysconfdir}/httpd/conf.d
%define apacheuser apache
%endif

Summary: Open Source host, service and network monitoring Web UI 
Name: icinga-web
Version: 1.0.1
#Release: 1%{?dist}
Release: 1
License: GPL
Group: Applications/System
URL: http://www.icinga.org/

Source0: icinga-web-1.0.1.tar.gz

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root

Requires: php >= 5.2.3
Requires: php-pear
Requires: php-gd
Requires: php-xml
Requires: php-ldap
Requires: php-pdo
Requires: php-dom
Requires: php-common
Requires: php-spl
Requires: pcre >= 7.6

Requires: icinga-api

##############################
%description
##############################

Icinga Web for Icinga Core, requires Icinga API.

##############################
%prep
##############################

%setup -n %{name}-%{version}

##############################
%build
##############################

# before we start, make sure that $(MAKE) fix-priv fix-libs will be kicked out
# we'll apply that ourselves in %post
# instead this is in make install - patch in the copying of etc/build.properties :D
%{__perl} -pi -e '
	s|\$\(MAKE\)\sfix-priv\sfix-libs|\$\(INSTALL\) -m 664 \$\(INSTALL_OPTS\) etc\/build\.properties \$\(DESTDIR\)\$\(prefix\)\/etc\/build\.properties|;
	' Makefile.in


%configure \
    --prefix="%{_datadir}/icinga-web" \
    --datadir="%{_datadir}/icinga-web" \
    --datarootdir="%{_datadir}/icinga-web" \
    --with-web-user='%{apacheuser}' \
    --with-web-group='%{apacheuser}' \
    --with-icinga-api='%{_datadir}/icinga-api' \

# --with-db-type, --with-db-host, --with-db-port, --with-db-name, --with-db-user, --with-db-pass

# resolve possible wrong files for makefile
#%{__make} devclean

##############################
%install
##############################

%{__rm} -rf %{buildroot}
%{__make} install \
    DESTDIR="%{buildroot}" \
    INSTALL_OPTS="" \
    COMMAND_OPTS="" \
    INIT_OPTS=""

##############################
%pre
##############################

#uncomment if building from git
#%{__rm} -rf %{buildroot}%{_datadir}/icinga-web/.git

##############################
%preun
##############################

#uncomment if having problems with cache dir
#%{__rm} -rf %{buildroot}%{_datadir}/icinga-web/app/cache

##############################
%post
##############################

### apply fixes after install
#mkdir %{_datadir}/icinga-web/app/cache

### fix-privs taken from Makefile.in
chown -R %{apacheuser}.%{apacheuser} \
	%{_datadir}/icinga-web/app/cache \
	%{_datadir}/icinga-web/app/data/log	
chmod -R 775 %{_datadir}/icinga-web/app/cache
chmod +x \
	%{_datadir}/icinga-web/bin/agavi \
	%{_datadir}/icinga-web/bin/create-makefile.sh \
	%{_datadir}/icinga-web/bin/create-rescuescheme.sh \
	%{_datadir}/icinga-web/bin/doctrinemodels.php \
	%{_datadir}/icinga-web/bin/phing \
	%{_datadir}/icinga-web/bin/testdeps.php \
	%{_datadir}/icinga-web/bin/loc-create-catalog.pl \
	%{_datadir}/icinga-web/bin/loc-create-json.sh \
	%{_datadir}/icinga-web/bin/loc-create-mo.sh \
	%{_datadir}/icinga-web/bin/loc-merge-template.sh \
	%{_datadir}/icinga-web/bin/rmtmp-files.sh

### fix-libs taken from Makefile.in
rm -rf %{_datadir}/icinga-web/pub/js/ext3
ln -fs %{_datadir}/icinga-web/lib/ext3 %{_datadir}/icinga-web/pub/js
ln -fs %{_datadir}/icinga-api %{_datadir}/icinga-web/lib/


##############################
%files
##############################

%dir %{_datadir}/icinga-web/app/cache/config
%{_datadir}/icinga-web/app/cache/config/.PLACEHOLDER

%dir %{_datadir}/icinga-web/app/config
%config(noreplace) %{_datadir}/icinga-web/app/config/action_filters.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/autoload.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/compile.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/config_handlers.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/databases.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/factories.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/global_filters.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/icinga.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/logging.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/output_types.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/rbac_definitions.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/routing.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/schedules.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/settings.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/translation.xml
%config(noreplace) %{_datadir}/icinga-web/app/config/validators.xml

%{_datadir}/icinga-web/app/data

%{_datadir}/icinga-web/app/lib
%{_datadir}/icinga-web/app/modules
%{_datadir}/icinga-web/app/templates

%{_datadir}/icinga-web/app/config.php

%{_datadir}/icinga-web/bin

%{_datadir}/icinga-web/etc
#%{_datadir}/icinga-web/etc/build.properties
#%{_datadir}/icinga-web/etc/build.xml

%{_datadir}/icinga-web/doc
%{_datadir}/icinga-web/lib
%{_datadir}/icinga-web/pub


##############################
%changelog
##############################
* Tue Jun 29 2010 Michael Friedrich <michael.friedrich@univie.ac.at> - 1.0.1-1
- updated for 1.0.1

* Fri Apr 16 2010 Michael Friedrich <michael.friedrich@univie.ac.at> - 0.9.1-1
- initial creation 


