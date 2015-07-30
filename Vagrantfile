# -*- mode: ruby -*-
# vi: set ft=ruby :

# Icinga Web 2 | (c) 2013-2015 Icinga Development Team | GPLv2+

VAGRANTFILE_API_VERSION = "2"
VAGRANT_REQUIRED_VERSION = "1.5.0"

if ! defined? Vagrant.require_version
  if Gem::Version.new(Vagrant::VERSION) < Gem::Version.new(VAGRANT_REQUIRED_VERSION)
    puts "Vagrant >= " + VAGRANT_REQUIRED_VERSION + " required. Your version is " + Vagrant::VERSION
    exit 1
  end
else
  Vagrant.require_version ">= " + VAGRANT_REQUIRED_VERSION
end

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.network "forwarded_port", guest: 80, host: 8080, auto_correct: true
  config.vm.box = "puphpet/centos65-x64"

  config.vm.provider :virtualbox do |v, override|
    override.vm.box = "puppetlabs/centos-6.6-64-puppet"
    v.customize ["modifyvm", :id, "--memory", "1024"]
  end

  config.vm.provider :parallels do |p, override|
    p.name = "Icinga Web 1 Development"
    p.update_guest_tools = true
    p.optimize_power_consumption = false
    p.memory = 1024
    p.cpus = 2
  end

  config.puppet_install.puppet_version = "3.8.1"
  config.vm.synced_folder "./app/cache", "/vagrant/app/cache"
  config.vm.synced_folder "./log", "/vagrant/log"
  config.vm.synced_folder "./app/data/tmp", "/vagrant/app/data/tmp"
  config.vm.provision :puppet do |puppet|
    puppet.module_path = ".vagrant-puppet/modules"
    puppet.manifests_path = ".vagrant-puppet/manifests"
    # puppet.options = "-v -d"
  end
  # config.vm.provision :shell, :path => ".vagrant-puppet/manifests/finalize.sh"
end
