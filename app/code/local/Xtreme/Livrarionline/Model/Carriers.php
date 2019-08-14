<?php

class Xtreme_Livrarionline_Model_Carriers extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('livrarionline/carriers');
    }
	
    public function toOptionArray()
    {
		$collection = $this->getCollection()->addFieldToFilter("status", Xtreme_Livrarionline_Model_Status::STATUS_ENABLED);
        $arr = array();
        foreach ($collection as $carrier) {
            $arr[] = array('value'=>$carrier->getId(), 'label'=>$carrier->getName());
        }
        return $arr;
    }	
}