<?php

class Xtreme_Livrarionline_Adminhtml_ManagestoresController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('livrarionline/stores')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Stores Manager'), Mage::helper('adminhtml')->__('Stores Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('livrarionline/stores')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}		

			Mage::register('store_data', $model);
			//print_r($model->getData());exit;

			$this->loadLayout();
			$this->_setActiveMenu('livrarionline/stores');
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Stores Manager'), Mage::helper('adminhtml')->__('Stores Manager'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('livrarionline/adminhtml_managestores_edit'))
				->_addLeft($this->getLayout()->createBlock('livrarionline/adminhtml_managestores_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('livrarionline')->__('Store does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {		
		if ($data = $this->getRequest()->getPost()) {
						
			//print_r($data);
			
			$model = Mage::getModel('livrarionline/stores');
			$model->setData($data)
					->setId($this->getRequest()->getParam('id'));
			//print_r($model->getData());
			//exit;
			
			try {
				if ($model->getCreatedTime() == NULL || $model->getUpdatedTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdatedTime(now());
				} else {
					$model->setUpdadteTime(now());
				}	
				
				$model->setDefault(!empty($data['default']));
				$model->save();
				/*print "<pre>";
				print_r($model->getData());
				exit;*/
				
				$id = $this->getRequest()->getParam('id');
				$res = $model->toggleStoresDefault($id);
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('livrarionline')->__('Store was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('livrarionline')->__('Unable to find store to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('livrarionline/stores');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Stores was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $storeIds = $this->getRequest()->getParam('stores');
        if(!is_array($storeIds)) {
	    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select store(s)'));
        } else {
            try {
                foreach ($storeIds as $storeId) {
                    $store = Mage::getModel('livrarionline/stores')->load($storeId);
                    $storeIds->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($storeIds)
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
        $carrierIds = $this->getRequest()->getParam('stores');
        if(!is_array($carrierIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select carrier(s)'));
        } else {
            try {
                foreach ($carrierIds as $carrierId) {
                    $carrier = Mage::getSingleton('livrarionline/stores')
                        ->load($carrierId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($carrierIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'stores.csv';
        $content    = $this->getLayout()->createBlock('livrarionline/adminhtml_managecarriers_grid')
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
	
	public function stateAction()
	{
        $countrycode = $this->getRequest()->getParam('country');
        $state = "<option value=''>Please Select State</option>";
        if ($countrycode != '') {
            $statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter($countrycode)->load();
            foreach ($statearray as $_state) {
                $state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
            }
        }
        echo $state;
    }
}