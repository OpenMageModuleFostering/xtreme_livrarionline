<?php

class Xtreme_Livrarionline_Model_Mysql4_Awb_Parcels_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('livrarionline/awb_parcels');
    }
}