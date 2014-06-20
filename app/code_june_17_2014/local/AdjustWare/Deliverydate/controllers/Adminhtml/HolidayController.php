<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      10.1.5
 * @license:     5WLwzjinYV1BwwOYUOiHBcz0D7SjutGH8xWy5nN0br
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @author Adjustware
 */  
class AdjustWare_Deliverydate_Adminhtml_HolidayController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() {
	    $this->loadLayout(); 
        $this->_setActiveMenu('sales/adjdeliverydate');
        $this->_addBreadcrumb($this->__('Holidays'), $this->__('Holidays')); 
        $this->_addContent($this->getLayout()->createBlock('adjdeliverydate/adminhtml_holiday')); 	    
 	    $this->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('adjdeliverydate/holiday')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('holiday_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('sales/adjdeliverydate');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('adjdeliverydate/adminhtml_holiday_edit'));
            
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adjdeliverydate')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->editAction();
	}
 
	public function saveAction() {
	    $id     = $this->getRequest()->getParam('id');
	    $model  = Mage::getModel('adjdeliverydate/holiday');
		if ($data = $this->getRequest()->getPost()) {
			$model->setData($data)->setId($id);
			try {
				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adjdeliverydate')->__('Holiday has been successfully saved'));    
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				$this->_redirect('*/*/');
				return;
				
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adjdeliverydate')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('holiday');
        if(!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adjdeliverydate')->__('Please select holiday(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('adjdeliverydate/holiday')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('adjdeliverydate/holiday');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adjdeliverydate')->__('Holiday has been deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
}