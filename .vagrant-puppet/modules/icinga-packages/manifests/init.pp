# Class: icinga-packages
#
#   Add Icinga release repository.
#
# Parameters:
#
# Actions:
#
# Requires:
#
# Sample Usage:
#
#   include icinga-packages
#
class icinga-packages {
  yumrepo { 'icinga-packages':
    mirrorlist  => 'http://packages.icinga.org/epel/6/release/ICINGA-release.repo',
    # baseurl is required, otherwise mirrorlist errors by yum
    baseurl     => 'http://packages.icinga.org/epel/6/release/',
    enabled     => '1',
    gpgcheck    => '0',
    descr       => "Icinga Packages for Enterprise Linux 6 - ${::architecture}"
  }
}
