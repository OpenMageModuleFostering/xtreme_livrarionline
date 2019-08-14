<?php
//require_once('/lib/lo.php');
class Xtreme_Livrarionline_Adminhtml_ManageawbController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('livrarionline/awb')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('AWB Manager'), Mage::helper('adminhtml')->__('AWB Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('livrarionline/awb')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('awb_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('livrarionline/awb');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('AWB Manager'), Mage::helper('adminhtml')->__('AWB Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('livrarionline/adminhtml_manageawb_edit'))
				->_addLeft($this->getLayout()->createBlock('livrarionline/adminhtml_manageawb_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('livrarionline')->__('AWB does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		//Mage::log("saveAction");
		if ($data = $this->getRequest()->getPost()) {
			
			//print "<pre>";
			//Mage::log($data);
			//print_r($data);exit;
				  			
			$model = Mage::getModel('livrarionline/awb');
			$model->setData($data)
				  ->setId($this->getRequest()->getParam('id'));
			
			//print_r($model);
						
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
				$order = Mage::getModel('sales/order')->load($model->getOrderId());
				//$shipment = Mage::getModel('sales/order_shipment')->load($order->getIncrementId());
				$shipments = $order->getShipmentsCollection();
				
				foreach($shipments as $ship)
				{
					//print '<pre>';
					//print_r($ship);
					
					$data = array();
					$data['carrier_code'] = 'livrarionline';
					$data['title'] = 'Livrari Online';
					$data['number'] = $awb_res;

					$track = Mage::getModel('sales/order_shipment_track')->addData($data);
					$ship->addTrack($track);
					$ship->save();
					
					$ship->sendEmail($email, '');
				}

				//exit;
				
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
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
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
				$model = Mage::getModel('livrarionline/awb');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('AWB was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function printAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {			
				$model = Mage::getModel('livrarionline/awb')->load($this->getRequest()->getParam('id'));			
				if($model->getAwbNo())
				{
					$html = Mage::helper('livrarionline')->printAWB($model->getAwbId());
					echo $html;
					exit;
				}
				else
				{
					echo "Missing AWB No";
					exit;
				}								
				//Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__($msg));
				//$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function cancelAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {			
				$model = Mage::getModel('livrarionline/awb')->load($this->getRequest()->getParam('id'));			
				if($model->getAwbNo())
				{
					$track = Mage::getModel('sales/order_shipment_track')->getCollection()
						->addFieldToFilter('track_number', $model->getAwbNo())
						->getFirstItem();
					if($track->getId())
						$track->delete();

					$result = Mage::helper('livrarionline')->cancelAWB($model->getAwbId());
					$msg = "AWB Cancelled successfully";
					$model->setStatus(Xtreme_Livrarionline_Model_Awb::STATUS_CANCELLED);
					$model->save();
				}
				else
				{
					echo "Missing AWB No";
					exit;
				}								
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__($msg));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $livrarionlineIds = $this->getRequest()->getParam('awb');
        if(!is_array($livrarionlineIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select awb(s)'));
        } else {
            try {
                foreach ($livrarionlineIds as $livrarionlineId) {
                    $livrarionline = Mage::getModel('livrarionline/awb')->load($livrarionlineId);
                    $livrarionline->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d AWB(s) were successfully deleted', count($livrarionlineIds)
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