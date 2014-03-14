# Class: nagios-plugins
#
#   This class installs the Monitoring Plugins.
#
# Parameters:
#
# Actions:
#
# Requires:
#
#   repoforge
#
# Sample Usage:
#
#   include nagios-plugins
#
class nagios-plugins {
  require repoforge

  package { 'nagios-plugins':
    ensure  => installed,
    require => Class['repoforge']
  }
}
