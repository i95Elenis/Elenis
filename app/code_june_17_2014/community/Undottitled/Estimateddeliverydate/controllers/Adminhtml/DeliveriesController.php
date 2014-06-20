<?php
 
class Undottitled_Estimateddeliverydate_Adminhtml_DeliveriesController extends Mage_Adminhtml_Controller_Action
{
 
    protected function _initAction()
    {
		$this->loadLayout()
            ->_setActiveMenu('estimateddeliverydate/deliveries')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Retailer Management'), Mage::helper('adminhtml')->__('Delivery Date Management'));
		return $this;
    }   
   
    public function indexAction() {
        $this->_initAction(); 
		
		$this->_addContent(
			$this->getLayout()->createBlock('estimateddeliverydate/adminhtml_deliveries', 'deliveries')
		);
		$this->renderLayout();
    }
	
	
	public function newAction() {
        $this->_forward('edit');
    }
	
	
	public function editAction() {
       	$this->_initAction(); 
        $deliveriesModel = Mage::getModel('estimateddeliverydate/deliveries')->load($this->getRequest()->getParam('id'));
       
		if ($deliveriesModel->getId()) {
 			Mage::register('deliveries_data', $deliveriesModel);
			
			$this->loadLayout();
            $this->_setActiveMenu('deliveries/items');
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
           $this->_addContent($this->getLayout()->createBlock('estimateddeliverydate/adminhtml_deliveries_edit', 'deliveries'))
				->_addLeft($this->getLayout()->createBlock('estimateddeliverydate/adminhtml_deliveries_edit_tabs'));
		    
            $this->renderLayout();
        } else {
            $this->loadLayout();
            $this->_setActiveMenu('deliveries/items');
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('estimateddeliverydate/adminhtml_deliveries_edit', 'deliveries'))
				->_addLeft($this->getLayout()->createBlock('estimateddeliverydate/adminhtml_deliveries_edit_tabs'));
		    
            $this->renderLayout();
        }
    }
	
	public function saveAction() {
		if ($this->getRequest()->getPost() ) {
            try {
				$postData = $this->getRequest()->getPost();
               
				if($postData['in_products']) {
					$product = Mage::getModel("catalog/product")->load($postData['in_products']);
					if($product) {
						$postData['pid'] = $product->getId();
						$postData['sku'] = $product->getSku();						
					} else {
						$postData['pid'] = "";
						$postData['sku'] = "";
					}
				}
               				
               $deliveriesModel = Mage::getModel('estimateddeliverydate/deliveries');
               if($postData['date'] != NULL)
				{
	                $date1 = Mage::app()->getLocale()->date($postData['date'], Zend_Date::DATETIME_SHORT);
	                $postData['date'] = ($date1->toString('YYYY-MM-dd HH:mm:ss'));
	            } 
			   if($this->getRequest()->getParam('id')) {
			   	    $deliveriesModel->setId($this->getRequest()->getParam('id'))->addData($postData);
					$deliveriesModel->save();
					$message = "Delivery was successfully updated";
				} else {
					unset($postData['id']);
					$deliveriesModel->addData($postData);					
					$deliveriesModel->save();
					$message = "Delivery was successfully saved";
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__($message));
                Mage::getSingleton('adminhtml/session')->setDeliveriesData(false);
 				if(!isset($chgRedirect)) {
	                $this->_redirect('*/*/index');
				} else {
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				}
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setDeliveriesData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
		 }
        $this->_redirect('*/*/index');
	}	

	public function deleteAction() {
        $model = Mage::getModel('estimateddeliverydate/deliveries');
        $id = $this->getRequest()->getParam('id');
 
        try {
            if ($id) {
                if (!$model->load($id)->getId()) {
                    Mage::throwException($this->__('No record with ID "%s" found.', $id));
                }
                $sku = $model->getSku();
                $model->delete();
                $this->_getSession()->addSuccess($this->__('"%s" (Delivery ID: %d) was successfully deleted', $sku, $id));
                $this->_redirect('*/*');
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*');
        }
	}
	
	public function gridAction()
    {
    	$blockMarkup = $this->getLayout()->createBlock('estimateddeliverydate/adminhtml_deliveries_edit_tab_products')->toHtml();
    	$this->getResponse()->setBody($blockMarkup);

    }

        
}