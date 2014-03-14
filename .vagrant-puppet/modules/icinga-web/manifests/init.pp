# Class: icinga-web
#
#   This class installs icinga-web.
#
# Parameters:
#
# Actions:
#
# Requires:
#
#   php
#   mysql
#
# Sample Usage:
#
#   include icinga-web
#
class icinga-web {
  include php
  include mysql

  php::extension { [ 'php-mysql', 'php-xml' ]:
    require => [ Class['mysql'] ]
  }

  exec { 'configure-icinga-web':
    path    => '/bin:/usr/bin',
    cwd     => '/vagrant',
    command => 'sh ./configure --prefix=/vagrant --with-devel-mode',
    require => Php::Extension[ 'php-mysql', 'php-xml' ]
  }

  exec { 'make-icinga-web':
    path    => '/bin:/usr/bin',
    cwd     => '/vagrant',
    command => 'make devel-inplace-config',
    require => Exec['configure-icinga-web']
  }

  file { '/etc/httpd/conf.d/icinga-web.conf':
    source    => 'puppet:////vagrant/etc/apache2/icinga-web.conf',
    require   => [ Package['apache'], Exec['make-icinga-web'] ],
    notify    => Service['apache']
  }

  exec { 'create-mysql-icinga-web-db':
    path    => '/bin:/usr/bin',
    unless  => 'mysql -uicinga_web -picinga_web icinga_web',
    command => 'mysql -uroot -e "CREATE DATABASE icinga_web; GRANT ALL ON icinga_web.* TO icinga_web@localhost IDENTIFIED BY \'icinga_web\';"',
    require => Service['mysqld']
  }

  exec { 'populate-icinga-web-mysql-db':
    path    => '/bin:/usr/bin',
    unless  => 'mysql -uicinga_web -picinga_web icinga_web -e "SELECT * FROM nsm_user;" &> /dev/null',
    command => 'mysql -uicinga_web -picinga_web icinga_web < /vagrant/etc/schema/mysql.sql',
    require => [ Exec['make-icinga-web'], Exec['create-mysql-icinga-web-db'] ]
  }
}
