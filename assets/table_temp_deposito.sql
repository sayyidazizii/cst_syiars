
/*Table structure for table `acct_deposito_profit_sharing_temp` */

DROP TABLE IF EXISTS `acct_deposito_profit_sharing_temp`;


CREATE TABLE `acct_deposito_profit_sharing_temp` (
  `deposito_profit_sharing_temp_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `branch_id` int(10) DEFAULT '0',
  `deposito_id` int(10) DEFAULT '0',
  `member_id` bigint(22) DEFAULT '0',
  `member_name` varchar(100) DEFAULT '',
  `deposito_account_id` bigint(22) DEFAULT '0',
  `deposito_profit_sharing_date` date DEFAULT NULL,
  `deposito_index_amount` decimal(10,5) DEFAULT '0.00000',
  `deposito_daily_average_balance` decimal(20,2) DEFAULT '0.00',
  `deposito_profit_sharing_amount` decimal(20,2) DEFAULT '0.00',
  `deposito_profit_sharing_period` varchar(10) DEFAULT '',
  `deposito_profit_sharing_token` varchar(100) DEFAULT NULL,
  `savings_account_id` bigint(22) DEFAULT '0',
  `savings_id` int(5) DEFAULT '0',
  `savings_account_opening_balance` decimal(20,2) DEFAULT '0.00',
  `savings_transfer_mutation_amount` decimal(20,2) DEFAULT '0.00',
  `savings_account_last_balance` decimal(20,2) DEFAULT '0.00',
  `operated_name` varchar(10) DEFAULT '',
  `created_id` int(10) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`deposito_profit_sharing_temp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8