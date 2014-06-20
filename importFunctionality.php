<?php

//Initialing cache
ob_start("ob_gzhandler");

ini_set('error_reporting', 1);
error_reporting(E_ALL);

define('MAGENTO', realpath(dirname(__FILE__)));

require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';

Mage::app();

/*$category = Mage::getModel('catalog/category');
    $tree = $category->getTreeModel();
    $tree->load();

    $ids = $tree->getCollection()->getAllIds();
	Mage::register("isSecureArea", 1);
	foreach($ids as $id)
	{
	
		if($id!=2 && $id!=1)
		{
			$category_id = $id;// yes you own category id
			try{
				Mage::getModel("catalog/category")->load( $category_id  )->delete();
				echo $category_id." deleted";
			}catch(Exception $e){
				echo "Delete failed";
			}
		}	
	}
	echo "<pre>";print_r($ids);exit;
	*/
//$fp = fopen('attributes.csv', 'r') or die("can't open file"); //--step1
$fp = fopen('attribute-value.csv', 'r') or die("can't open file"); //--step2
//$fp = fopen('aboutus-cat.csv', 'r') or die("can't open file"); // step3
while ($csv_line = fgetcsv($fp, 1024)) {
    //$attrubuteName = $csv_line[0]; //--step1
// echo $attrubuteName;exit;
   // createAttribute(strtolower($attrubuteName), $attrubuteName, "select"); //--step1
    $attributeType=$csv_line[0]; //--step2
   $aattributeValue=$csv_line[1]; //--step2
   addAttributeOption(strtolower($attributeType),$aattributeValue); //--step2
    //$catName=$csv_line[0]; // step3
   // $isActive=$csv_line[1]; // step3
    //$parentId=5; // step3
    //echo $catName.$parentId;exit;
   // createCategory($catName,$isActive,$parentId); // step3
    
}

function createAttribute($code, $label, $attribute_type) {

    $_attribute_data = array(
        'attribute_code' => $code,
        'is_global' => '1',
        'frontend_input' => $attribute_type, //'boolean',
        'default_value_text' => '',
        'default_value_yesno' => '0',
        'default_value_date' => '',
        'default_value_textarea' => '',
        'is_unique' => '0',
        'is_required' => '0',
        'apply_to' => array('simple', 'grouped', 'configurable', 'bundle', 'downloadable', 'virtual', 'giftcard'),
        'is_configurable' => '0',
        'is_searchable' => '0',
        'is_visible_in_advanced_search' => '0',
        'is_comparable' => '0',
        'is_used_for_price_rules' => '0',
        'is_wysiwyg_enabled' => '0',
        'is_html_allowed_on_front' => '1',
        'is_visible_on_front' => '0',
        'used_in_product_listing' => '0',
        'used_for_sort_by' => '0',
        'frontend_label' => $label
    );
//echo "<pre>";print_r($_attribute_data);exit;
    $model = Mage::getModel('catalog/resource_eav_attribute');
    if (!isset($_attribute_data['is_configurable'])) {
        $_attribute_data['is_configurable'] = 0;
    }
    if (!isset($_attribute_data['is_filterable'])) {
        $_attribute_data['is_filterable'] = 0;
    }
    if (!isset($_attribute_data['is_filterable_in_search'])) {
        $_attribute_data['is_filterable_in_search'] = 0;
    }
    if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
        $_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
    }
    $defaultValueField = $model->getDefaultValueByInput($_attribute_data['frontend_input']);
    if ($defaultValueField) {
        $_attribute_data['default_value'] = $this->getRequest()->getParam($defaultValueField);
    }
    $model->addData($_attribute_data);
    $model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
    $model->setIsUserDefined(1);
    try {
        $model->save();
        echo $model->getId() . "--" . $label."\n";
    } catch (Exception $e) {
        echo "<p>Sorry, error occured while trying to save the attribute. Error: " . $e->getMessage() . "</p>\n";
    }
}
function addAttributeOption($attribute_code, $attribute_value) {
	try{
    $attribute_model = Mage::getModel('eav/entity_attribute');
    $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
   
    $attribute_code = $attribute_model->getIdByCode('catalog_product', $attribute_code);
    $attribute = $attribute_model->load($attribute_code);
   
    $attribute_table = $attribute_options_model->setAttribute($attribute);
    $options = $attribute_options_model->getAllOptions(false);
   
    $value['option'] = array($attribute_value,$attribute_value);
    $result = array('value' => $value);
    $attribute->setData('option',$result);
    $attribute->save();
    echo $attribute_code."saved for-".$attribute_value."\n";
	}catch(Exception $e)
	{
		echo $e->getMessage();continue;
	}
}
function createCategory($catname,$is_active,$parent_id)
{
   
$category = Mage::getModel('catalog/category');
$category->setStoreId(0); // 0 = default/all store view. If you want to save data for a specific store view, replace 0 by Mage::app()->getStore()->getId().

$general['name'] = $catname;
$general['path'] = "1/".$parent_id; // 1/3 is root catalog
$general['description'] = $catname;
$general['meta_title'] = $catname; //Page title
$general['meta_keywords'] = $catname;
$general['meta_description'] = $catname;
$general['landing_page'] = ""; //has to be created in advance, here comes id
$general['display_mode'] = "PRODUCTS_AND_PAGE"; //static block and the products are shown on the page
$general['is_active'] = $is_active;
$general['is_anchor'] = 0;
$general['url_key'] = $catname;//url to be used for this category's page by magento.
//$general['image'] = "cars.jpg";


$category->addData($general);
$category->save();
echo $category->getId()."--".$catname."\n";
    

}