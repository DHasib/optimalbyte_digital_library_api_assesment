# create databases
CREATE DATABASE IF NOT EXISTS `dg_library_db`;

# create local_developer user and grant rights
CREATE USER 'local_developer'@'mysql_DB' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'local_developer'@'%';
