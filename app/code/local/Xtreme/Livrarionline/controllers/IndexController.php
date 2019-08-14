<?php
class Xtreme_Livrarionline_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/livrarionline?id=15 
    	 *  or
    	 * http://site.com/livrarionline/id/15 	
    	 */
    	/* 
		$livrarionline_id = $this->getRequest()->getParam('id');

  		if($livrarionline_id != null && $livrarionline_id != '')	{
			$livrarionline = Mage::getModel('livrarionline/livrarionline')->load($livrarionline_id)->getData();
		} else {
			$livrarionline = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($livrarionline == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$livrarionlineTable = $resource->getTableName('livrarionline');
			
			$select = $read->select()
			   ->from($livrarionlineTable,array('livrarionline_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$livrarionline = $read->fetchRow($select);
		}
		Mage::register('livrarionline', $livrarionline);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}