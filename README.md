Webiness Inventory
==================


### __Description__

"Webiness Inventory" is small, web based, inventory managment system.


### __Features__

* manage product categories with different VAT rates
* manage products
* 3 types of the documents: sale, purchase and dismission
* 2 statuses of the document: proposal and approved
* reports: inventory list and protuct cards
* managment of suppliers and custumers (partners)
* user managment


### __Installation__

* clone git repository into root directory of your web server
```bash
git clone https://github.com/webiness/webiness_inventory
```
* make runtime directory writable by the web server
```bash
chmod 777 webiness_inventory/runtime
# on Linux distributions that use SELinux security mechanism (CentOS, Fedora, RedHat EL, etc.)
# also enter the following commands:
chcon -R -t httpd_sys_rw_content_t user
chcon -R -t httpd_sys_rw_content_t webiness_inventory/runtime
```
* create database in your MySQL/MariaDB or PostgreSQL database server. (If you use SQLite DB then skip this step)
* edit __webiness_inventory/protected/config/config.php__ file and change configuration to match your settings:
```php
WsConfig::set('db_driver', 'pgsql'); // change to 'mysql' for MySQL server or to 'sqlite' for SQLite DB
WsConfig::set('db_host', 'your_db_hostname'); // ignored for SQLite DB
WsConfig::set('db_name', 'database_name'); // ignored for SQLite DB
WsConfig::set('db_user', 'database_user_name'); // ignored for SQLite DB
WsConfig::set('db_password', 'database_user_password'); // ignored for SQLite DB
```
* in the same file find line:
```php
WsConfig::set('auth_admin', 'bojan.kajfes@gmail.com');
```
and replace given email address to the one that will be used for addministration access.
Default password will be automaticaly set to: __admin__
* point your web browser to installed application and login with he user data described above

### __Requirements__

* web server with PHP 5.4 or above with PDO extensions for MySQL, PostgreSQL or SQLite
* MySQL, MariaDB or PostgreSQL database server

### __License__

The project is distributed under [MIT LICENSE](https://opensource.org/licenses/MIT)

