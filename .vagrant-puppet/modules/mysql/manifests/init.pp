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
  package { [ 'mariadb', 'mariadb-server', ]:
      ensure => installed,
  }

  service { 'mariadb':
    enable  => true,
    ensure  => running,
    require => Package['mariadb-server']
  }

  file { '/etc/my.cnf':
    content => template('mysql/my.cnf.erb'),
    require => Package['mariadb-server'],
    notify  => Service['mariadb']
  }
}
