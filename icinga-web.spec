# $Id$
# Authority: The icinga devel team <icinga-devel at lists.icinga.org>
# Upstream: The icinga devel team <icinga-devel at lists.icinga.org>
# ExcludeDist: el4 el3

%define revision 1

%define logdir %{_localstatedir}/log/%{name}
%define cachedir %{_localstatedir}/cache/%{name}
%define reportingcachedir %{_localstatedir}/cache/%{name}/reporting

%define phpname php

# el5 requires newer php53 rather than php (5.1)
%if 0%{?el5} || 0%{?rhel} == 5 || "%{?dist}" == ".el5"
%define phpname php53
%endif

%if "%{_vendor}" == "suse"
%define apacheconfdir  %{_sysconfdir}/apache2/conf.d
%define apacheuser wwwrun
%define apachegroup www
%define extcmdfile %{_localstatedir}/icinga/rw/icinga.cmd
%endif
%if "%{_vendor}" == "redhat"
%define apacheconfdir %{_sysconfdir}/httpd/conf.d
%define apacheuser apache
%define apachegroup apache
%define extcmdfile %{_localstatedir}/spool/icinga/cmd/icinga.cmd
%endif

Summary:        Open Source host, service and network monitoring Web UI
Name:           icinga-web
Version:        1.14.1
Release:        %{revision}%{?dist}
License:        GPL-3.0+
Group:          Applications/System
URL:            http://www.icinga.org
BuildArch:      noarch

%if "%{_vendor}" == "suse"
AutoReqProv:    Off
%endif

Source0:	https://github.com/Icinga/icinga-web/releases/download/v%{version}/icinga-web-%{version}.tar.gz

BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root

BuildRequires:  make
BuildRequires:  %{phpname} >= 5.2.3
BuildRequires:  %{phpname}-devel >= 5.2.3
BuildRequires:  %{phpname}-gd
BuildRequires:  %{phpname}-ldap
BuildRequires:  %{phpname}-pdo

%if "%{_vendor}" == "redhat"
BuildRequires:  %{phpname}-xml
BuildRequires:  php-pear
%endif
%if "%{_vendor}" == "suse"
# needed for the directory ownership:
BuildRequires:  apache2
BuildRequires:  %{phpname}-json
BuildRequires:  %{phpname}-sockets
BuildRequires:  %{phpname}-xsl
BuildRequires:  %{phpname}-dom
BuildRequires:  %{phpname}-pear
%endif

Requires:       pcre >= 7.6
Requires:       %{phpname} >= 5.2.3
Requires:       %{phpname}-gd
Requires:       %{phpname}-ldap
Requires:       %{phpname}-pdo
%if "%{_vendor}" == "redhat"
Requires:       %{phpname}-common
Requires:       %{phpname}-xml
Requires:       php-pear
%endif
%if "%{_vendor}" == "suse"
Requires:       %{phpname}-pear
Requires:       %{phpname}-xsl
Requires:       %{phpname}-dom
Requires:       %{phpname}-tokenizer
Requires:       %{phpname}-gettext
Requires:       %{phpname}-ctype
Requires:       %{phpname}-json
Requires:       %{phpname}-pear
Requires:       mod_php_any
%endif


%description
Icinga Web for Icinga Core, uses Icinga IDOUtils DB as data source.

%package mysql
Summary:        Database config for mysql
Group:          Applications/System
Requires:       %{name} = %{version}-%{release}
Requires:	%{phpname}-mysql
Conflicts:      %{name}-pgsql

%description mysql
Database config and requirements for mysql for icinga-web

%package pgsql
Summary:        Database config for pgsql
Group:          Applications/System
Requires:       %{name} = %{version}-%{release}
Requires:	%{phpname}-pgsql
Conflicts:      %{name}-mysql

%description pgsql
Database config and requirements for pgsql for icinga-web

%package module-pnp
Summary:        PNP Integration module for Icinga Web
Group:          Applications/System
Requires:       pnp4nagios
Requires:       %{name} = %{version}-%{release}

%description module-pnp
PNP Integration module for Icinga Web

%package scheduler
Summary:	Scheduler for Icinga Web
Group:		Applications/System
Requires:	%{name} = %{version}-%{release}
%if "%{_vendor}" == "suse"
Requires:       cron
%endif
%if "%{_vendor}" == "redhat"
%if 0%{?el5} || 0%{?rhel} == 5 || "%{?dist}" == ".el5"
Requires:    vixie-cron
%else
Requires:       cronie
%endif
%endif

%description scheduler
Scheduler for Icinga Web


%prep
%setup -q -n %{name}-%{version}

%build
%configure \
    --prefix="%{_datadir}/%{name}" \
    --datadir="%{_datadir}/%{name}" \
    --datarootdir="%{_datadir}/%{name}" \
    --sysconfdir="%{_sysconfdir}/%{name}" \
    --with-conf-dir='%{_sysconfdir}/%{name}/conf.d' \
    --with-web-user='%{apacheuser}' \
    --with-web-group='%{apachegroup}' \
    --with-api-cmd-file='%{extcmdfile}' \
    --with-log-dir='%{logdir}' \
    --with-cache-dir='%{cachedir}' \
    --with-reporting-tmp-dir='%{reportingcachedir}' \
    --with-icinga-bin='%{_bindir}/icinga' \
    --with-icinga-cfg='%{_sysconfdir}/icinga/icinga.cfg' \
    --with-icinga-objects-dir='%{_sysconfdir}/icinga/objects' \
    --with-clearcache-path='%{_bindir}' \
    --with-web-apache-path=%{apacheconfdir}

%install
%{__rm} -rf %{buildroot}
%{__mkdir} -p %{buildroot}/%{apacheconfdir}
%{__mkdir} -p %{buildroot}/%{_bindir}
%{__make} install \
    install-apache-config \
    DESTDIR="%{buildroot}" \
    INSTALL_OPTS="" \
    COMMAND_OPTS="" \
    INSTALL_OPTS_WEB="" \
    INSTALL_OPTS_CACHE="" \
    INIT_OPTS=""

# install scheduler
%{__mkdir} -p %{buildroot}%{_sysconfdir}/cron.d/
sed -e "s#%%USER%%#icinga#;s#%%PATH%%#%{_datadir}/%{name}#" etc/scheduler/icingaCron > %{buildroot}%{_sysconfdir}/cron.d/icingaCron
%{__mkdir} -p %{buildroot}%{_localstatedir}/log/icingaCron

# we only want clearcache.sh prefixed in {_bindir}, generated from configure
%{__mv} %{buildroot}%{_bindir}/clearcache.sh %{buildroot}%{_bindir}/%{name}-clearcache

# wipe the rest of bin/, we don't need prepackage stuff in installed envs
%{__rm} -rf %{buildroot}%{_datadir}/%{name}/bin

# place the pnp templates for -module-pnp
%{__cp} contrib/PNP_Integration/templateExtensions/* %{buildroot}%{_datadir}/%{name}/app/modules/Cronks/data/xml/extensions/

%pre
# Add apacheuser in the icingacmd group
# If the group exists, add the apacheuser in the icingacmd group.
# It is not neccessary that icinga-web is installed on the same system as
# icinga and only on systems with icinga installed the icingacmd
# group exists. In all other cases the user used for ssh access has
# to be added to the icingacmd group on the remote icinga server.
getent group icingacmd > /dev/null

if [ $? -eq 0 ]; then
%if "%{_vendor}" == "suse"
%{_sbindir}/usermod -G icingacmd %{apacheuser}
%else
%{_sbindir}/usermod -a -G icingacmd %{apacheuser}
%endif
fi

# uncomment if building from git
# %{__rm} -rf %{buildroot}%{_datadir}/icinga-web/.git

%preun
%if "%{_vendor}" == "suse"
	%restart_on_update apache2
%endif

%post
# clean config cache, e.g. after upgrading
%{name}-clearcache

%if "%{_vendor}" == "suse"
	a2enmod rewrite
	%restart_on_update apache2
%endif

%postun
%if "%{_vendor}" == "suse"
        %restart_on_update apache2
%endif

%post pgsql
### change databases.xml to match pgsql config
# check if this is an upgrade
if [ $1 -eq 2 ]
then
        %{__cp} %{_sysconfdir}/%{name}/conf.d/databases.xml %{_sysconfdir}/%{name}/conf.d/databases.xml.pgsql
        %{__perl} -pi -e '
                s|db_servertype=mysql|db_servertype=pgsql|;
                s|db_port=3306|db_port=5432|;
                ' %{_sysconfdir}/%{name}/conf.d/databases.xml.pgsql
        %logmsg "Warning: upgrade, pgsql config written to databases.xml.pgsql"
fi
# install
if [ $1 -eq 1 ]
then
        %{__perl} -pi -e '
                s|db_servertype=mysql|db_servertype=pgsql|;
                s|db_port=3306|db_port=5432|;
                ' %{_sysconfdir}/%{name}/conf.d/databases.xml
fi

%post module-pnp
# clean cronk template cache
%{name}-clearcache

%postun module-pnp
if [ -f %{_sbindir}/%{name}-clearcache ]; then
	%{name}-clearcache
fi

%clean
%{__rm} -rf %{buildroot}

%files
# main dirs
%defattr(-,root,root)
%if "%{_vendor}" == "redhat"
%doc etc/schema doc/README.RHEL doc/AUTHORS doc/CHANGELOG-1.14 doc/LICENSE
%endif
%if "%{_vendor}" == "suse"
%doc etc/schema doc/README.SUSE doc/AUTHORS doc/CHANGELOG-1.14 doc/LICENSE
%endif
# packaged by subpackages
%exclude %{_datadir}/%{name}/app/modules/Cronks/data/xml/extensions
%exclude %{_sysconfdir}/%{name}/conf.d/databases.xml
%{_datadir}/%{name}
%{_datadir}/%{name}/app
%{_datadir}/%{name}/doc
%{_datadir}/%{name}/etc
%{_datadir}/%{name}/lib
%{_datadir}/%{name}/pub
# configs
%defattr(-,root,root)
%config(noreplace) %attr(-,root,root) %{apacheconfdir}/icinga-web.conf
%dir %{_sysconfdir}/%{name}
%dir %{_sysconfdir}/%{name}/conf.d
%config(noreplace) %attr(644,-,-) %{_sysconfdir}/%{name}/conf.d/*
# logs+cache
%attr(2775,%{apacheuser},%{apachegroup}) %dir %{logdir}
%attr(-,%{apacheuser},%{apachegroup}) %{cachedir}
%attr(-,%{apacheuser},%{apachegroup}) %{cachedir}/config
# data directory writable for web server
%attr(-,%{apacheuser},%{apachegroup})  %{_datadir}/%{name}/app/data/tmp
# binaries
%defattr(-,root,root)
%{_bindir}/%{name}-clearcache
# stylesheet
%config(noreplace) %{_datadir}/%{name}/pub/styles/icinga.site.css

%files mysql
%config(noreplace) %attr(644,-,-) %{_sysconfdir}/%{name}/conf.d/databases.xml

%files pgsql
%config(noreplace) %attr(644,-,-) %{_sysconfdir}/%{name}/conf.d/databases.xml


%files module-pnp
# templates, experimental treatment as configs (noreplace)
%defattr(-,root,root)
%doc contrib/PNP_Integration/README contrib/PNP_Integration/INSTALL
%doc contrib/PNP_Integration/doc contrib/nginx
%dir %{_datadir}/icinga-web/app/modules/Cronks/data/xml/extensions
%config(noreplace) %attr(644,-,-) %{_datadir}/%{name}/app/modules/Cronks/data/xml/extensions/*

%files scheduler
%defattr(-,root,root)
%{_sysconfdir}/cron.d/icingaCron
%attr(-,icinga,icinga) %{_localstatedir}/log/icingaCron

%changelog
