<?php
/**
 * Quickrfq extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Quickrfq
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Quickrfq_Adminhtml_QuickrfqController extends Mage_Adminhtml_Controller_action
{
    	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('quickrfq/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('quickrfq/quickrfq')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('quickrfq_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('quickrfq/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('quickrfq/adminhtml_quickrfq_edit'))
				->_addLeft($this->getLayout()->createBlock('quickrfq/adminhtml_quickrfq_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('quickrfq')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	//public function newAction() {
	//	$this->_forward('edit');
	//}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$todaydate=date("Y-m-d");
			$data['update_date']=$todaydate;
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('quickrfq/quickrfq');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('quickrfq')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('quickrfq')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('quickrfq/quickrfq');
				// print_r($model);exit;
				$tableName=Mage::getSingleton('core/resource')->getTableName('quickrfq/quickrfq');
				$id=$this->getRequest()->getParam('id');
				$getupload_query="SELECT prd FROM $tableName where quickrfq_id=$id";
				$file_data = Mage::getSingleton('core/resource') ->getConnection('core_read')->fetchAll($getupload_query);
				$file_name=$file_data[0]['prd'];
				$pos=strripos($_SERVER['SCRIPT_FILENAME'],"/");
				$DirPath=substr($_SERVER['SCRIPT_FILENAME'],0, $pos) . DS . 'media' . DS . 'quickrfq' . DS;
				$filePath = $DirPath  . $file_name ;
				if (!empty($file_name)) 
				{
					if(file_exists($filePath))
					{
				       
						unlink($filePath);
						$model->setId($this->getRequest()->getParam('id'))
							->delete();
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('RFQ and its related uploaded file both were successfully deleted '));
						$this->_redirect('*/*/');
					}
					else
					{
						 $model->setId($this->getRequest()->getParam('id'))
						->delete();
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('RFQ was successfully deleted'));
						$this->_redirect('*/*/');
					}
					
			       
				}
			       else{
					 $model->setId($this->getRequest()->getParam('id'))
						->delete();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('RFQ was successfully deleted'));
					$this->_redirect('*/*/');
				}
				
			} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $quickrfqIds = $this->getRequest()->getParam('quickrfq');
        if(!is_array($quickrfqIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($quickrfqIds as $quickrfqId) {
			$quickrfq = Mage::getModel('quickrfq/quickrfq')->load($quickrfqId);
			$id=$quickrfq->getId();
			$tableName=Mage::getSingleton('core/resource')->getTableName('quickrfq/quickrfq');
			$getupload_query="SELECT prd FROM $tableName where quickrfq_id=$id";
			$file_data = Mage::getSingleton('core/resource') ->getConnection('core_read')->fetchAll($getupload_query);
			$file_name=$file_data[0]['prd'];
			$pos=strripos($_SERVER['SCRIPT_FILENAME'],"/");
			$DirPath=substr($_SERVER['SCRIPT_FILENAME'],0, $pos) . DS . 'media' . DS . 'quickrfq' . DS;;
			$filePath = $DirPath  . $file_name ;
			if (!empty($file_name)) 
			{
				if(file_exists($filePath))
				{
					unlink($filePath);
					$quickrfq->delete();
				}
				else
				{
					 $quickrfq->delete();
				}
				
		       
			}
		       else
		       {
				$quickrfq->delete();
			}
                }
		
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($quickrfqIds)
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
        $quickrfqIds = $this->getRequest()->getParam('quickrfq');
        if(!is_array($quickrfqIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($quickrfqIds as $quickrfqId) {
                    $quickrfq = Mage::getSingleton('quickrfq/quickrfq')
                        ->load($quickrfqId);
			$id=$quickrfq->getId();
			$newstatus=$this->getRequest()->getParam('status');
			$todaydate=date("Y-m-d");
            $tableName=Mage::getSingleton('core/resource')->getTableName('quickrfq/quickrfq');
			$getupdate_query="UPDATE $tableName SET status='$newstatus',update_date='$todaydate' where quickrfq_id=$id";
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$result = $db->query($getupdate_query);
			
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($quickrfqIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'quickrfq.csv';
        $content    = $this->getLayout()->createBlock('quickrfq/adminhtml_quickrfq_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'quickrfq.xml';
        $content    = $this->getLayout()->createBlock('quickrfq/adminhtml_quickrfq_grid')
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