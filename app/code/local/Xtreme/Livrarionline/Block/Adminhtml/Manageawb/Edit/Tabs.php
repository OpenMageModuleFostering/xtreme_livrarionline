<?php

class Xtreme_Livrarionline_Block_Adminhtml_Manageawb_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('livrarionline_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('livrarionline')->__('AWB Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('livrarionline')->__('AWB Information'),
          'title'     => Mage::helper('livrarionline')->__('AWB Information'),
          'content'   => $this->getLayout()->createBlock('livrarionline/adminhtml_manageawb_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}