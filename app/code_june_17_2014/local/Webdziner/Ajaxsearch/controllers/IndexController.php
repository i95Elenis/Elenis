<?php
class Webdziner_Ajaxsearch_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('ajaxsearch/ajaxsearch')->toHtml());
    }
}