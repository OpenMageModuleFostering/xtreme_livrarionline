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

		$fieldset->addField('flat_fee', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Flat Fee'),
			'required'  => true,
			'name'      => 'flat_fee',
			'note'	    => Mage::helper('livrarionline')->__('Any negative number if you don\'t want to use quotation module (eg: -1)'),
			'value' 	=> -1.00
		));

		$states = Mage::getModel('directory/country')->load('RO')->getRegions()->toOptionArray();
		array_shift($states);
		$fieldset->addField('applicable_states', 'multiselect', array(
			'label'    => Mage::helper('livrarionline')->__('Applicable to the following states'),
			'class'    => 'required-entry',
			'required' => true,
			'name'     => 'applicable_states',
			'values'   => $states,
			'note'	   => Mage::helper('livrarionline')->__('CTRL + click for multiple select')
        ));

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