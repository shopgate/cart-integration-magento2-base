# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
Reminder: `develop` currently contains changes to tests & CI - there won't be a release for this. Feel free to remove
this comment once there's production code changes that need to be documented.

## [2.9.33] - 2023-11-08
### Added
- support for enhanced gender options from Shopgate

### Changed
- uses Shopgate Cart Integration SDK 2.9.91

### Fixed
- error when browsing to the Shopgate configuration page and using german language
- issue with `set_settings` without a scope set
- issue with `get_settings` & country data not loading

## [2.9.32] - 2022-11-24
### Fixed
- character encoding issues in some cases when using PHP 8.1

### Changed
- uses Shopgate Cart Integration SDK 2.9.90

## [2.9.31] - 2022-05-18
### Added
- support for Magento 2.4.4
- support for PHP 8.1.x

### Changed
- uses Shopgate Cart Integration SDK v2.9.89

## [2.9.30] - 2022-01-05
### Changed
- fixed another incompatibility with Magento 2.4.3

## [2.9.29] - 2021-09-01
### Fixed
- restored compatibility with Magento 2.4.3 and lower

## [2.9.28] - 2021-09-01
### Fixed
- compatibility with Magento 2.4.3

## [2.9.27] - 2021-04-21
### Fixed
- avoid logging or displaying a PHP "undefined index" notice on payment mapping

## [2.9.26] - 2020-12-16
### Fixed
- an internal bug that would break the backend configuration page on some systems

## [2.9.25] - 2020-11-19
### Added
- quote to checkout session
- support for custom product prices during check_cart and add_order
- support for Magento 2.4

### Changed
- uses Shopgate Cart Integration SDK v2.9.79

## [2.9.24] - 2020-07-24
### Added
- mapping for name prefix
- quote to checkout session

### Fixed
- Order import for Estonia and Croatia when also states are used

## [2.9.23] - 2020-03-17
### Fixed
- Inactive child products are not exported anymore

## [2.9.22] - 2020-02-05
### Added
- support for regions based on text input fields

## [2.9.21] - 2019-11-26
### Added
- Security enhancements
- PluginInfo and ShopInfo to ping action

### Removed
- Support for PHP < 7.1
- Support for Magento < 2.2  

## [2.9.20] - 2019-11-01
### Added
- support for including customer data in cart validation by exposing the getCustomer method in checkCart
- export custom attributes as extra fields with customer and customer address data

## [2.9.19] - 2019-09-18
### Fixed
- Export of child products

## [2.9.18] - 2019-09-13
### Added
- filter for website specific items in product export

## [2.9.17] - 2019-08-22
### Added
- registered internalCartInfo cart helper method

## [2.9.16] - 2019-08-08
### Fixed
- app only coupons not working

## [2.9.15] - 2019-07-18
### Added
- Magento 2.3.2 CsrfValidation support which fixes the 302 errors
### Fixed
- Error "unknown shop number" when using Shopgate with different store views connected

## [2.9.14] - 2019-06-17
### Added
- Support for Export shopgate order collection calls
### Fixed
- Region/state mapping for get_customer calls, e.g. returns US-TX instead of TX

## [2.9.13] - 2019-06-05
### Fixed
- Cart validation for products with the same item id

## [2.9.12] - 2019-06-04
### Fixed
- Check_cart now returns the correct item_number
- Order import when ordering a product more than once with different custom option values

## [2.9.11] - 2019-02-13
### Added
- Support for Mage 2.3 controller CsrfValidation with backwards compatibility for older versions

## [2.9.10] - 2018-10-27
### Changed
- Uses Shopgate Cart Integration SDK 2.9.78

## [2.9.9] - 2018-08-01
### Added
- Empty implementation of the cron action
- Shopgate config variables to DI to support app:config:dump call
- App-only cart rules, not compatible with CustomerSegment
### Changed
- Uses Shopgate Cart Integration SDK 2.9.74
### Fixed
- Issue importing config.php when CMS Map config is empty
- Travis release zipping logic
- Missing IP address for guest orders
### Removed
- Import of prefixes in customer addresses

## [2.9.8] - 2018-04-19
### Fixed
- Incompatibility with Magento 2 SOAP API
- Option validation for child products
- Corrected order addresses to pass validation
- Saving of addresses in order import
- Issues with displaying configuration menu on Magento v2.2.0+
### Added
- Possibility to exclude specific items from the export

## [2.9.7]
### Fixed
- Addresses being saved more than once
- Added support when installing module for Magento 2.2.2
### Changed
- Uses Shopgate Cart Integration SDK 2.9.66
- Changed the GitHub composer naming so that it does not clash with Marketplace repo

## Version 2.9.6
- Uses Shopgate Cart Integration SDK 2.9.64
- Fixed issue with missing folders and Magento Cloud

## Version 2.9.5
- Fixed incorrect permission reference in acl.xml
- Fixed set_settings not saving properties that are not defined in the di.xml
- Fixed ping call returning supported_fields_check_cart as JSON instead of Array
- Added version upper bounds for composer module require declarations

## Version 2.9.4
### Added
- Review export via XML
- Cache invalidation on configuration save
### Updated
- Uses Shopgate Library 2.9.64
### Fixed
- Fix frontend store translations
- Fix when an item's internal_order_info is empty, e.g. SG coupons
- Fix for registered customer coupon not showing in order imports
- Fix for empty Shopgate configuration page in EE >= 2.1.0

## Version 2.9.3
### Added
- check_stock call
- Logic for shipping method export and import mapping
### Fixed
- Translation issues by replacing relative path for xsd files with magento style pathes
- Missing product in imported order, in case 2 different configurations of a product were bought

## Version 2.9.2
- Added new plugin configuration for exporting descriptions of child products
- Children of grouped products are now exported in the correct order
- Improved export of sale prices
- Added new plugin configuration for exporting invisible attributes
- Improved order import, order items now contain the original price
- Improved category mapping in item export, now taking care of anchor categories

## Version 2.9.1
### Added
- Config initialization
- get/set_settings calls
- check_cart call
- add_order call
- update_order call

## Version 2.9.0
- Created Initial Plugin

[Unreleased]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.33...HEAD
[2.9.33]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.32...2.9.33
[2.9.32]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.31...2.9.32
[2.9.31]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.30...2.9.31
[2.9.30]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.29...2.9.30
[2.9.29]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.28...2.9.29
[2.9.28]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.27...2.9.28
[2.9.27]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.26...2.9.27
[2.9.26]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.25...2.9.26
[2.9.25]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.24...2.9.25
[2.9.24]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.23...2.9.24
[2.9.23]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.22...2.9.23
[2.9.22]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.21...2.9.22
[2.9.21]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.20...2.9.21
[2.9.20]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.19...2.9.20
[2.9.19]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.18...2.9.19
[2.9.18]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.17...2.9.18
[2.9.17]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.16...2.9.17
[2.9.16]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.15...2.9.16
[2.9.15]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.14...2.9.15
[2.9.14]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.13...2.9.14
[2.9.13]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.12...2.9.13
[2.9.12]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.11...2.9.12
[2.9.11]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.10...2.9.11
[2.9.10]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.9...2.9.10
[2.9.9]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.8...2.9.9
[2.9.8]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.7...2.9.8
