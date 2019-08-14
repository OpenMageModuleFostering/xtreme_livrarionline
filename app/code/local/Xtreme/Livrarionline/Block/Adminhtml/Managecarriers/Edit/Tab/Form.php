<?php

class Xtreme_Livrarionline_Block_Adminhtml_Managecarriers_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('livrarionline_form', array('legend'=>Mage::helper('livrarionline')->__('Carrier information')));

		$fieldset->addField('name', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'name',
		));

		$fieldset->addField('service_id', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Service ID'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'service_id',
		));

		$fieldset->addField('shipping_company_id', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Shipping Company ID'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'shipping_company_id',
		));

		/*$fieldset->addField('filename', 'file', array(
		  'label'     => Mage::helper('livrarionline')->__('File'),
		  'required'  => false,
		  'name'      => 'filename',
		));*/
		$fieldset->addField('status', 'select', array(
		  'label'     => Mage::helper('livrarionline')->__('Status'),
		  'name'      => 'status',
		  'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => Mage::helper('livrarionline')->__('Enabled'),
			  ),

			  array(
				  'value'     => 2,
				  'label'     => Mage::helper('livrarionline')->__('Disabled'),
			  ),
		  ),
		));

		/*$fieldset->addField('description', 'editor', array(
		  'name'      => 'description',
		  'label'     => Mage::helper('livrarionline')->__('Description'),
		  'title'     => Mage::helper('livrarionline')->__('Description'),
		  'style'     => 'width:300px; height:100px;',
		  'wysiwyg'   => false,
		  'required'  => true,
		));*/


		if ( Mage::getSingleton('adminhtml/session')->getCarrierData() )
		{
		  $form->setValues(Mage::getSingleton('adminhtml/session')->getCarrierData());
		  Mage::getSingleton('adminhtml/session')->setCarrierData(null);
		} elseif ( Mage::registry('carrier_data') ) {
		  $form->setValues(Mage::registry('carrier_data')->getData());
		}
		return parent::_prepareForm();
	}
}