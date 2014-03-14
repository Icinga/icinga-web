# Class: repoforge
#
#   Configure RepoForge repository.
#
# Parameters:
#
# Actions:
#
# Requires:
#
# Sample Usage:
#
#   include repoforge
#
class repoforge {
  yumrepo { 'repoforge':
    mirrorlist => 'http://mirrorlist.repoforge.org/el6/mirrors-rpmforge',
    enabled    => '1',
    gpgcheck   => '0',
    descr      => 'RepoForge'
  }
}
