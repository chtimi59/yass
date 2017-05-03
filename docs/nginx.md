# How to install Yass on Nginx

# Prequists

## 1 - GIT
```bash
sudo apt-get install git
```

## 2 - Nginx set upload limit
```bash
sudo cp /etc/nginx/nginx.conf vi /etc/nginx/nginx.conf.$(date "+%b_%d_%Y_%H.%M.%S")
sudo nano /etc/nginx/nginx.conf
```
add the following line in *http* section
```
client_max_body_size 100M;
```

## 3 - PHP
Install php5-fpm
```bash
sudo apt-get -y install php5-fpm
```
edit Nginx *sites-available/default*
```bash
sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.$(date "+%b_%d_%Y_%H.%M.%S")
sudo nano /etc/nginx/sites-available/default
```
in it, add 'index.php' in index list:
```
root /var/www;
index index.html index.htm index.php
```
and allows php5-fpm usage:
```
location ~ \.php$ {
      fastcgi_pass unix:/var/run/php5-fpm.sock;
      fastcgi_index index.php;
      include fastcgi_params;
}
```
then change *php.ini*,
```bash
sudo cp /etc/php5/fpm/php.ini /etc/php5/fpm/php.ini.$(date "+%b_%d_%Y_%H.%M.%S")
sudo nano /etc/php5/fpm/php.ini
```
set cgi.fix_pathinfo to 0:
```
[...]
; cgi.fix_pathinfo provides *real* PATH_INFO/PATH_TRANSLATED support for CGI.  PHP's
; previous behaviour was to set PATH_TRANSLATED to SCRIPT_FILENAME, and to not grok
; what PATH_INFO is.  For more information on PATH_INFO, see the cgi specs.  Setting
; this to 1 will cause PHP CGI to fix its paths to conform to the spec.  A setting
; of zero causes PHP to behave as before.  Default is 1.  You should fix your scripts
; to use SCRIPT_FILENAME rather than PATH_TRANSLATED.
; http://php.net/cgi.fix-pathinfo
cgi.fix_pathinfo=0
[...]
```

set post_max_size to 0 (no limit) and upload_max_filesize to 100M:
```
[...]
; Maximum size of POST data that PHP will accept.
; Its value may be 0 to disable the limit. It is ignored if POST data reading
; is disabled through enable_post_data_reading.
; http://php.net/post-max-size
post_max_size = 0
[...]
; Maximum allowed size for uploaded files.
; http://php.net/upload-max-filesize
upload_max_filesize = 100M
[...]
```

Optinally add a test php in your www folder
```bash
sudo mkdir /var/www/test_php
echo "<?php phpinfo(); ?>" | sudo tee /var/www/test_php/index.php
```
Restart Nginx
```bash
sudo service nginx restart
sudo service php5-fpm restart
```

## 4 - MySQL
install mysql
```bash
sudo apt-get install mysql-server php5-mysql
sudo mysql_install_db
```
You should now have access to mysql CLI:
```bash
~$ sudo mysql -pabc123...
```
and create a dbyass database
```
Welcome to the MySQL monitor.  Commands end with ; or \g.
Your MySQL connection id is 46
Server version: 5.5.55-0ubuntu0.14.04.1 (Ubuntu)

Copyright (c) 2000, 2017, Oracle and/or its affiliates. All rights reserved.

Oracle is a registered trademark of Oracle Corporation and/or its
affiliates. Other names may be trademarks of their respective
owners.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

mysql> create database dbyass
    -> ;
Query OK, 1 row affected (0.00 sec)
```

## 5 - *'mcrypt'* extension
```bash
sudo apt-get install php5-mcrypt
sudo updatedb
sudo php5enmod mcrypt
sudo service php5-fpm restart
sudo service nginx restart
```

## 6 - Create a private vars folder
```bash
sudo mkdir /var/www-private
sudo chmod 777 /var/www-private
```

## 7 - Folder privilege
Make sure, that Nginx can use www folder
```bash
sudo chgrp -cR www-data www
sudo chown -R www-data www
```
