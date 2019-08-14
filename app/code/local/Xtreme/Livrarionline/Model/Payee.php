<?php

class Xtreme_Livrarionline_Model_Payee extends Varien_Object
{
    const PAYEE_MERCHANT	= 1;
    const PAYEE_DESTINATAR	= 2;
    const PAYEE_EXPEDITOR	= 3;

    static public function getOptionArray()
    {
        return array(
            self::PAYEE_MERCHANT    => Mage::helper('livrarionline')->__('Merchant'),
            self::PAYEE_DESTINATAR   => Mage::helper('livrarionline')->__('Destinatar'),
            self::PAYEE_EXPEDITOR   => Mage::helper('livrarionline')->__('Expeditor')
        );
    }
}