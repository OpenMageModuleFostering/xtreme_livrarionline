<?php

class Xtreme_Livrarionline_Model_Awb extends Mage_Core_Model_Abstract
{
    const TIPCOLET_PLIC		= 1;
    const TIPCOLET_COLET	= 2;
    const TIPCOLET_PALET	= 3;

    const PARCELCONTENT_ACTE			= 1;
    const PARCELCONTENT_TIPIZATE		= 2;
    const PARCELCONTENT_FRAGILE			= 3;
    const PARCELCONTENT_GENERALE		= 4;

    const STATUS_ENABLED	= 1;
    const STATUS_CANCELLED	= 2;

    public function _construct()
    {
        parent::_construct();
        $this->_init('livrarionline/awb');
    }
	
    static public function getTipColetOptionArray()
    {
        return array(
            self::TIPCOLET_PLIC    => Mage::helper('livrarionline')->__('Envelope'),
            self::TIPCOLET_COLET   => Mage::helper('livrarionline')->__('Parcel'),
            self::TIPCOLET_PALET   => Mage::helper('livrarionline')->__('Palet')
        );
    }

    static public function getParcelContentOptionArray()
    {
        return array(
            self::PARCELCONTENT_ACTE    => Mage::helper('livrarionline')->__('Documents'),
            self::PARCELCONTENT_TIPIZATE   => Mage::helper('livrarionline')->__('Preprinted'),
            self::PARCELCONTENT_FRAGILE   => Mage::helper('livrarionline')->__('Fragile'),
            self::PARCELCONTENT_GENERALE   => Mage::helper('livrarionline')->__('General')
        );
    }

    static public function getStatusOptionArray()
    {
        return array(
            self::STATUS_ENABLED    	=> Mage::helper('livrarionline')->__('Enabled'),
            self::STATUS_CANCELLED   	=> Mage::helper('livrarionline')->__('Cancelled')
        );
    }	
}