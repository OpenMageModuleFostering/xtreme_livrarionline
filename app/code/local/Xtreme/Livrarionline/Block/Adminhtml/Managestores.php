<?php
class Xtreme_Livrarionline_Block_Adminhtml_Managestores extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_managestores';
    $this->_blockGroup = 'livrarionline';
    $this->_headerText = Mage::helper('livrarionline')->__('Stores Manager');
    $this->_addButtonLabel = Mage::helper('livrarionline')->__('Add Store');
    parent::__construct();
  }
}