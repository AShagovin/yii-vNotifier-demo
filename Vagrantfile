# -*- mode: ruby -*-
# vi: set ft=ruby :

# vagrant plugin install vagrant-librarian-chef
# vagrant plugin install vagrant-hostsupdater

Vagrant.configure("2") do |config|

  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"

  config.librarian_chef.cheffile_dir = "."

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--cpus", "1"]
    vb.customize ["modifyvm", :id, "--memory", 768]
  end

  config.vm.network :forwarded_port, guest: 80, host: 8080
  config.vm.network :private_network, ip: "192.168.33.10"

  config.vm.synced_folder ".", "/vagrant", nfs: true

  config.hostsupdater.aliases = ["yii-notifier.local"]
  
  config.vm.provision :chef_solo do |chef|
    chef.cookbooks_path = [".chef/site-cookbooks", ".chef/cookbooks"]

    chef.add_recipe "apt"
    chef.add_recipe "apache2"
    chef.add_recipe "php"
    
    chef.add_recipe "apache2::mod_rewrite"
    chef.add_recipe "apache2::mod_php5"
    
    chef.add_recipe "apache2::vhosts"
    
    chef.add_recipe "yii"

    chef.add_recipe "packages"

    chef.json = {
      "php" => {
        "conf_dir" => "/etc/php5/apache2",
        "directives" => {
          "display_errors" => :on,
          "html_errors" => :on,
#          "include_path" => ".:/usr/share/php:/usr/share/pear:/usr/local/lib/php",
        }
      },

      "yii" => {
        "revision" => "1.1.14"
      },

      "packages" => {
        "names" => [ "php5-curl", "vim", "mc", "htop", "iotop" ],
        "reload" => [ "apache2" ]
      }
    }

  end

end
