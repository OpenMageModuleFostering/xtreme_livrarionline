<?php
class Xtreme_Livrarionline_Block_Adminhtml_Manageawb extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_manageawb';
    $this->_blockGroup = 'livrarionline';
    $this->_headerText = Mage::helper('livrarionline')->__('AWB Manager');
    parent::__construct();
	$this->_removeButton('add');
  }
}