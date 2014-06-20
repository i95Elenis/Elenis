<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_InteractiveController extends Aitoc_Aitsys_Abstract_Adminhtml_Controller
{
    public function indexAction()
    {
        $this->interactive();
        $this->_redirect('*/index');
    }
    
    public function interactive()
    {
        $query = array();
        $request = $this->getRequest();
        $method = $request->getParam('method');
        $query['cid'] = $request->getParam('cid');
        $query['args'] = $request->getParam('args');
        if (!$method)
        {
            $method = 'interactivePostback';
        }
        if (!$query['cid'])
        {
            unset($query['cid']);
        }
        if (!$query['args'])
        {
            unset($query['args']);
        }
        $service = $this->tool()->platform()->getService();
        if ($service->connect()->isLogined())
        {
            try
            {
                $service->$method($query);
            }
            catch (Exception $exc)
            {
                $this->tool()->testMsg($exc);
            }
            $service->disconnect();
        }
    }
    
}