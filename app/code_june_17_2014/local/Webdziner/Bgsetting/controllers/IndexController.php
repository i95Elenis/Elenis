<?php
class Webdziner_Bgsetting_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/bgsetting?id=15 
    	 *  or
    	 * http://site.com/bgsetting/id/15 	
    	 */
    	/* 
		$bgsetting_id = $this->getRequest()->getParam('id');

  		if($bgsetting_id != null && $bgsetting_id != '')	{
			$bgsetting = Mage::getModel('bgsetting/bgsetting')->load($bgsetting_id)->getData();
		} else {
			$bgsetting = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($bgsetting == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$bgsettingTable = $resource->getTableName('bgsetting');
			
			$select = $read->select()
			   ->from($bgsettingTable,array('bgsetting_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$bgsetting = $read->fetchRow($select);
		}
		Mage::register('bgsetting', $bgsetting);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}