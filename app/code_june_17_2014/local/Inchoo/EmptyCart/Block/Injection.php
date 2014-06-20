<?php

/**
 * @category   Inchoo
 * @package    Inchoo_EmptyCart
 * @author     Mladen Lotar - mladen.lotar@surgeworks.com
 */

class Inchoo_EmptyCart_Block_Injection extends Mage_Core_Block_Text
{
	public function setPassingTransport($transport)
	{
		$this->setData('text', $transport.$this->_generateContent());
	}

	private function _generateContent()
	{
		$_extensionDirectory = dirname(dirname(__FILE__));
		$_javascriptFileName = 'content.phtml';
		$_templateDirectory = 'template';
		$_fileContents = file_get_contents($_extensionDirectory . DS . $_templateDirectory . DS . $_javascriptFileName);
		return eval('?>' . $_fileContents);
	}
}