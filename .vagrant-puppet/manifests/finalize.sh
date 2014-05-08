#!/bin/bash

set -e

mountIcingaWebAppCache () {
    # Remount /vagrant/app/cache/ with appropriate permissions since the group apache is missing initially
    mount -t vboxsf -o uid=`id -u vagrant`,gid=`id -g apache`,dmode=775,fmode=775 /vagrant/app/cache/ /vagrant/app/cache/
}

mountIcingaWebLog () {
    # Remount /vagrant/log/ with appropriate permissions since the group apache is missing initially
    mount -t vboxsf -o uid=`id -u vagrant`,gid=`id -g apache`,dmode=775,fmode=775 /vagrant/log/ /vagrant/log/
}

mountIcingaWebAppDataTmp () {
    # Remount /vagrant/app/data/tmp/ with appropriate permissions since the group apache is missing initially
    mount -t vboxsf -o uid=`id -u vagrant`,gid=`id -g apache`,dmode=775,fmode=775 /vagrant/app/data/tmp/ /vagrant/app/data/tmp/
}

mountIcingaWebAppCache
mountIcingaWebLog
mountIcingaWebAppDataTmp

exit 0
