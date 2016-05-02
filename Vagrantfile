# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "debian/jessie64"
  config.vm.network "private_network", ip: "192.168.99.99"
  config.vm.synced_folder ".", "/vagrant", id: "v-root", mount_options: ["rw", "tcp", "nolock", "noacl", "async"], type: "nfs", nfs_udp: false
  config.vm.provider "virtualbox" do |vb|
    vb.memory = "8096"
    vb.cpus = "4"
    vb.name = "symfony-microservice-edition"
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
  end

  config.vm.provision "shell", inline: <<-SHELL
     echo 'deb http://www.rabbitmq.com/debian/ testing main' | sudo tee /etc/apt/sources.list.d/rabbitmq.list
     echo 'deb http://packages.dotdeb.org jessie all' | sudo tee /etc/apt/sources.list.d/dotdeb.list
     wget -O- https://www.rabbitmq.com/rabbitmq-signing-key-public.asc | sudo apt-key add -
     wget -O- https://www.dotdeb.org/dotdeb.gpg | sudo apt-key add -

     # install
     apt-get update
     apt-get install -y curl git php7.0-cli php7.0-curl php7.0-intl php7.0-mysql php7.0-redis rabbitmq-server redis-server vim

     rabbitmq-plugins enable rabbitmq_management

     cat >/etc/php/mods-available/custom.ini <<EOF
date.timezone = 'UTC'
error_reporting = E_ALL
display_errors = On
display_startup_errors = On
EOF

     ln -s /etc/php/mods-available/custom.ini /etc/php/7.0/mods-available/99-custom.ini

     echo "127.0.0.1 rabbitmq.dev" | sudo tee -a /etc/hosts >/dev/null
     echo "127.0.0.1 redis.dev" | sudo tee -a /etc/hosts >/dev/null
     sudo curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony
     sudo chmod +x /usr/local/bin/symfony

     php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
     sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
     php -r "unlink('composer-setup.php');"

  SHELL
end
