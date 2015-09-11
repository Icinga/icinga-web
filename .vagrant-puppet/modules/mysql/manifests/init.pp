# Class: mysql
#
#   This class installs the mysql server and client software.
#
# Parameters:
#
# Actions:
#
# Requires:
#
# Sample Usage:
#
#   include mysql
#
class mysql {
  package { [ 'mysql', 'mysql-server', ]:
      ensure => installed,
  }

  service { 'mysqld':
    enable  => true,
    ensure  => running,
    require => Package['mysql-server']
  }

  file { '/etc/my.cnf':
    content => template('mysql/my.cnf.erb'),
    require => Package['mysql-server'],
    notify  => Service['mysqld']
  }
}
