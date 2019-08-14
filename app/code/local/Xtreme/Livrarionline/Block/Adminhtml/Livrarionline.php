<?php
class Xtreme_Livrarionline_Block_Adminhtml_Livrarionline extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_livrarionline';
    $this->_blockGroup = 'livrarionline';
    $this->_headerText = Mage::helper('livrarionline')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('livrarionline')->__('Add Item');
    parent::__construct();
  }
}