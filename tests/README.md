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

To set up the environment before running the postman tests you will need to configure the Base extension.
This can be done via [n98](https://github.com/netz98/n98-magerun2) tool like so:

```shell
bin/n98-magerun2 config:set shopgate_base/general/active 1;
bin/n98-magerun2 config:set shopgate_base/general/customer_number 123456;
bin/n98-magerun2 config:set shopgate_base/general/shop_number 12345;
bin/n98-magerun2 config:set shopgate_base/general/api_key 111111111111111111;
bin/n98-magerun2 config:set oauth/access_token_lifetime/admin ""
bin/n98-magerun2 config:set oauth/access_token_lifetime/customer ""
bin/n98-magerun2 config:set shopgate_advanced/staging/server "custom"
bin/n98-magerun2 config:set shopgate_advanced/staging/api_url "https://eoscstmcvyyyg6w.m.pipedream.net"
```
Note that you can get the `mock_url` from the environment file of the Postman test suite.

* One of the tests also requires the .sql file to be imported, you can do it like this:
```shell
bin/n98-magerun2 db:import dev/modules/cart-integration-magento2-base/tests/Postman/addTestCartRule.sql
```

Create admin:
```shell
bin/n98-magerun2 admin:user:create --admin-user=admin --admin-password=magento123 --admin-email=test@example.com --admin-firstname=Admin --admin-lastname=Test
```

* Import collection, environment & global files into postman. 
* Configure the environment to match yours.
* Run collection


