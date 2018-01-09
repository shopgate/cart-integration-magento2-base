#!/usr/bin/env bash

# todo-sg: not tested, for further setup @see https://github.com/astorm/magento2-travis/blob/master/.travis.yml
MAGE2_URL=http://magento-2-travis.dev
MAGE2_ADMIN_EMAIL=system@shopgate.com
MAGE2_ADMIN_FIRST_NAME=Firstname
MAGE2_ADMIN_LAST_NAME=Lastname
MAGE2_ADMIN_USERNAME=admin
MAGE2_ADMIN_PASSWORD=test.123
MAGE2_DBNAME=magento2_travis
# some useful debugging stuff for travis
# curl http://magento-2-travis.dev/index.php
# curl http://magento-2-travis.dev/
# sudo find /var/log/apache2 -exec cat '{}' \;
# sdo cat /etc/apache2/sites-available/000-default.conf
# sudo cat /etc/apache2/sites-enabled/000-default.conf
# sudo apachectl -V
# sudo apache2ctl -V
# ls -lh $TRAVIS_BUILD_DIR
# sudo ls /etc/apache2/sites-available
# sudo ls /etc/apache2/sites-enabled
# pwd
#
# get latest composer
composer selfupdate
# disable xdebug for perf
echo '' > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
# add multiverse support to get MySQL 5.6 update apt
sudo add-apt-repository "deb http://archive.ubuntu.com/ubuntu/ trusty multiverse" && sudo add-apt-repository "deb http://archive.ubuntu.com/ubuntu/ trusty-updates multiverse"
sudo apt-get update -qq
# add mysql 5.6
sudo apt-get remove -y -qq --purge mysql-common mysql-server-5.5 mysql-server-core-5.5 mysql-client-5.5 mysql-client-core-5.5
sudo apt-get -y -qq autoremove;
sudo apt-get -y -qq autoclean;
sudo apt-get install -y -qq mysql-server-5.6 mysql-client-5.6;
mysql -uroot -e "SET @@global.sql_mode = NO_ENGINE_SUBSTITUTION; CREATE DATABASE ${MAGE2_DBNAME};";
# add apache
sudo apt-get install -y -qq apache2 libapache2-mod-fastcgi
#   enable php-fpm -- www.conf.default is PHP 7 only, so we dev/null any copy problems
sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d/www.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d/www.conf 2>/dev/null || true
sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf
sudo a2enmod rewrite actions fastcgi alias
echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
#   awful hack used during travis debugging that I swear I'm going to remove
#   but then again you're reading this so I didn't remove it and **sigh**
sudo chmod 777 /home /home/travis /home/travis/build

#   configure apache virtual hosts, assumes 000-default.conf is there
sudo cp -f .travis/travis-ci-apache /etc/apache2/sites-available/000-default.conf
sudo sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/000-default.conf
# restart apache
sudo service apache2 restart

# clone main magento github repository
git clone https://github.com/magento/magento2
# install Magento
cd magento2
# switch to specific branch in you like
#git checkout 2.0;git checkout tags/2.2.2
# run installation command using evn variables set above
php bin/magento setup:install --admin-email "$MAGE2_ADMIN_EMAIL" --admin-firstname "$MAGE2_ADMIN_FIRST_NAME" --admin-lastname "$MAGE2_ADMIN_LAST_NAME" --admin-password "$MAGE2_ADMIN_PASSWORD" --admin-user "$MAGE2_ADMIN_USERNAME" --backend-frontname admin --base-url "$MAGE2_URL" --db-host 127.0.0.1 --db-name magento_2_travis --db-user root --session-save files --use-rewrites 1 --use-secure 0 -vvv
# test that magento is installed
curl "$MAGE2_URL/index.php" > /tmp/output.txt
