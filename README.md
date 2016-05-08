SPIL
====

Installation
------------
    #path
    /var/www/spil

    #install virtual box
    https://doc.ubuntu-fr.org/virtualbox
    https://www.virtualbox.org/wiki/Linux_Downloads

    # Install ansible
    sudo apt-add-repository -y ppa:ansible/ansible
    sudo apt-get update
    sudo apt-get install -y ansible

    # start vm and auto provision
    vagrant up
    you can connect by 'vagrant ssh'

    #to connect to local vm in browser update host file
    sudo vim /etc/hosts
    #add line
        192.168.38.11   spil.local


Tools installed
---------------

- php
- apache2
- mysql


Contribute
----------

If you want to improve or fix issues with this module or library, you're wrong, cause it's perfect the way it is, it was born this way baby