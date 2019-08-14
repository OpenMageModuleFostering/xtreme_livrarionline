<?php

class Xtreme_Livrarionline_Model_Mysql4_Carriers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the livrarionline_id refers to the key field in your database table.
        $this->_init('livrarionline/carriers', 'carrier_id');
    }
}