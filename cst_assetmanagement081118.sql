/*Table structure for table `acct_asset` */

DROP TABLE IF EXISTS `acct_asset`;

CREATE TABLE `acct_asset` (
  `asset_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `asset_type_id` int(10) DEFAULT '0',
  `location_id` int(10) DEFAULT '0',
  `asset_code` varchar(20) DEFAULT '',
  `asset_name` varchar(30) DEFAULT '',
  `asset_description` text,
  `asset_location_detail` text,
  `asset_quantity` decimal(10,0) DEFAULT '0',
  `asset_purchase_date` date DEFAULT NULL,
  `asset_purchase_value` decimal(20,2) DEFAULT '0.00',
  `asset_disposal_date` date DEFAULT NULL,
  `asset_disposal_value` decimal(20,2) DEFAULT '0.00',
  `asset_usage_date` date DEFAULT NULL,
  `asset_estimated_lifespan` decimal(10,2) DEFAULT '0.00',
  `asset_book_value` decimal(20,2) DEFAULT '0.00',
  `asset_depreciation_value` decimal(20,2) DEFAULT '0.00',
  `asset_salvage_value` decimal(20,2) DEFAULT '0.00',
  `voided` decimal(1,0) DEFAULT '0',
  `voided_id` int(10) DEFAULT '0',
  `voided_on` datetime DEFAULT NULL,
  `voided_remark` text,
  `data_state` decimal(1,0) DEFAULT '0',
  `created_id` int(10) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_id`),
  KEY `FK_acct_asset_location_id` (`location_id`),
  KEY `FK_acct_asset_asset_type_id` (`asset_type_id`),
  CONSTRAINT `FK_acct_asset_asset_type_id` FOREIGN KEY (`asset_type_id`) REFERENCES `acct_asset_type` (`asset_type_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_acct_asset_location_id` FOREIGN KEY (`location_id`) REFERENCES `core_location` (`location_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `acct_asset_depreciation` */

DROP TABLE IF EXISTS `acct_asset_depreciation`;

CREATE TABLE `acct_asset_depreciation` (
  `asset_depreciation_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `asset_id` bigint(22) DEFAULT '0',
  `asset_depreciation_no` varchar(20) DEFAULT '',
  `asset_depreciation_date` date DEFAULT NULL,
  `asset_depreciation_duration` decimal(10,0) DEFAULT '0',
  `asset_depreciation_start_month` decimal(10,0) DEFAULT '0',
  `asset_depreciation_start_year` decimal(10,0) DEFAULT '0',
  `asset_depreciation_end_month` decimal(10,0) DEFAULT '0',
  `asset_depreciation_end_year` decimal(10,0) DEFAULT '0',
  `asset_depreciation_book_value` decimal(20,2) DEFAULT '0.00',
  `asset_depreciation_beginning_book_value` decimal(20,2) DEFAULT '0.00',
  `asset_depreciation_ending_book_value` decimal(20,2) DEFAULT '0.00',
  `asset_depreciation_status` decimal(1,0) DEFAULT '0',
  `asset_depreciation_type` decimal(1,0) DEFAULT '0',
  `asset_depreciation_remark` text,
  `data_state` decimal(1,0) DEFAULT '0',
  `created_id` int(10) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_depreciation_id`),
  KEY `FK_acct_asset_depreciation_asset_id` (`asset_id`),
  CONSTRAINT `FK_acct_asset_depreciation_asset_id` FOREIGN KEY (`asset_id`) REFERENCES `acct_asset` (`asset_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `acct_asset_depreciation_item` */

DROP TABLE IF EXISTS `acct_asset_depreciation_item`;

CREATE TABLE `acct_asset_depreciation_item` (
  `asset_depreciation_item_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `asset_depreciation_id` bigint(22) DEFAULT '0',
  `asset_depreciation_item_month` decimal(10,0) DEFAULT '0',
  `asset_depreciation_item_year` decimal(10,0) DEFAULT '0',
  `asset_depreciation_item_amount` decimal(20,2) DEFAULT '0.00',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_depreciation_item_id`),
  KEY `FK_acct_asset_depreciation_item_asset_depreciation_id` (`asset_depreciation_id`),
  CONSTRAINT `FK_acct_asset_depreciation_item_asset_depreciation_id` FOREIGN KEY (`asset_depreciation_id`) REFERENCES `acct_asset_depreciation` (`asset_depreciation_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8;

/*Table structure for table `acct_asset_maintenance` */

DROP TABLE IF EXISTS `acct_asset_maintenance`;

CREATE TABLE `acct_asset_maintenance` (
  `asset_maintenance_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `asset_type_id` int(10) DEFAULT '0',
  `maintenance_id` int(18) DEFAULT '0',
  `data_state` decimal(1,0) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_maintenance_id`),
  KEY `FK_acct_asset_maintenance_asset_type_id` (`asset_type_id`),
  KEY `FK_acct_asset_maintenance_maintenance_id` (`maintenance_id`),
  CONSTRAINT `FK_acct_asset_maintenance_asset_type_id` FOREIGN KEY (`asset_type_id`) REFERENCES `acct_asset_type` (`asset_type_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_acct_asset_maintenance_maintenance_id` FOREIGN KEY (`maintenance_id`) REFERENCES `core_maintenance` (`maintenance_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `acct_asset_maintenance_card` */

DROP TABLE IF EXISTS `acct_asset_maintenance_card`;

CREATE TABLE `acct_asset_maintenance_card` (
  `asset_maintenance_card_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `asset_maintenance_card_date` date DEFAULT NULL,
  `asset_id` bigint(22) DEFAULT '0',
  `asset_maintenance_id` bigint(22) DEFAULT '0',
  `vendor_id` int(18) DEFAULT '0',
  `asset_maintenance_card_total_cost_amount` decimal(20,2) DEFAULT '0.00',
  `asset_maintenance_description_of_demage` text,
  `asset_maintenance_description_of_improvement` text,
  `asset_maintenance_card_remark` text,
  `data_state` decimal(1,0) DEFAULT '0',
  `created_id` int(10) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_maintenance_card_id`),
  KEY `FK_acct_asset_maintenance_card_asset_id` (`asset_id`),
  KEY `FK_acct_asset_maintenance_card_asset_maintenance_id` (`asset_maintenance_id`),
  CONSTRAINT `FK_acct_asset_maintenance_card_asset_id` FOREIGN KEY (`asset_id`) REFERENCES `acct_asset` (`asset_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_acct_asset_maintenance_card_asset_maintenance_id` FOREIGN KEY (`asset_maintenance_id`) REFERENCES `acct_asset_maintenance` (`asset_maintenance_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

/*Table structure for table `acct_asset_maintenance_part` */

DROP TABLE IF EXISTS `acct_asset_maintenance_part`;

CREATE TABLE `acct_asset_maintenance_part` (
  `asset_maintenance_part_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `asset_maintenance_card_id` bigint(22) DEFAULT '0',
  `item_id` int(18) DEFAULT '0',
  `asset_maintenance_quantity` decimal(10,2) DEFAULT '0.00',
  `asset_maintenance_cost_amount` decimal(20,2) DEFAULT '0.00',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_maintenance_part_id`),
  KEY `FK_acct_asset_maintenance_part_asset_maintenance_card_id` (`asset_maintenance_card_id`),
  KEY `FK_aact_asset_maintenance_part_item_id` (`item_id`),
  CONSTRAINT `FK_aact_asset_maintenance_part_item_id` FOREIGN KEY (`item_id`) REFERENCES `invt_item` (`item_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_acct_asset_maintenance_part_asset_maintenance_card_id` FOREIGN KEY (`asset_maintenance_card_id`) REFERENCES `acct_asset_maintenance_card` (`asset_maintenance_card_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Table structure for table `acct_asset_maintenance_schedule` */

DROP TABLE IF EXISTS `acct_asset_maintenance_schedule`;

CREATE TABLE `acct_asset_maintenance_schedule` (
  `asset_maintenance_schedule_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `asset_maintenance_card_id` bigint(22) DEFAULT '0',
  `asset_id` bigint(22) DEFAULT '0',
  `maintenance_id` int(18) DEFAULT '0',
  `asset_maintenance_schedule_date` date DEFAULT NULL,
  `asset_maintenance_schedule_next_date` date DEFAULT NULL,
  `asset_maintenance_schedule_difference` decimal(10,0) DEFAULT '0',
  `asset_maintenance_schedule_status` decimal(1,0) DEFAULT '0',
  `asset_maintenance_schedule_remark` text,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_maintenance_schedule_id`),
  KEY `FK_acct_asset_maintenance_schedule_asset_id` (`asset_id`),
  KEY `FK_acct_asset_maintenance_schedule_maintenance_id` (`maintenance_id`),
  KEY `FK_acct_asset_maintenance_schedule_asset_maintenance_card_id` (`asset_maintenance_card_id`),
  CONSTRAINT `FK_acct_asset_maintenance_schedule_asset_id` FOREIGN KEY (`asset_id`) REFERENCES `acct_asset` (`asset_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_acct_asset_maintenance_schedule_asset_maintenance_card_id` FOREIGN KEY (`asset_maintenance_card_id`) REFERENCES `acct_asset_maintenance_card` (`asset_maintenance_card_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_acct_asset_maintenance_schedule_maintenance_id` FOREIGN KEY (`maintenance_id`) REFERENCES `core_maintenance` (`maintenance_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Table structure for table `acct_asset_transfer` */

DROP TABLE IF EXISTS `acct_asset_transfer`;

CREATE TABLE `acct_asset_transfer` (
  `asset_transfer_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `asset_id` bigint(22) DEFAULT '0',
  `location_from_id` int(10) DEFAULT '0',
  `location_to_id` int(10) DEFAULT '0',
  `asset_received_by_name` varchar(50) DEFAULT NULL,
  `asset_transfer_date` date DEFAULT NULL,
  `asset_transfer_remark` text,
  `voided` decimal(1,0) DEFAULT '0',
  `voided_id` int(10) DEFAULT '0',
  `voided_on` datetime DEFAULT NULL,
  `voided_remark` text,
  `data_state` decimal(1,0) DEFAULT '0',
  `created_id` int(10) DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_transfer_id`),
  KEY `FK_acct_asset_transfer_asset_id` (`asset_id`),
  CONSTRAINT `FK_acct_asset_transfer_asset_id` FOREIGN KEY (`asset_id`) REFERENCES `acct_asset` (`asset_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `acct_asset_type` */

DROP TABLE IF EXISTS `acct_asset_type`;

CREATE TABLE `acct_asset_type` (
  `asset_type_id` int(10) NOT NULL AUTO_INCREMENT,
  `asset_type_code` varchar(20) DEFAULT '',
  `asset_type_name` varchar(50) DEFAULT '',
  `asset_type_description` text,
  `asset_type_parent` int(10) DEFAULT '0',
  `asset_type_parent_status` decimal(1,0) DEFAULT '0',
  `data_state` decimal(1,0) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `core_location` */

DROP TABLE IF EXISTS `core_location`;

CREATE TABLE `core_location` (
  `location_id` int(10) NOT NULL DEFAULT '0',
  `location_code` varchar(20) DEFAULT '',
  `location_name` varchar(50) DEFAULT '',
  `location_address` text,
  `location_contact_person` varchar(20) DEFAULT '',
  `location_home_phone` varchar(30) DEFAULT '',
  `location_mobile_phone` varchar(30) DEFAULT '',
  `data_state` decimal(1,0) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `core_maintenance` */

DROP TABLE IF EXISTS `core_maintenance`;

CREATE TABLE `core_maintenance` (
  `maintenance_id` int(18) NOT NULL AUTO_INCREMENT,
  `maintenance_code` varchar(20) DEFAULT '',
  `maintenance_name` varchar(50) DEFAULT '',
  `maintenance_period` decimal(10,0) DEFAULT '0',
  `maintenance_time` decimal(1,0) DEFAULT '0',
  `data_state` decimal(1,0) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`maintenance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `core_vendor` */

DROP TABLE IF EXISTS `core_vendor`;

CREATE TABLE `core_vendor` (
  `vendor_id` int(18) NOT NULL AUTO_INCREMENT,
  `vendor_name` varchar(100) DEFAULT '',
  `vendor_address` text,
  `vendor_contact_person` varchar(50) DEFAULT '',
  `vendor_phone` varchar(50) DEFAULT '',
  `data_state` decimal(1,0) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vendor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `invt_item` */

DROP TABLE IF EXISTS `invt_item`;

CREATE TABLE `invt_item` (
  `item_id` int(18) NOT NULL AUTO_INCREMENT,
  `item_code` varchar(20) DEFAULT '',
  `item_name` varchar(50) DEFAULT '',
  `data_state` decimal(1,0) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Table structure for table `preference_company` */

DROP TABLE IF EXISTS `preference_company`;

CREATE TABLE `preference_company` (
  `company_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(50) DEFAULT '',
  `company_address` text,
  `company_home_phone1` varchar(30) DEFAULT '',
  `company_home_phone2` varchar(30) DEFAULT '',
  `company_bank_name_ppn1` varchar(100) DEFAULT NULL,
  `company_bank_name_ppn2` varchar(100) DEFAULT NULL,
  `company_bank_name_nonppn1` varchar(100) NOT NULL,
  `company_bank_name_nonppn2` varchar(100) NOT NULL,
  `company_bank_account_name_ppn` varchar(50) DEFAULT NULL,
  `company_bank_account_name_nonppn` varchar(50) DEFAULT NULL,
  `company_bank_account_no_ppn1` varchar(50) DEFAULT NULL,
  `company_bank_account_no_ppn2` varchar(50) DEFAULT NULL,
  `company_bank_account_no_nonppn1` varchar(50) NOT NULL,
  `company_bank_account_no_nonppn2` varchar(50) NOT NULL,
  `company_fax_number` varchar(30) DEFAULT '',
  `company_tax_number` varchar(30) DEFAULT '',
  `company_tax_date` date DEFAULT NULL,
  `company_logo` varchar(200) DEFAULT '',
  `company_current_period` decimal(10,0) DEFAULT '0' COMMENT '1 : Januari, 2 : Februari, 3 : Maret, 4 : April, 5 : Mei, 6 : Juni, 7 : Juli, 8 : Agustus, 9 : September, 10 : Oktober, 11 : November, 12 : Desember',
  `biaya_selisih_kurs` int(10) NOT NULL DEFAULT '0',
  `pendapatan_selisih_kurs` int(10) NOT NULL DEFAULT '0',
  `shortover_plus` int(10) NOT NULL DEFAULT '0',
  `shortover_minus` int(10) NOT NULL DEFAULT '0',
  `company_fiscal_year` decimal(10,0) DEFAULT '0',
  `company_last_period` decimal(10,0) DEFAULT '0' COMMENT '1 : Januari, 2 : Februari, 3 : Maret, 4 : April, 5 : Mei, 6 : Juni, 7 : Juli, 8 : Agustus, 9 : September, 10 : Oktober, 11 : November, 12 : Desember',
  `company_using_period` decimal(10,0) DEFAULT '0',
  `work_days` int(10) NOT NULL DEFAULT '0',
  `work_shift` int(10) NOT NULL DEFAULT '0',
  `expired_days_notification` int(10) NOT NULL DEFAULT '0' COMMENT 'days',
  `minimal_stock_warning_percentage` int(10) NOT NULL DEFAULT '0' COMMENT 'percentage',
  `minimal_stock_warning_type` enum('0','1') NOT NULL COMMENT '0 : Per Item, 1 Per Warehouse',
  `refreshrate` int(10) NOT NULL DEFAULT '60',
  `default_password` varchar(35) CHARACTER SET latin1 DEFAULT NULL,
  `default_setting_stock` enum('0','1') DEFAULT '0' COMMENT '0=''allow minus'', 1=''not allow minus''',
  `ppn` decimal(10,2) DEFAULT '0.00',
  `tax_percentage` decimal(10,2) NOT NULL,
  `sales_collection_cash` int(10) NOT NULL,
  `purchase_payment_cash` int(10) NOT NULL DEFAULT '0',
  `stockist_application_relative_path` varchar(200) DEFAULT '',
  `finished_goods_category_id` int(10) DEFAULT '0',
  `supplier_id_lem` int(10) DEFAULT '0',
  `supplier_id_pls` int(10) DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `preference_reference` */

DROP TABLE IF EXISTS `preference_reference`;

CREATE TABLE `preference_reference` (
  `reference_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(10) DEFAULT '0',
  `transaction_module_id` int(10) DEFAULT '0',
  `reference_format_type` enum('0','1') DEFAULT '0' COMMENT '0 : Master, 1 : Transaction',
  `front_code` varchar(10) DEFAULT '',
  `conjunction_one` varchar(10) DEFAULT '',
  `middle_one` varchar(10) DEFAULT '',
  `conjunction_two` varchar(10) DEFAULT '',
  `middle_two` varchar(10) DEFAULT '',
  `conjunction_three` varchar(10) DEFAULT '',
  `month_active` int(10) NOT NULL DEFAULT '0',
  `last_number` int(50) DEFAULT '0',
  `reference_format` varchar(50) DEFAULT '',
  `reference_counter` decimal(5,0) DEFAULT '0',
  `reference_last_digit` decimal(5,0) DEFAULT '0',
  `reference_last_digit_use` decimal(5,0) NOT NULL DEFAULT '0',
  `created_by` varchar(20) DEFAULT '',
  `created_on` datetime DEFAULT NULL,
  `data_state` enum('0','1','2','3') DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reference_id`),
  KEY `FK_preference_reference_warehouse_id` (`warehouse_id`),
  KEY `FK_preference_reference_transaction_module_id` (`transaction_module_id`),
  CONSTRAINT `FK_preference_reference_transaction_module_id` FOREIGN KEY (`transaction_module_id`) REFERENCES `preference_transaction_module` (`transaction_module_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

/*Table structure for table `system_change_log` */

DROP TABLE IF EXISTS `system_change_log`;

CREATE TABLE `system_change_log` (
  `change_log_id` int(11) NOT NULL DEFAULT '0',
  `user_log_id` int(11) DEFAULT NULL,
  `kode` varchar(15) DEFAULT NULL,
  `old_data` mediumtext,
  `new_data` mediumtext,
  `log_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`change_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `system_log_user` */

DROP TABLE IF EXISTS `system_log_user`;

CREATE TABLE `system_log_user` (
  `user_log_id` bigint(20) NOT NULL,
  `user_id` int(10) DEFAULT '0',
  `username` varchar(50) DEFAULT '',
  `id_previllage` int(4) DEFAULT '0',
  `log_stat` enum('0','1') DEFAULT NULL,
  `class_name` varchar(250) DEFAULT '',
  `pk` varchar(20) DEFAULT '',
  `remark` varchar(50) DEFAULT '',
  `log_time` datetime DEFAULT NULL,
  PRIMARY KEY (`user_log_id`),
  KEY `FK_system_log_user` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `system_menu` */

DROP TABLE IF EXISTS `system_menu`;

CREATE TABLE `system_menu` (
  `id_menu` varchar(10) NOT NULL,
  `id` varchar(100) DEFAULT NULL,
  `type` enum('folder','file','function') DEFAULT NULL,
  `text` varchar(50) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_menu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `system_menu_mapping` */

DROP TABLE IF EXISTS `system_menu_mapping`;

CREATE TABLE `system_menu_mapping` (
  `user_group_level` int(3) NOT NULL,
  `id_menu` varchar(10) NOT NULL,
  PRIMARY KEY (`user_group_level`,`id_menu`),
  KEY `FK_system_menu_mapping` (`id_menu`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `system_user` */

DROP TABLE IF EXISTS `system_user`;

CREATE TABLE `system_user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(35) DEFAULT NULL,
  `database` varchar(100) DEFAULT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  `location_id` int(10) DEFAULT '0',
  `branch_id` int(6) DEFAULT NULL,
  `division_id` int(10) DEFAULT '0',
  `section_id` int(10) DEFAULT '0',
  `user_level` decimal(1,0) DEFAULT '0',
  `approve` decimal(1,0) DEFAULT '0',
  `log_stat` enum('on','off') DEFAULT NULL,
  `avatar` text,
  `data_state` enum('0','1') DEFAULT '0',
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`username`),
  KEY `FK_system_user` (`user_group_id`),
  CONSTRAINT `FK_system_user_user_group_id` FOREIGN KEY (`user_group_id`) REFERENCES `system_user_group` (`user_group_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

/*Table structure for table `system_user_group` */

DROP TABLE IF EXISTS `system_user_group`;

CREATE TABLE `system_user_group` (
  `user_group_id` int(3) NOT NULL AUTO_INCREMENT,
  `user_group_level` int(11) DEFAULT NULL,
  `user_group_name` varchar(50) DEFAULT NULL,
  `data_state` enum('0','1') DEFAULT '0',
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/* Trigger structure for table `core_location` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `insert_core_location` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `insert_core_location` AFTER INSERT ON `core_location` FOR EACH ROW BEGIN
	DECLARE LAST INT;
	
	SELECT reference_counter INTO LAST 
		FROM preference_reference 
		WHERE reference_id = '3';
		
	UPDATE preference_reference SET reference_counter = LAST + 1 
		WHERE reference_id = '3';
		
	UPDATE preference_reference SET reference_last_digit_use = LAST + 1 
		WHERE reference_id = '3';
    END */$$


DELIMITER ;

/* Function  structure for function  `getNewChangeLogID` */

/*!50003 DROP FUNCTION IF EXISTS `getNewChangeLogID` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `getNewChangeLogID`() RETURNS int(11)
BEGIN
	DECLARE prev_id INT;
	DECLARE next_id INT;
	SELECT change_log_id INTO prev_id FROM system_change_log ORDER BY change_log_id DESC LIMIT 0,1;
	IF prev_id IS NULL THEN
		SET prev_id = 0;
	END IF;
	SET next_id = prev_id + 1;
	RETURN next_id;
END */$$
DELIMITER ;

/* Function  structure for function  `getNewCodeAsset` */

/*!50003 DROP FUNCTION IF EXISTS `getNewCodeAsset` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `getNewCodeAsset`(whr INT(10), nosave varchar(20)) RETURNS varchar(20) CHARSET latin1
BEGIN
	DECLARE warehouse INT;
	DECLARE nomorada VARCHAR(20);
	DECLARE front VARCHAR(5);
	DECLARE cjt_one VARCHAR(5);
	DECLARE cjt_two VARCHAR(5);
	DECLARE cjt_three VARCHAR(5);
	DECLARE mdl_one VARCHAR(10);
	DECLARE mdl_two VARCHAR(10);
	DECLARE angkadepan VARCHAR(10);
	DECLARE p1 VARCHAR(10);
	DECLARE p2 VARCHAR(10);
	DECLARE new_id VARCHAR(20);
	DECLARE digit INT;
	DECLARE last INT;
	DECLARE mountuse INT;
	DECLARE panjanglast INT;
	DECLARE i int;
	SET warehouse = whr;
	SET nomorada = nosave;
	SELECT month_active INTO mountuse from preference_reference WHERE reference_id='20';
	IF(mountuse='')THEN 
	UPDATE preference_reference SET month_active=DATE_FORMAT(NOW(),'%m') WHERE reference_id='20'; 
	ELSEIF (mountuse<>DATE_FORMAT(NOW(),'%m'))THEN
	UPDATE preference_reference SET month_active=DATE_FORMAT(NOW(),'%m'), reference_last_digit='0', reference_last_digit_use='0' WHERE reference_id='20';
	END IF;
	SELECT front_code INTO front from preference_reference WHERE reference_id='20';
	SELECT conjunction_one INTO cjt_one from preference_reference WHERE reference_id='20';
	SELECT conjunction_two INTO cjt_two from preference_reference WHERE reference_id='20';
	SELECT conjunction_three INTO cjt_three from preference_reference WHERE reference_id='20';
	SELECT middle_one INTO mdl_one from preference_reference WHERE reference_id='20';
	SELECT middle_two INTO mdl_two from preference_reference WHERE reference_id='20';
	SELECT last_number INTO digit from preference_reference WHERE reference_id='20';
	SELECT reference_last_digit_use INTO last from preference_reference WHERE reference_id='20';
	
	IF(mdl_one='[BLN]')THEN
		SET mdl_one=DATE_FORMAT(NOW(),'%m');
	ELSEIF(mdl_one='[THN]')THEN
		SET mdl_one=DATE_FORMAT(NOW(),'%y');
	ELSEIF(mdl_one='[BLNTHN]')THEN
		SET mdl_one=DATE_FORMAT(NOW(),'%m%y');
	ELSEIF(mdl_one='[GD]')THEN
		SET mdl_one=warehouse;
	END IF;
	IF(mdl_two='[BLN]')THEN
		SET mdl_two=DATE_FORMAT(NOW(),'%m');
	ELSEIF(mdl_two='[THN]')THEN
		SET mdl_two=DATE_FORMAT(NOW(),'%y');
	ELSEIF(mdl_two='[BLNTHN]')THEN
		SET mdl_two=DATE_FORMAT(NOW(),'%m%y');
	ELSEIF(mdl_two='[GD]')THEN
		SET mdl_two=warehouse;
	END IF;
	SET p1='';
	SET i=1;
	ulang: WHILE i<=digit DO
		SET p1=CONCAT(p1, '0');
		SET i=i+1;
	END WHILE ulang;
	IF(nomorada<>'0')THEN
		SET last=SUBSTR(nomorada,LENGTH(nomorada),LENGTH(last));
		SET panjanglast=LENGTH(last);
		SET angkadepan=SUBSTR(p1,1,LENGTH(p1)-panjanglast);
		SET p2=CONCAT(angkadepan,last);
	ELSE
		IF(LENGTH(p1)<LENGTH(last)) THEN
			SET p2=last+1;
		ELSE
			SET panjanglast=LENGTH(last);
			SET angkadepan=SUBSTR(p1,1,LENGTH(p1)-panjanglast);
			SET p2=CONCAT(angkadepan,last+1);
		END IF;
	END IF;
	IF(nomorada='0')THEN
		UPDATE preference_reference SET reference_last_digit_use=last+1 WHERE reference_id='20'; 
	END IF;
	SET new_id=CONCAT(front,cjt_one,mdl_one,cjt_two,mdl_two,cjt_three,p2);
	
	RETURN new_id;
END */$$
DELIMITER ;

/* Function  structure for function  `getNewCodeAssetType` */

/*!50003 DROP FUNCTION IF EXISTS `getNewCodeAssetType` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `getNewCodeAssetType`() RETURNS varchar(20) CHARSET latin1
BEGIN
	DECLARE digit INT;
	DECLARE last INT;
	DECLARE panjanglast INT;
	DECLARE angkadepan VARCHAR(10);
	DECLARE p1 VARCHAR(10);
	DECLARE p2 VARCHAR(10);
	DECLARE i int;
	DECLARE front VARCHAR(5);
	DECLARE conjunction VARCHAR(5);
	DECLARE new_id VARCHAR(14);
	SELECT front_code INTO front from preference_reference WHERE reference_id='19';
	SELECT conjunction_one INTO conjunction from preference_reference WHERE reference_id='19';
	SELECT last_number INTO digit from preference_reference WHERE reference_id='19';
	SELECT reference_last_digit_use INTO last from preference_reference WHERE reference_id='19';
	SET p1='';
	SET i=1;
	ulang: WHILE i<=digit DO
	SET p1=CONCAT(p1, '0');
	SET i=i+1;
	END WHILE ulang;
	IF(LENGTH(p1)<LENGTH(last)) THEN
	SET p2=last+1;
	ELSE
	SET panjanglast=LENGTH(last);
	SET angkadepan=SUBSTR(p1,1,LENGTH(p1)-panjanglast);
	SET p2=CONCAT(angkadepan,last+1);
	END IF;
	UPDATE preference_reference SET reference_last_digit_use=last+1 WHERE reference_id='19';
	SET new_id=CONCAT(front,conjunction,p2);
	RETURN new_id;
END */$$
DELIMITER ;

/* Function  structure for function  `getNewUserGroupID` */

/*!50003 DROP FUNCTION IF EXISTS `getNewUserGroupID` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `getNewUserGroupID`() RETURNS int(11)
BEGIN
	DECLARE prev_id INT;
	DECLARE next_id INT;
	SELECT user_group_id INTO prev_id FROM system_user_group ORDER BY user_group_id DESC LIMIT 0,1;
	IF prev_id IS NULL THEN
		SET prev_id = 0;
	END IF;
	SET next_id = prev_id + 1;
	RETURN next_id;
END */$$
DELIMITER ;

/* Function  structure for function  `getNewUserLogId` */

/*!50003 DROP FUNCTION IF EXISTS `getNewUserLogId` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `getNewUserLogId`() RETURNS int(11)
BEGIN
	DECLARE prev_id INT;
	DECLARE next_id INT;
	SELECT user_log_id INTO prev_id FROM system_log_user ORDER BY user_log_id DESC LIMIT 0,1;
	IF prev_id IS NULL THEN
		SET prev_id = 0;
	END IF;
	SET next_id = prev_id + 1;
	RETURN next_id;
END */$$
DELIMITER ;

/* Function  structure for function  `getNewCodeLocation` */

/*!50003 DROP FUNCTION IF EXISTS `getNewCodeLocation` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `getNewCodeLocation`() RETURNS varchar(20) CHARSET latin1
BEGIN
	DECLARE digit INT;
	DECLARE last INT;
	DECLARE panjanglast INT;
	DECLARE angkadepan VARCHAR(10);
	DECLARE p1 VARCHAR(10);
	DECLARE p2 VARCHAR(10);
	DECLARE i int;
	DECLARE front VARCHAR(5);
	DECLARE conjunction VARCHAR(5);
	DECLARE new_id VARCHAR(14);
	
	SELECT front_code INTO front 
		from preference_reference 
		WHERE reference_id = '3';
		
	SELECT conjunction_one INTO conjunction 
		from preference_reference 
		WHERE reference_id = '3';
		
	SELECT last_number INTO digit 
		from preference_reference 
		WHERE reference_id = '3';
		
	SELECT reference_last_digit_use INTO last 
		from preference_reference 
		WHERE reference_id = '3';
		
	SET p1 = '';
	SET i = 1;
	ulang: WHILE i<=digit DO
		SET p1 = CONCAT(p1, '0');
		SET i = i + 1;
	END WHILE ulang;
	
	IF(LENGTH(p1)<LENGTH(last)) THEN
		SET p2 = last + 1;
	ELSE
		SET panjanglast = LENGTH(last);
		SET angkadepan = SUBSTR(p1, 1, LENGTH(p1) - panjanglast);
		SET p2 = CONCAT(angkadepan, last + 1);
	END IF;
	
	SET new_id = CONCAT(front, conjunction, p2);
	RETURN new_id;
END */$$
DELIMITER ;

/* Function  structure for function  `getNewCodeMaintenance` */

/*!50003 DROP FUNCTION IF EXISTS `getNewCodeMaintenance` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `getNewCodeMaintenance`() RETURNS varchar(20) CHARSET latin1
BEGIN
	DECLARE digit INT;
	DECLARE last INT;
	DECLARE panjanglast INT;
	DECLARE angkadepan VARCHAR(10);
	DECLARE p1 VARCHAR(10);
	DECLARE p2 VARCHAR(10);
	DECLARE i int;
	DECLARE front VARCHAR(5);
	DECLARE conjunction VARCHAR(5);
	DECLARE new_id VARCHAR(14);
	
	SELECT front_code INTO front 
		from preference_reference 
		WHERE reference_id = '3';
		
	SELECT conjunction_one INTO conjunction 
		from preference_reference 
		WHERE reference_id = '3';
		
	SELECT last_number INTO digit 
		from preference_reference 
		WHERE reference_id = '3';
		
	SELECT reference_last_digit_use INTO last 
		from preference_reference 
		WHERE reference_id = '3';
		
	SET p1 = '';
	SET i = 1;
	ulang: WHILE i<=digit DO
		SET p1 = CONCAT(p1, '0');
		SET i = i + 1;
	END WHILE ulang;
	
	IF(LENGTH(p1)<LENGTH(last)) THEN
		SET p2 = last + 1;
	ELSE
		SET panjanglast = LENGTH(last);
		SET angkadepan = SUBSTR(p1, 1, LENGTH(p1) - panjanglast);
		SET p2 = CONCAT(angkadepan, last + 1);
	END IF;
	
	SET new_id = CONCAT(front, conjunction, p2);
	RETURN new_id;
END */$$
DELIMITER ;

