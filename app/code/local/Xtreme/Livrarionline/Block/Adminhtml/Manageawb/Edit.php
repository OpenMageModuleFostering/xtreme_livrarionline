<?php

class Xtreme_Livrarionline_Block_Adminhtml_Manageawb_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'livrarionline';
        $this->_controller = 'adminhtml_manageawb';
        
        $this->_updateButton('save', 'label', Mage::helper('livrarionline')->__('Save AWB'));
        $this->_updateButton('delete', 'label', Mage::helper('livrarionline')->__('Delete AWB'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('livrarionline_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'livrarionline_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'livrarionline_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('awb_data') && Mage::registry('awb_data')->getId() ) {
            return Mage::helper('livrarionline')->__("Edit AWB '%s'", $this->htmlEscape(Mage::registry('awb_data')->getId()));
        } else {
            return Mage::helper('livrarionline')->__('Add AWB');
        }
    }
}