<?php


 /* Quickrfq extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Quickrfq
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
 
class FME_Quickrfq_IndexController extends Mage_Core_Controller_Front_Action
{   const XML_PATH_EMAIL_RECIPIENT  = 'quickrfq/email/recipient';
    const XML_PATH_EMAIL_SENDER     = 'quickrfq/email/sender';
    const XML_PATH_EMAIL_TEMPLATE   = 'quickrfq/email/template';
    const XML_PATH_ENABLED          = 'quickrfq/option/enable';
    const XML_PATH_UPLOAD          = 'quickrfq/upload/allow';
     const XML_PATH_SUBJECT          = 'quickrfq/email_reply/subject';
    const XML_PATH_BODY          = 'quickrfq/email_reply/body';
    public function preDispatch()
    {
        parent::preDispatch();

        if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) ) {
         Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Sorry This Feature is disabled temporarily'));
         $this->norouteAction();
	
	}
    }
    public function indexAction()
    {
    	
    	 $this->loadLayout();
         $this->renderLayout();
    }
     /***************************************************************
     this function validates and saves posted rfq data from frontend
     and also sends the emails to admin and the client who has
     requested the quote
    ***************************************************************/
    public function postAction()
    {
	 
	/***************************************************************
	 $rfqdata saves the posted data as an array
	***************************************************************/
	$rfqdata = $this->getRequest()->getPost();
//	echo "<pre>";print_r($rfqdata);exit;
	
	$params = $this->getRequest()->getParams();
    //echo "<pre>";print_r($params);exit;
	/***************************************************************
		    check whether any data posted or not
	***************************************************************/
	
	if ( $rfqdata ) {
	/***************************************************************
	    check servers date and save it as create date in database
	***************************************************************/
	   
	    $todaydate=date("Y-m-d");
	    $rfqdata['create_date']=$todaydate;
	    $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
	/***************************************************************
	    $postObject is to set all the posted data to send emails
	***************************************************************/
                $postObject = new Varien_Object();
                $postObject->setData($rfqdata);
	/***************************************************************
	    these variables are set default value as false
	    further they will be used as to check which required fields
	    are not validating
	***************************************************************/
                $nameerror = false;
		$hideiterror= false;
		$emailerror = false;
		$overviewerror = false;
		//$captchaerror = false;
	/***************************************************************
	   zend validator validates the required fields
	***************************************************************/
                if (!Zend_Validate::is(trim($rfqdata['contact_name']) , 'NotEmpty')) { 
                    $nameerror = true;
                }
		/*if (!Zend_Validate::is(trim($rfqdata['security_code']) , 'NotEmpty')) { 
                    $captchaerror = true;
                }
                */
                if (!Zend_Validate::is(trim($rfqdata['overview']) , 'NotEmpty')) {
                    $overviewerror = true;
                }

                if (!Zend_Validate::is(trim($rfqdata['email']), 'EmailAddress')) {
                    $emailerror = true;
                }

                if (Zend_Validate::is(trim($rfqdata['hideit']), 'NotEmpty')) {
                    $hideiterror = true;
                }
	/***************************************************************
	   if error returned by zend validator then add an error message
	***************************************************************/
		 /*if ($captchaerror) {
		    
		    $translate->setTranslateInline(true);
                    Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Please Enter verification text '));
                }
		*/
		 if ($nameerror) {
		     $translate->setTranslateInline(true);
                    Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Please Enter your Name'));
                }
		if ($overviewerror) {
		     $translate->setTranslateInline(true);
                    Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Please Give a brief Overview'));
                }
		if ($emailerror) {
		     $translate->setTranslateInline(true);
                    Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Please Enter a valid Email Address'));
                }
		/***************************************************************
		    if any error occurs then throw an exception so not to move
		    from this forward this if condition
		 ***************************************************************/

                //if ($hideiterror || $nameerror  || $overviewerror  || $emailerror || $captchaerror) { 
                if ($hideiterror || $nameerror  || $overviewerror  || $emailerror ) { 
                    throw new Exception();
                }
		/*if (!$captchaerror && $rfqdata['security_code']!= $rfqdata['captacha_code']) {
		    
				$translate->setTranslateInline(true);
				Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Sorry The Security Code You Entered Was Incorrect'));
				throw new Exception();
		}
         * */
        
		/***************************************************************
		    if any file is uploaded then move first to this function
		 ***************************************************************/
                 if(isset($_FILES['prd']['name']) && $_FILES['prd']['name'] != '') {
		   
                                try {
				    
				    /***************************************************************
					get allowed extensions from backend
				    (system/configuration/{quickrfq}configurations->upload restriction
				    for rfq)
				     ***************************************************************/
					$ext = array();
					$extensions = array();
						$ext = explode(",",Mage::getStoreConfig(self::XML_PATH_UPLOAD));
						
						foreach($ext as $exten)
						{
						    $exten=trim($exten);
						    $extensions[]=$exten;
						    
						}
                                        /* Starting upload */        
                                        $uploader = new Varien_File_Uploader('prd');
                                        
                                        // Any extention would work
					$uploader->setAllowedExtensions($extensions);
                                        $uploader->setAllowRenameFiles(false);
                                        
                                        // Set the file upload mode 
                                        // false -> get the file directly in the specified folder
                                        // true -> get the file in the product like folders 
                                        //        (file.jpg will go in something like /media/f/i/file.jpg)
                                        $uploader->setFilesDispersion(false);
                                                        
                                        // We set media/quickrfq as the upload dir
                                        $path = Mage::getBaseDir('media') . DS . 'quickrfq' . DS;
					
                                        $_FILES['prd']['name']='rfq'.'_'.time().'_'.$_FILES['prd']['name'];
                                        $uploader->save($path, $_FILES['prd']['name'] );
                                        
				    }
				    catch (Exception $e) {
					
				    
				     $translate->setTranslateInline(true);

				    Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Unable to upload your file. Use the specified extensions only '));
				   throw new Exception();
                      
				 }
		/***************************************************************
		    if any file is uploaded then and its name in the posted data
		    array to save files name in the database
		 ***************************************************************/	 
                        $rfqdata['prd'] = $_FILES['prd']['name'];
                        
		}
	    /***************************************************************
		if any any none required field is left empty then use a
		default text of "Not Mentioned" in it instead of empty field
		in database.
	     ***************************************************************/
      //  echo "<pre>";
		 foreach($rfqdata as $key => $value)
	    {
      //       echo $key."-".$value."\n";
             if(is_array($value))
             {
                 $rfqdata[$key]= implode(" , ",$value)."\n";
             }
		if($key=='hideit')
		{break;}
		if($value == null)
		{
		    $rfqdata[$key]="Not Mentioned";
		}
	    }
       // exit;
         //   $postObject
       // echo implode(",",$postObject);
            $postObject['prefered_methods']=implode(",",$postObject['prefered_methods']);
            $postObject['budget']=implode(",",$postObject['budget']);
            //echo implode(",",$postObject['prefered_methods']);exit;
            
        // $body=Mage::getStoreConfig(self::XML_PATH_BODY);
          //                          echo "<pre>";print_r($body);exit;
	    /***************************************************************
		Now after all workings save the data to DB
	     ***************************************************************/
		    $model = Mage::getModel('quickrfq/quickrfq');
		    $model->setData($rfqdata)->setId($this->getRequest()->getParam('id'));
		    try { 
                            $model->save();
                        /*    foreach($rfqdata as $key=>$value)
            {
                echo $key."<br/>";
                if($key=="project_title")
                {
                    $key="number_of_cookies";
                }
                if($key=="bugget")
                {
                    $key="cookies_cupcakes_used_for";
                }
            }
            
       echo "<pre>";print_r($postObject);print_r($rfqdata);;exit;*/
			    /***************************************************************
				Send all data to the email address saved at backend
			    (system/configuration/{quickrfq}configurations->email setup options
			    for the rfq reciever)
			     ***************************************************************/
				    
				    $mailTemplate = Mage::getModel('core/email_template');
				     /* @var $mailTemplate Mage_Core_Model_Email_Template */
				    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($rfqdata['email'])
					->sendTransactional(
					    Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
					    Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
					    Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
					    null,
					    array('data' => $rfqdata)
					);
		    
				    if (!$mailTemplate->getSentSuccess()) {
					throw new Exception();
				    }
			    /****************************************************************
			    Send an email to the client also and get its email subject and
			    the body from backend
			    (system/configuration/{quickrfq}configurations->Reply to customer)
			    *****************************************************************/    
				    $mail = new Zend_Mail();
				    $subject=Mage::getStoreConfig(self::XML_PATH_SUBJECT);
				    $subject=trim($subject);
				    if(empty($subject))
				    {
					$subject="Thank You for Requesting A Quote";
				    }
				    $body=Mage::getStoreConfig(self::XML_PATH_BODY);
                                    echo "<pre>";print_r($body);exit;
				    $body=trim($body);
				    if(empty($body))
				    {
					$body="Your request has been forwarded to the concerning department and they will be in touch shortly. 


Thank you once again for visiting our site and contacting us for a Quote. 



Regards ";
				    }
					echo "<pre>";print_r($postObject);
                                    echo "<pre>";print_r($body);exit;
				    $mail->setBodyText($body);
				    $mail->setFrom(Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT));
				    $mail->addTo($rfqdata['email']);
				    $mail->setSubject($subject);
				    try {
					$mail->send();
				    }        
				    catch(Exception $e) {
					Mage::getSingleton('core/session')->addError('Unable to send email to your account ');
			     
				    }
			    $translate->setTranslateInline(true);
			    Mage::getSingleton('core/session')->addSuccess(Mage::helper('quickrfq')->__('Your Request was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
			    $this->_redirect('*/*/');
			    return;
			}
		    catch (Exception $e)
			{
			    
			 $translate->setTranslateInline(true);
	
			Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Your request cannont be sent this time, Please, try again later'));
			 $this->_redirect('*/*/');
			return;
			 }
		
	 
	}
	    
	catch (Exception $e) {
	  
                $translate->setTranslateInline(true);
                Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__($e->getMessage()));
		Mage::getSingleton('core/session')->addError(Mage::helper('quickrfq')->__('Unable to submit your request. Please, try again later'));
		 $this->_redirect('*/*/');
                return;
            }

        }
	else
	{
           
               $this->_redirect('*/*/');

        }
      
    }
    
}