# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "ubuntu/xenial64"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  config.vm.synced_folder ".", "/home/ubuntu/www", owner: "www-data", group: "www-data"

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
  #   vb.gui = true
  #
  #   # Customize the amount of memory on the VM:
    vb.memory = "1024"
  end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  # config.push.define "atlas" do |push|
  #   push.app = "YOUR_ATLAS_USERNAME/YOUR_APPLICATION_NAME"
  # end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  config.vm.provision "shell", inline: <<-SHELL

  	# Variables
  	echo -e "\n--- Set variables ---\n"
  	DBUSER=dbuser
  	DBPASSWORD=veryverystrongandlongpassword
  	DBNAME=symfony
  	DOMAIN=tzswivl.thiz.biz
  	
  	# Preconfigure mysql root password
  	echo -e "\n--- Preconfigure mysql root password ---\n"
  	echo "mysql-server mysql-server/root_password password $DBPASSWORD" | debconf-set-selections
  	echo "mysql-server mysql-server/root_password_again password $DBPASSWORD" | debconf-set-selections
  	
  	# Main installations
  	echo -e "\n--- Apt-get update ---\n"
  	apt-get update
  	echo -e "\n--- Install unzip, php, nginx, mysql-client, mysql-server ---\n"
  	apt-get install --assume-yes unzip nginx mysql-client mysql-server php php7.0-xml php7.0-intl php7.0-mysql phpunit

  	# DB installation tuning
  	echo -e "\n--- DB installation tuning ---\n"
  	mysql -uroot -p$DBPASSWORD -e "CREATE DATABASE $DBNAME"
  	mysql -uroot -p$DBPASSWORD -e "grant all privileges on $DBNAME.* to '$DBUSER'@'localhost' identified by '$DBPASSWORD'"
  	
  	cd www

  	# Composer installation
  	echo -e "\n--- Composer installation ---\n"
  	wget https://getcomposer.org/installer
  	php ./installer
  	rm ./installer
  	
  	# Nginx site config
  	echo -e "\n--- Nginx site config ---\n"
  	cat vagrant_nginx >> /etc/nginx/sites-available/$DOMAIN
  	sed -i "s/theredomainname/$DOMAIN/g" /etc/nginx/sites-available/$DOMAIN
  	ln -s /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/$DOMAIN
  	
  	# Add ubuntu user to www-data group (for composer)
   	echo -e "\n--- Add ubuntu user to www-data group ---\n"
 	usermod -a -G www-data ubuntu

  SHELL
end
