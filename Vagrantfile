# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|


 config.vm.define "spil"

 config.vm.box = "ubuntu/trusty64"
 config.vm.box_url = "https://atlas.hashicorp.com/ubuntu/boxes/trusty64/versions/20150516.0.0/providers/virtualbox.box"

 config.vm.network "private_network", ip: "192.168.38.11"
 config.vm.network :forwarded_port, guest: 80, host: 8888
 config.vm.hostname = "spil.local"

 config.vm.synced_folder ".", "/vagrant", disabled: true
 config.vm.synced_folder ".", "/var/www/spil/", id: "vagrant-root",
   nfs: true

 config.vm.provider :virtualbox do |vb|
   vb.gui = false
   vb.customize ["modifyvm", :id, "--memory", "2048", "--cpus", "2"]
 end

 # http://foo-o-rama.com/vagrant--stdin-is-not-a-tty--fix.html
 config.vm.provision "fix-no-tty", type: "shell" do |s|
    s.privileged = false
    s.inline = "sudo sed -i '/tty/!s/mesg n/tty -s \\&\\& mesg n/' /root/.profile"
 end

 # automatically cd to project dir on login
 config.vm.provision :shell, inline: "echo 'cd /var/www/spil/' > /home/vagrant/.bashrc"

 # automatically enable command line xdebug config
 config.vm.provision :shell, inline: "echo 'export XDEBUG_CONFIG=\"idekey=PHPSTORM\"' >> /home/vagrant/.bashrc"
 config.vm.provision :shell, inline: "echo 'alias php=\"php -dxdebug.remote_host=`netstat -rn | grep \"^0.0.0.0 \" | cut -d \" \" -f10`\"' >> /home/vagrant/.bashrc"


 config.vm.provision :ansible do |ansible|
   ansible.raw_arguments = ['--timeout=300']
   ansible.playbook = ".ansible/spilplaybook.yml"
   ansible.verbose = "vvv"
 end


 # call phing configure
 config.vm.provision :shell, privileged:false, inline: "/usr/bin/phing -f /var/www/spil/build.xml -propertyfile /var/www/spil/build.properties -verbose"


end
