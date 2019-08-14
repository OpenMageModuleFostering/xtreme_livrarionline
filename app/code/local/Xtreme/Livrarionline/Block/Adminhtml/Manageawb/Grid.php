<?php

class Xtreme_Livrarionline_Block_Adminhtml_Manageawb_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('livrarionlineGrid');
      $this->setDefaultSort('awb_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
		$collection = Mage::getModel('livrarionline/awb')->getCollection();
		$collection->getSelect()->join( array('tb_orders'=>Mage::getSingleton('core/resource')->getTableName('sales/order')), 'main_table.order_id = tb_orders.entity_id', array('tb_orders.increment_id'));
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
  }

	protected function _prepareColumns()
	{
      $this->addColumn('awb_id', array(
          'header'    => Mage::helper('livrarionline')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'awb_id',
      ));

		$this->addColumn('increment_id', array(
			'header'    => Mage::helper('livrarionline')->__('Order'),
			'align'     =>'center',
			'index'     => 'increment_id',
			'width'     => '90px',
		));

      $this->addColumn('awb_no', array(
          'header'    => Mage::helper('livrarionline')->__('AWB No'),
          'align'     =>'left',
          'index'     => 'awb_no',
			'width'     => '90px',
      ));

      $this->addColumn('description', array(
          'header'    => Mage::helper('livrarionline')->__('Description'),
          'align'     =>'left',
          'index'     => 'description',
      ));

      $this->addColumn('reference', array(
          'header'    => Mage::helper('livrarionline')->__('Reference'),
          'align'     =>'left',
          'index'     => 'reference',
      ));

	  
      $this->addColumn('created_time', array(
			'header'    => Mage::helper('livrarionline')->__('Created'),
			'width'     => '150px',
			'type'      => 'datetime',
			'index'     => 'created_time',
      ));

      /*$this->addColumn('updated_time', array(
			'header'    => Mage::helper('livrarionline')->__('Updated'),
			'width'     => '150px',
			'type'      => 'datetime',
			'index'     => 'updated_time',
      ));*/
	  

      $this->addColumn('status', array(
          'header'    => Mage::helper('livrarionline')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => Xtreme_Livrarionline_Model_Awb::getStatusOptionArray()
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('livrarionline')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    //array(
                    //    'caption'   => Mage::helper('livrarionline')->__('Edit'),
                    //    'url'       => array('base'=> '*/*/edit'),
                    //    'field'     => 'id'
                    //),
                    array(
                        'caption'   => Mage::helper('livrarionline')->__('Cancel'),
                        'url'       => array('base'=> '*/*/cancel'),
                        'field'     => 'id',
						'confirm' => Mage::helper('livrarionline')->__('Are you sure?'),
                    ),
                    array(
                        'caption'   => Mage::helper('livrarionline')->__('Print'),
                        'url'       => array('base'=> '*/*/print'),
                        'field'     => 'id',
						'popup'   => true,
                    ),
                    //array(
                    //    'caption'   => Mage::helper('livrarionline')->__('Delete'),
                    //   'url'       => array('base'=> '*/*/delete'),
                    //    'field'     => 'id',
					//	'confirm' 	=> Mage::helper('livrarionline')->__('Are you sure?'),
                    //),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'awb',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('livrarionline')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('livrarionline')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('awb_id');
        $this->getMassactionBlock()->setFormFieldName('awb');

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
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}