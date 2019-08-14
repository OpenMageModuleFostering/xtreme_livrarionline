<?php

class Xtreme_Livrarionline_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {
    public function  __construct() {

        parent::__construct();

		$hasShipping = count($this->getOrder()->getShipmentsCollection());
		if($hasShipping)
		{
			$awb_col = Mage::getModel('livrarionline/awb')->getCollection()
						->addFieldToFilter('order_id', $this->getOrder()->getId())
						->addFieldToFilter('status', 1);
		}
		
		if(!$hasShipping) $title = Mage::helper('livrarionline')->__('Must create shipping first');
		else if(count($awb_col) > 0) $title = "";
		else $title = Mage::helper('livrarionline')->__('LO Create AWB');
		
		$label = Mage::helper('livrarionline')->__('LO Create AWB');
		if(count($awb_col) > 0) $label = Mage::helper('livrarionline')->__('AWB already created');
        $this->_addButton('button_id', array(
            'label'     => $label,
            'onclick'   => ($hasShipping && count($awb_col) == 0) ? 'setLocation(\'' . $this->getLOAwbUrl() . '\')' : '',
            'class'     => ($hasShipping && count($awb_col) == 0) ? 'go' : '',
			'disabled'	=> (!$hasShipping || count($awb_col) > 0) ? 'disabled' : '',
			'title'		=> $title,
        ), 0, 100, 'header', 'header');
    }
	
	public function getLOAwbUrl()
    {
        return $this->getUrl('livrarionline/adminhtml_awb');
    }
}