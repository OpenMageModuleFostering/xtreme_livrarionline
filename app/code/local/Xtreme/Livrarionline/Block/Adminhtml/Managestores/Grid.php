<?php

class Xtreme_Livrarionline_Block_Adminhtml_Managestores_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('livrarionlineGrid');
      $this->setDefaultSort('store_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('livrarionline/stores')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('store_id', array(
          'header'    => Mage::helper('livrarionline')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'store_id',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('livrarionline')->__('Store Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));

      $this->addColumn('email', array(
          'header'    => Mage::helper('livrarionline')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));

      $this->addColumn('firstname', array(
          'header'    => Mage::helper('livrarionline')->__('Firstname'),
          'align'     =>'left',
          'index'     => 'firstname',
      ));

      $this->addColumn('lastname', array(
          'header'    => Mage::helper('livrarionline')->__('Lastname'),
          'align'     =>'left',
          'index'     => 'lastname',
      ));

      $this->addColumn('country', array(
          'header'    => Mage::helper('livrarionline')->__('Country'),
          'align'     =>'left',
          'index'     => 'country',
      ));

      $this->addColumn('state', array(
          'header'    => Mage::helper('livrarionline')->__('State'),
          'align'     =>'left',
          'index'     => 'state',
      ));

	  
      $this->addColumn('created_time', array(
			'header'    => Mage::helper('livrarionline')->__('Created'),
			'width'     => '150px',
			'type'      => 'datetime',
			'index'     => 'created_time',
      ));

      $this->addColumn('updated_time', array(
			'header'    => Mage::helper('livrarionline')->__('Updated'),
			'width'     => '150px',
			'type'      => 'datetime',
			'index'     => 'updated_time',
      ));
	  

      $this->addColumn('status', array(
          'header'    => Mage::helper('livrarionline')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
	  $this->addColumn('default', array(
          'header'    => Mage::helper('livrarionline')->__('Default'),
          'align'     => 'center',
          'width'     => '80px',
          'index'     => 'default',
          'type'      => 'options',
          'options'   => array(
              0 => '',
              1 => 'Yes',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('livrarionline')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('livrarionline')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('livrarionline')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('livrarionline')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('livrarionline')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('store_id');
        $this->getMassactionBlock()->setFormFieldName('stores');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('livrarionline')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('livrarionline')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('livrarionline/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('livrarionline')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('livrarionline')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}