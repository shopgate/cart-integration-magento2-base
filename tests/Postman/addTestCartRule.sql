INSERT INTO `salesrule` (`name`, `description`, `from_date`, `to_date`, `uses_per_customer`, `is_active`, `conditions_serialized`, `actions_serialized`, `stop_rules_processing`, `is_advanced`, `product_ids`, `sort_order`, `simple_action`, `discount_amount`, `discount_qty`, `discount_step`, `apply_to_shipping`, `times_used`, `is_rss`, `coupon_type`, `use_auto_generation`, `uses_per_coupon`, `simple_free_shipping`)
VALUES
	('App only discount','','2018-01-01','2030-01-01',0,1,'a:7:{s:4:\"type\";s:46:\"Magento\\SalesRule\\Model\\Rule\\Condition\\Combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:5:{s:4:\"type\";s:48:\"Shopgate\\Base\\Model\\Rule\\Condition\\ShopgateOrder\";s:9:\"attribute\";s:17:\"is_shopgate_order\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";b:0;}}}','a:6:{s:4:\"type\";s:54:\"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";}',0,1,NULL,0,'by_percent',10.0000,NULL,0,0,0,1,1,0,0,0);

INSERT INTO `salesrule_website` (`rule_id`, `website_id`)
VALUES
	(LAST_INSERT_ID(),1);

INSERT INTO `salesrule_customer_group` (`rule_id`, `customer_group_id`)
VALUES
	(LAST_INSERT_ID(),0),
	(LAST_INSERT_ID(),1),
	(LAST_INSERT_ID(),2),
	(LAST_INSERT_ID(),3);
