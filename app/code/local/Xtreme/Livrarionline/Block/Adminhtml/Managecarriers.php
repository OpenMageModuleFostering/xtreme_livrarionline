<?php
class Xtreme_Livrarionline_Block_Adminhtml_Managecarriers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_managecarriers';
    $this->_blockGroup = 'livrarionline';
    $this->_headerText = Mage::helper('livrarionline')->__('Carriers Manager');
    $this->_addButtonLabel = Mage::helper('livrarionline')->__('Add Carrier');
    parent::__construct();
  }
}