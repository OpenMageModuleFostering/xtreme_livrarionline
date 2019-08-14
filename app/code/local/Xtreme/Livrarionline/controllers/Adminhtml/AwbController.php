<?php

class Xtreme_Livrarionline_Adminhtml_AwbController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
			//->_setActiveMenu('livrarionline/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('LO Create AWB'), Mage::helper('adminhtml')->__('LO Create AWB'));
		$order_id     = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel("sales/order")->load($order_id);		
		$block = $this->getLayout()->getBlock("lo_awb");
		$block->setOrder($order);
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('livrarionline/livrarionline')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('livrarionline_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('livrarionline/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('livrarionline/adminhtml_livrarionline_edit'))
				->_addLeft($this->getLayout()->createBlock('livrarionline/adminhtml_livrarionline_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('livrarionline')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
							  			
			$model = Mage::getModel('livrarionline/awb');
			$model->setData($data)
				  ->setId($this->getRequest()->getParam('id'));
									
			try {
				if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				if($model->getAwbId())
				{
					foreach($data['parcels'] as $parcel)
					{
						$modelparcel = Mage::getModel('livrarionline/awb_parcels');
						$modelparcel->setData($parcel)
							  ->setId($this->getRequest()->getParam('parcel_id'));
						if ($modelparcel->getCreatedTime() == NULL || $modelparcel->getUpdateTime() == NULL) {
							$modelparcel->setCreatedTime(now())
								->setUpdateTime(now());
						} else {
							$modelparcel->setUpdateTime(now());
						}		  
						$modelparcel->setAwbId($model->getAwbId());
						$modelparcel->save();
					}
				}
				
				$awb_res = Mage::helper('livrarionline')->createAwbFromOrder($model->getAwbId());
				
				$model->setAwbNo($awb_res);
				$model->save();
				
				$order = Mage::getModel('sales/order')->load($model->getOrderId());
				//Mage::log($order->getData());
				$shipments = $order->getShipmentsCollection();
				
				foreach($shipments as $ship)
				{					
					$data = array();
					$data['carrier_code'] = 'livrarionline';
					$data['title'] = 'Livrari Online';
					$data['number'] = $awb_res;

					$track = Mage::getModel('sales/order_shipment_track')->addData($data);
					$ship->addTrack($track);
					$ship->save();
					
					$ship->sendEmail($order->getCustomerEmail(), '');
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('livrarionline')->__('AWB was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getAwbId()));
					return;
				}
				Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id'=>$model->getOrderId())));
				return;
            } catch (Exception $e) {
		//print $e->getMessage();exit;				
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/index', array('order_id' => $this->getRequest()->getParam('order_id')));
                return;
            }
        }
	exit;
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('livrarionline')->__('Unable to find awb to save'));
	$this->_redirect('*/*/');
    }
 
    public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('livrarionline/livrarionline');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $livrarionlineIds = $this->getRequest()->getParam('livrarionline');
        if(!is_array($livrarionlineIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($livrarionlineIds as $livrarionlineId) {
                    $livrarionline = Mage::getModel('livrarionline/livrarionline')->load($livrarionlineId);
                    $livrarionline->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($livrarionlineIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $livrarionlineIds = $this->getRequest()->getParam('livrarionline');
        if(!is_array($livrarionlineIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($livrarionlineIds as $livrarionlineId) {
                    $livrarionline = Mage::getSingleton('livrarionline/livrarionline')
                        ->load($livrarionlineId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($livrarionlineIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'livrarionline.csv';
        $content    = $this->getLayout()->createBlock('livrarionline/adminhtml_livrarionline_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'livrarionline.xml';
        $content    = $this->getLayout()->createBlock('livrarionline/adminhtml_livrarionline_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}