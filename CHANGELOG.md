# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Fixed
 - Option validation for child products

## Version 2.9.7
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

[Unreleased]: https://github.com/shopgate/cart-integration-magento2-base/compare/2.9.7...HEAD
