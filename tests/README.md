### Unit tests

* Go to `[m2 root]/vendor/shopgate/cart-integration-magento2-base`
* `composer run tests:unit`

### Integration tests
* Create a new empty database for the integration tests. 
* Grant all privileges to the user that will be used for running the tests.

#### MySQL setup
```mysql
CREATE DATABASE magento2_tests;
CREATE USER 'magento2tests'@'localhost' IDENTIFIED BY 'magento2tests';
GRANT ALL PRIVILEGES ON magento2_tests.* TO 'magento2tests'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

* Copy the dev/tests/integration/etc/install-config-mysql.php.dist file to dev/tests/integration/etc/install-config-mysql.php.
* Update the database settings in install-config-mysql.php with the credentials of the user created in step 2.
* Adjust the PHPUnit configuration file, dev/tests/integration/phpunit.xml, according to your requirements.

### Postman

Set up the Shopgate configurations (can be done manually via Admin panel)

```shell
cd ../../..
bin/magento config:set shopgate_base/general/active 1;
bin/magento config:set shopgate_base/general/customer_number 123456;
bin/magento config:set shopgate_base/general/shop_number 12345;
bin/magento config:set shopgate_base/general/api_key 111111111111111111;
bin/magento config:set oauth/access_token_lifetime/admin ""
bin/magento config:set oauth/access_token_lifetime/customer ""
```

* One of the tests also requires the .sql file to be imported, 
you can do it like this (replace user/password/database with yours):
```shell
mysql -u magento2 -p magento2 < dev/modules/cart-integration-magento2-base/tests/Postman/addTestCartRule.sql
```

Create admin:
```shell
bin/magento admin:user:create --admin-user=admin --admin-password=magento123 --admin-email=test@example.com --admin-firstname=Admin --admin-lastname=Test
```

* Import collection, environment & global files into postman. 
* Configure the environment to match yours.
* Run collection

### Disabling Inventory modules

There are a few merchants out there who do not need to full extent of the `Magento_Inventory_*` abilities, thus a way 
to disable them, and test our code, is necessary. Until we have our tests automated, it's necessary to write the 
procedure down. It is as follows:

```shell
bin/magento module:disable $(php -f bin/magento module:status | grep "^Magento_Inventory")
bin/magento setup:upgrade
rm -rf var/di generated/*
bin/magento setup:di:compile
```
