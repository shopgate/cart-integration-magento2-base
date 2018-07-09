INSERT INTO `salesrule` (`name`, `description`, `from_date`, `to_date`, `uses_per_customer`, `is_active`, `conditions_serialized`, `actions_serialized`, `stop_rules_processing`, `is_advanced`, `product_ids`, `sort_order`, `simple_action`, `discount_amount`, `discount_qty`, `discount_step`, `apply_to_shipping`, `times_used`, `is_rss`, `coupon_type`, `use_auto_generation`, `uses_per_coupon`, `simple_free_shipping`)
  VALUES ('Shopgate App Discount', '', '2018-07-09', null, 0, 1, '{"type":"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Shopgate\\\\Base\\\\Model\\\\Rule\\\\Condition\\\\ShopgateOrder","attribute":"is_shopgate_order","operator":"==","value":"1","is_value_processed":false}]}', '{"type":"Magento\\\\SalesRule\\\\Model\\\\Rule\\\\Condition\\\\Product\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all"}', 0, 1, null, 0, 'by_percent', 10.0000, null, 0, 0, 0, 1, 2, 0, 0, 0);

INSERT INTO `salesrule_website` (`rule_id`, `website_id`)
  VALUES (LAST_INSERT_ID(),1);

INSERT INTO `salesrule_customer_group` (`rule_id`, `customer_group_id`)
  VALUES
  (LAST_INSERT_ID(),0),
  (LAST_INSERT_ID(),1),
  (LAST_INSERT_ID(),2),
  (LAST_INSERT_ID(),3);

INSERT INTO `salesrule_coupon` (`rule_id`, `code`, `usage_limit`, `usage_per_customer`, `times_used`, `expiration_date`, `is_primary`, `created_at`, `type`)
  VALUES (LAST_INSERT_ID(), 'APP10', null, null, 0, null, 1, null, 0);
