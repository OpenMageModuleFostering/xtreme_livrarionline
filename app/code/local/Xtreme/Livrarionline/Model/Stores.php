<?php

class Xtreme_Livrarionline_Model_Stores extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('livrarionline/stores');
    }
	
    public function toOptionArray()
    {
		$collection = $this->getCollection()->addFieldToFilter("status", Xtreme_Livrarionline_Model_Status::STATUS_ENABLED);
        $arr = array();
        foreach ($collection as $store) {
            $arr[] = array('value'=>$store->getId(), 'label'=>$store->getName());
        }
        return $arr;
    }

	public function toggleStoresDefault($store_id)
	{
		$resource = Mage::getSingleton('core/resource');
		$tableName = $this->getResource()->getMainTable();
		$sql = "UPDATE `${tableName}` SET `default` = '0' WHERE `store_id` != '${store_id}'";
		$write = $resource->getConnection('core_write');
		$write->query($sql);
	}	
}