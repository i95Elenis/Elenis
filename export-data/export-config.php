

<?php
//Initialing cache
ob_start("ob_gzhandler");

ini_set('error_reporting',1);
error_reporting(E_ALL);

session_cache_limiter('nocache');
header('Expires: ' . gmdate('r', 0));
header('Content-type: application/json');
define('MAGENTO', realpath(dirname(__FILE__)));

require_once MAGENTO .DIRECTORY_SEPARATOR. 'app'.DIRECTORY_SEPARATOR.'Mage.php';

Mage::app();
/**
 * Connection API v2
 */
echo "ghg";exit;
$options = array(
    'trace' => true,
    'connection_timeout' => 120,
    'wsdl_cache' => WSDL_CACHE_NONE,
);
$proxy = new SoapClient('http://elenidev2.vanwestmedia.com/www2/index.php/api/v2_soap?wsdl=1', $options);
$sessionId = $proxy->login('apiuser', 'apikey');

 
/**
 * Simple product #1 (sku : SKU-001)
 */
$productData = array(
    'name' => 'Name of product #1',
    'description' => 'Description of product #1',
    'short_description' => 'Short description of product #1',
    'website_ids' => array('base'), // Id or code of website
    'status' => 1, // 1 = Enabled, 2 = Disabled
    'visibility' => 1, // 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
    'tax_class_id' => 2, // Default VAT
    'weight' => 0,
    'stock_data' => array(
        'use_config_manage_stock' => 0,
        'manage_stock' => 0, // We do not manage stock, for example
    ),
    'price' => 9.90, // Same price than configurable product, no price change
    'additional_attributes' => array(
        'single_data' => array(
            array(
                'key'   => 'color',
                'value' => 'Blue', // Id or label of color, attribute that will be used to configure product
            ),
            array(
                'key'   => 'size',
                'value' => 'Large', // Id or label of size, attribute that will be used to configure product
            ),
        ),
    ),
);
// Creation of product #1
$proxy->catalogProductCreate($sessionId, 'simple', 'Default', 'SKU-001', $productData);
 
/**
 * Simple product #2 (sku : SKU-002)
 */
$productData = array(
    'name' => 'Name of product #2',
    'description' => 'Description of product #2',
    'short_description' => 'Short description of product #2',
    'website_ids' => array('base'), // Id or code of website
    'status' => 1, // 1 = Enabled, 2 = Disabled
    'visibility' => 1, // 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
    'tax_class_id' => 2, // Default VAT
    'weight' => 0,
    'stock_data' => array(
        'use_config_manage_stock' => 0,
        'manage_stock' => 0, // We do not manage stock, for example
    ),
    'price' => 8.90, // Red product is $1 less than configurable product
    'additional_attributes' => array(
        'single_data' => array(
            array(
                'key'   => 'color',
                'value' => 'Red', // Id or label of color, attribute that will be used to configure product
            ),
            array(
                'key'   => 'size',
                'value' => 'Medium', // Id or label of size, attribute that will be used to configure product
            ),
        ),
    ),
);
// Creation of product #2
$proxy->catalogProductCreate($sessionId, 'simple', 'Default', 'SKU-002', $productData);
 
/**
 * Configurable product
 */
$productData = array(
    'name' => 'Configurable product',
    'description' => 'Description of configurable product',
    'short_description' => 'Short description of configurable product',
    'website_ids' => array('base'), // Id or code of website
    'status' => 1, // 1 = Enabled, 2 = Disabled
    'visibility' => 4, // 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
    'tax_class_id' => 2, // Default VAT
    'weight' => 0,
    'stock_data' => array(
        'use_config_manage_stock' => 0,
        'manage_stock' => 0, // We do not manage stock, for example
    ),
    'price' => 9.90,
    'associated_skus' => array('SKU-001', 'SKU-002'), // Simple products to associate
    'price_changes' => array(
        array(
            'color' => array(
                'Red' => '2',
                'Blue' => '-10%',
            ),
            'size' => array(
                'Large'  => '+1',
                'Medium'   => '-3',
            ),
        ),
    ),
);
// Creation of configurable product
$proxy->call($sessionId, 'product.create', array('configurable', 'Default', 'SKU-TEST', $productData));

