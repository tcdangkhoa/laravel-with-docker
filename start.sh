#!/bin/sh


#Create database whenever runing the script
# DB="wager"
# USER="user1"
# PASS="examplepass"

# mysql -h mysql -uroot -pmysqlpass -e "CREATE DATABASE $DB CHARACTER SET utf8 COLLATE utf8_general_ci";
# mysql -h mysql -uroot -pmysqlpass -e "CREATE USER $USER@'%' IDENTIFIED BY '$PASS'";
# mysql -h mysql -uroot -pmysqlpass -e "GRANT SELECT, INSERT, UPDATE ON $DB.* TO '$USER'@'%'";

#Composer install
composer install --no-interaction

#Running Migration

#php artisan migrate