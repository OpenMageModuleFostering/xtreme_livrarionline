<?php

class Xtreme_Livrarionline_Model_Mysql4_Awb extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the awb_id refers to the key field in your database table.
        $this->_init('livrarionline/awb', 'awb_id');
    }
}