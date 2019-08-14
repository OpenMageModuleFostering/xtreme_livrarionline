<?php
class Xtreme_Livrarionline_Block_Adminhtml_Awb extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_awb';
    $this->_blockGroup = 'livrarionline';
    $this->_headerText = Mage::helper('livrarionline')->__('LO Create AWB');
    $this->_addButtonLabel = Mage::helper('livrarionline')->__('Create AWB');
	//$this->setOrderId(22);
    parent::__construct();
  }
}