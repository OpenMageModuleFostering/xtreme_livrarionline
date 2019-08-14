<?php

$installer = $this;
$installer->startSetup();
$installer->run("

ALTER TABLE {$this->getTable('livrarionline_stores')}
  add column `default` tinyint(1) NOT NULL default '0' AFTER `comment`;

    ");
	
$installer->endSetup(); 