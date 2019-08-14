<?php

$installer = $this;

$installer->startSetup();

$installer->run("

#DROP TABLE IF EXISTS {$this->getTable('livrarionline')};
CREATE TABLE IF NOT EXISTS {$this->getTable('livrarionline')} (
  `livrarionline_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`livrarionline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#DROP TABLE IF EXISTS {$this->getTable('livrarionline_carriers')};
CREATE TABLE  IF NOT EXISTS {$this->getTable('livrarionline_carriers')} (
  `carrier_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `service_id` int(11) unsigned NOT NULL,
  `shipping_company_id` int(11) unsigned NOT NULL,
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `updated_time` datetime NULL,
  PRIMARY KEY (`carrier_id`),
  KEY `created_time` (`created_time`),
  KEY `updated_time` (`updated_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#DROP TABLE IF EXISTS {$this->getTable('livrarionline_stores')};
CREATE TABLE  IF NOT EXISTS {$this->getTable('livrarionline_stores')} (
  `store_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `address1` varchar(255) NOT NULL default '',
  `address2` varchar(255) NULL,
  `zipcode` varchar(20) NULL,
  `city` varchar(255) NULL,
  `country` varchar(2) NOT NULL,
  `state` varchar(2) NOT NULL,
  `latitude` decimal(10,6) unsigned NULL,
  `longtitude` decimal(10,6) unsigned NULL,
  `phone` varchar(50) default NULL,
  `fax` varchar(50) default NULL,
  `email` varchar(255) default NULL,
  `firstname` varchar(255) default NULL,
  `lastname` varchar(255) default NULL,
  `comment` text default NULL,
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `updated_time` datetime NULL,
  PRIMARY KEY (`store_id`),
  KEY `created_time` (`created_time`),
  KEY `updated_time` (`updated_time`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#DROP TABLE IF EXISTS {$this->getTable('livrarionline_awb')};
CREATE TABLE  IF NOT EXISTS {$this->getTable('livrarionline_awb')} (
  `awb_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `service_id` int(11) unsigned NOT NULL,
  `description` text NOT NULL,
  `reference` text NOT NULL,
  `declared_value` decimal(10,2) DEFAULT NULL,
  `cod` decimal(10,2) DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `payee` int(11) unsigned NOT NULL,
  `pick_from` int(11) unsigned NOT NULL,
  `num_packages` int(11) unsigned NOT NULL,
  `insurance` tinyint(1) NOT NULL DEFAULT '0',
  `returndocs` tinyint(1) NOT NULL DEFAULT '0',
  `returndocsbank` tinyint(1) NOT NULL DEFAULT '0',
  `deliveryconf` tinyint(1) NOT NULL DEFAULT '0',
  `deliverysat` tinyint(1) NOT NULL DEFAULT '0',
  `emailnotify` tinyint(1) NOT NULL DEFAULT '0',
  `smsnotify` tinyint(1) NOT NULL DEFAULT '0',
  `deliveryconfmerchant` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_time` datetime DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  PRIMARY KEY (`awb_id`),
  KEY `created_time` (`created_time`),
  KEY `updated_time` (`updated_time`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#DROP TABLE IF EXISTS {$this->getTable('livrarionline_awb_parcels')};
CREATE TABLE  IF NOT EXISTS {$this->getTable('livrarionline_awb_parcels')} (
  `parcel_id` int(11) unsigned NOT NULL auto_increment,
  `awb_id` int(11) unsigned NOT NULL,
  `parcel_type` int(11) unsigned NOT NULL,
  `content` text NOT NULL default '',
  `weight` decimal(10,2) NOT NULL default '0',
  `length` decimal(10,2) NOT NULL default '0',
  `width` decimal(10,2) NOT NULL default '0',
  `height` decimal(10,2) NOT NULL default '0',
  `created_time` datetime NULL,
  `updated_time` datetime NULL,
  PRIMARY KEY (`parcel_id`),
  KEY `created_time` (`created_time`),
  KEY `updated_time` (`updated_time`),
  KEY `awb_id` (`awb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE {$this->getTable('livrarionline_awb_parcels')}
  ADD CONSTRAINT `FK_AWB_ID` FOREIGN KEY (`awb_id`) REFERENCES {$this->getTable('livrarionline_awb')} (`awb_id`) ON DELETE CASCADE;

    ");
	
$installer->endSetup(); 