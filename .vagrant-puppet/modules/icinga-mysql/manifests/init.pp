# Class: icinga-mysql
#
#   This class installs Icinga.
#
# Parameters:
#
# Actions:
#
# Requires:
#
#   icinga-packages
#   mysql
#   nagios-plugins
#
# Sample Usage:
#
#   include icinga-mysql
#
class icinga-mysql {
  include icinga-packages
  include mysql
  include nagios-plugins

  package { [ 'icinga', 'icinga-idoutils-libdbi-mysql' ]:
    ensure  => installed,
    require => Class['icinga-packages']
  }

  service { 'icinga':
    enable  => true,
    ensure  => running,
    require => Package['icinga']
  }

  service { 'ido2db':
    enable  => true,
    ensure  => running,
    require => Package['icinga-idoutils-libdbi-mysql']
  }

  exec { 'create-mysql-icinga-db':
    path    => '/bin:/usr/bin',
    unless  => 'mysql -uicinga -picinga icinga',
    command => 'mysql -uroot -e "CREATE DATABASE icinga; GRANT ALL ON icinga.* TO icinga@localhost IDENTIFIED BY \'icinga\';"',
    require => Service['mysqld']
  }

  exec { 'populate-icinga-mysql-db':
    path    => '/bin:/usr/bin',
    unless  => 'mysql -uicinga -picinga icinga -e "SELECT * FROM icinga_dbversion;" &> /dev/null',
    command => 'mysql -uicinga -picinga icinga < /usr/share/doc/icinga-idoutils-libdbi-mysql-`rpm -q --qf "%{VERSION}\n" icinga-idoutils-libdbi-mysql`/db/mysql/mysql.sql',
    require => [ Package['icinga-idoutils-libdbi-mysql'], Exec['create-mysql-icinga-db'] ]
  }
}
