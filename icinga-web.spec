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
Version: 1.0.3
#Release: 1%{?dist}
Release: 1
License: GPL
Group: Applications/System
URL: http://www.icinga.org/

Source0: icinga-web-1.0.3.tar.gz

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

%configure \
    --prefix="%{_datadir}/icinga-web" \
    --datadir="%{_datadir}/icinga-web" \
    --datarootdir="%{_datadir}/icinga-web" \
    --with-web-user='%{apacheuser}' \
    --with-web-group='%{apacheuser}' \
    --with-icinga-api='%{_datadir}/icinga/share/icinga-api' \
    --with-web-apache-path=%{apacheconfdir}

# resolve possible wrong files for makefile
#%{__make} devclean

##############################
%install
##############################

%{__rm} -rf %{buildroot}
%{__mkdir} -p %{buildroot}/%{apacheconfdir}
%{__make} install \
    install-apache-config \
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

##############################
%post
##############################

# clean config cache
#%{__rm} -rf %{_datadir}/icinga-web/app/cache/config/*.php

##############################
%clean
##############################

%{__rm} -rf %{buildroot}

##############################
%files
##############################

%config(noreplace) %attr(-,root,root) %{apacheconfdir}/icinga-web.conf
%config(noreplace) %{_datadir}/icinga-web/app/config/databases.xml
%config(noreplace) %{_datadir}/icinga-web/app/modules/Web/config/module.xml

%{_datadir}/icinga-web/app/cache/config

%{_datadir}/icinga-web/app/config

%{_datadir}/icinga-web/app/data

%{_datadir}/icinga-web/app/lib

%{_datadir}/icinga-web/app/modules

%{_datadir}/icinga-web/app/templates

%{_datadir}/icinga-web/app/config.php

%{_datadir}/icinga-web/bin

%{_datadir}/icinga-web/doc

%{_datadir}/icinga-web/etc

%{_datadir}/icinga-web/lib

%{_datadir}/icinga-web/pub


##############################
%changelog
##############################
* Tue Aug 17 2010 Michael Friedrich <michael.friedrich@univie.ac.at> - 1.0.3-1
- updated for 1.0.3, removed fix-priv fix-libs as this is now in make install

* Tue Jun 29 2010 Michael Friedrich <michael.friedrich@univie.ac.at> - 1.0.1-1
- updated for 1.0.1

* Fri Apr 16 2010 Michael Friedrich <michael.friedrich@univie.ac.at> - 0.9.1-1
- initial creation

