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
* Will use this function to flush cache storage
*/
function emptyDir($dirname = null)
{
    if(!is_null($dirname)) {
        if (is_dir($dirname)) {
            if ($handle = @opendir($dirname)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file != "." && $file != "..") {
                        $fullpath = $dirname . '/' . $file;
                        if (is_dir($fullpath)) {
                            emptyDir($fullpath);
                            @rmdir($fullpath);
                        }
                        else {
                            @unlink($fullpath);
                        }
                    }
                }
                closedir($handle);
            }
        }
    }
}

$installer = $this;

$installer->startSetup();


$attr = array(
    'input'    => 'text',
    'type'     => 'text',
    'grid'     => true,
    'label'    => 'Delivery Comment',
);
$installer->addAttribute('order', 'delivery_comment', $attr);


/*
$installer->run("
DELETE FROM `{$this->getTable('eav/attribute')}` WHERE `attribute_code`='delivery_comment' limit 1;

INSERT INTO `{$this->getTable('eav/attribute')}` (entity_type_id, attribute_code, backend_type, frontend_input, frontend_label) VALUES ($typeId, 'delivery_comment', 'text', 'text', 'Delivery Comment');
");
*/


/**
* Need to fluch cache storage now
*/
emptyDir(Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);


$installer->endSetup();