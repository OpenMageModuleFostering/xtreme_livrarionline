<?php

class Xtreme_Livrarionline_Model_Livrarionline extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('livrarionline/livrarionline');
    }
}