<?php

class Xtreme_Livrarionline_Block_Adminhtml_Sales_Order_View_Tab_Loawb  extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _construct()
    {
		parent::_construct();
        $this->setTemplate('livrarionline/sales/order/view/loawb.phtml');
 
		Mage::Log("LO AWB Tab Created");
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Retrieve source model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getOrder();
    }


    public function getTabLabel()
    {
        return Mage::helper('sales')->__('LO AWB');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('LivrariOnline Manage AWB');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}