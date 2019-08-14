<?php

class Xtreme_Livrarionline_Block_Adminhtml_Managestores_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$formData = Mage::registry('store_data')->getData();
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('livrarionline_form', array('legend'=>Mage::helper('livrarionline')->__('Stores information')));

		$fieldset->addField('name', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'name',
		));

		$fieldset->addField('address1', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Address (line 1)'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'address1',
		));

		$fieldset->addField('address2', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Address (line 2)'),
			//'class'     => 'required-entry',
			'required'  => false,
			'name'      => 'address2',
		));

		$fieldset->addField('zipcode', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Zipcode'),
			//'class'     => 'required-entry',
			'required'  => false,
			'name'      => 'zipcode',
		));

		$country = $fieldset->addField('country', 'select', array(
			'label'     => Mage::helper('livrarionline')->__('Country'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'country',
			'values'    => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
			'onchange'  => 'getstate(this)',
			//'value'  	=> 'RO'
		));
			
		/*
        * Add Ajax to the Country select box html output
        */
        $country->setAfterElementHtml("<script type=\"text/javascript\">
            function getstate(selectElement){
                var reloadurl = '". $this->getUrl('livrarionline/adminhtml_managestores/state') . "country/' + selectElement.value;
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    onComplete: function(transport){
                        var response = transport.responseText;
                        $('state').update(response);
                    }
                });
            }
        </script>");
		if(Mage::registry('store_data'))
		{
			$countrycode = Mage::registry('store_data')->getData('country');
			$states = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter($countrycode)->load();
			foreach($states as $st)
			{
				$statearray[] = array(
					'value'     => $st->getCode(),
					'label'     => $st->getDefaultName(),
				);
			}
		}
		else
		{
			$statearray = array('' => '--Please Select State--');
		}
		$fieldset->addField('state', 'select', array(
			'label'     => Mage::helper('livrarionline')->__('State'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'state',
			//'values'    => Mage::getModel('modulename/modulename')->getstate('AU'),
			'values' 	=> $statearray,
		));

		$fieldset->addField('city', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('City'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'city',
		));

		$fieldset->addField('latitude', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Latitude'),
			//'class'     => 'required-entry',
			'required'  => false,
			'name'      => 'latitude',
		));

		$fieldset->addField('longtitude', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Longtitude'),
			//'class'     => 'required-entry',
			'required'  => false,
			'name'      => 'longtitude',
		));

		$fieldset->addField('phone', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Phone'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'phone',
		));

		$fieldset->addField('fax', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Fax'),
			//'class'     => 'required-entry',
			'required'  => false,
			'name'      => 'fax',
		));

		$fieldset->addField('email', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Email'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'email',
		));

		$fieldset->addField('firstname', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Firstname'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'firstname',
		));
		$fieldset->addField('lastname', 'text', array(
			'label'     => Mage::helper('livrarionline')->__('Lastname'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'lastname',
		));

		$fieldset->addField('default', 'checkbox', array(
			'label'     => Mage::helper('livrarionline')->__('Default Store'),
			//'class'     => 'required-entry',
			'required'  => false,
			'name'      => 'default',
			'onclick'   => 'this.value = this.checked ? 1 : 0;',
			'disabled'				=> (!empty($formData['default']) && $formData['default']),
			'after_element_html' 	=> (!empty($formData['default']) && $formData['default']) ? '<small>This is the default store, it can not be unchecked.</small>' : '',
		));

		/*$fieldset->addField('filename', 'file', array(
		  'label'     => Mage::helper('livrarionline')->__('File'),
		  'required'  => false,
		  'name'      => 'filename',
		));*/

		$fieldset->addField('comment', 'editor', array(
		  'name'      => 'comment',
		  'label'     => Mage::helper('livrarionline')->__('Comment'),
		  'title'     => Mage::helper('livrarionline')->__('Comment'),
		  'style'     => 'width:300px; height:100px;',
		  'wysiwyg'   => true,
		  'required'  => false,
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

		if ( Mage::getSingleton('adminhtml/session')->getStoreData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getStoreData());
			Mage::getSingleton('adminhtml/session')->setStoreData(null);
		} elseif ( Mage::registry('store_data') ) {
			$form->setValues(Mage::registry('store_data')->getData());
			$form->getElement('default')->setIsChecked(!empty($formData['default']));
			//print_r(Mage::registry('store_data')->getData());
		}
		return parent::_prepareForm();
	}
}