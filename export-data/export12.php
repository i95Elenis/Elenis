<?php

#######################################################################################
#                       *Command's pattern to Run the script*                         #
#=====================================================================================#
#         php export.php 'DB-USERNAME' 'DB-PASSWORD' 'DB-NAME'  'DB-HOST'             # 
#=====================================================================================#
#                                    *For Example*                                    #
#                      $ php export.php 'root' '' 'satchmo' 'localhost'               #
#######################################################################################


print "Start \n";
ini_set('memory_limit', '-1');
error_reporting(0);
ini_set('display_errors', FALSE);

include './csvHandler.php';
include './db.php';

//global $argv;
//echo "<pre>";print_r($argv);
/* if (isset($argv[1]) && isset($argv[2]) && isset($argv[3]) && isset($argv[4])) {

  $username = $argv[1];
  $password = $argv[2];
  $dataBaseName = $argv[3];
  $host = $argv[4];
  print "All parameters Found!\n";
  } else {

  print 'Please Provide Complete Database Connectivity Parameters.' . "\n";
  die('Shuting Down Process !');
  exit;
  }
 */
$username = "dbadmin1";
$password = "895e3f!";
$dataBaseName = "elenis_django";
$host = "localhost";
$exc = '';
try {
    /* echo $host."\n";
      echo $username."\n";
      echo $password."\n";
      echo $dataBaseName."\n";
      exit; */
    $db = new Database($host, $username, $password, $dataBaseName);
} catch (Exception $exc) {

    print 'Could not connect to Database!' . "\n";
    die('Shuting Down Process !');
    exit;
}

print "Fatching Data..............! \n";

/* $q = "SELECT pp.* 

  , GROUP_CONCAT(DISTINCT(IFNULL(pprice.price,'0.0')) SEPARATOR '|') AS prodPrice
  , GROUP_CONCAT(DISTINCT(IFNULL(pprice.id,'0.0')) SEPARATOR '|') AS prodPriceID
  , GROUP_CONCAT(DISTINCT(IFNULL(CONCAT(pprice.key ,':',  pprice.price,':0'),'0.0')) SEPARATOR '|') AS superPriceInfo
  , GROUP_CONCAT(DISTINCT(IFNULL(pc.name,'-')) SEPARATOR ',')  AS prodCatName
  , GROUP_CONCAT(DISTINCT(IFNULL(pc.id,'-')) SEPARATOR ',')  AS prodCatIds
  , GROUP_CONCAT(DISTINCT(IFNULL(ppi.picture,'-')) SEPARATOR '|') AS prodPicutre
  , GROUP_CONCAT(DISTINCT(IFNULL(ccp.product_id,'-')) SEPARATOR '|') AS ConfigProductId
  , GROUP_CONCAT(DISTINCT(IFNULL(ccpog.configurableproduct_id,'-')) SEPARATOR '|') AS configOptionProductId
  , GROUP_CONCAT(DISTINCT(IFNULL(pog.`id`,'-')) SEPARATOR '|') AS prodOptionGroupId
  , GROUP_CONCAT(DISTINCT(IFNULL(pog.`name`,'-')) SEPARATOR '|') AS prodOptionGroupName
  , GROUP_CONCAT(DISTINCT(IFNULL(pog.`description`,'-')) SEPARATOR '|') AS prodOptionGroupDescription

  FROM product_product AS pp

  LEFT JOIN product_product_category AS ppc ON pp.id = ppc.product_id
  LEFT JOIN product_category AS pc ON ppc.category_id  = pc.id
  LEFT JOIN product_productpricelookup AS pprice ON pprice.parentid  = pp.id
  LEFT JOIN product_productimage AS ppi ON ppi.product_id  = pp.id
  LEFT JOIN configurable_configurableproduct AS ccp ON ccp.product_id  = pp.id
  LEFT JOIN configurable_configurableproduct_option_group AS ccpog ON ccpog.configurableproduct_id  = pp.id
  LEFT JOIN product_optiongroup AS pog ON pog.id  = ccpog.optiongroup_id
  LEFT JOIN product_product_related_items AS ppri ON ppri.from_product_id  = pp.id
  LEFT JOIN product_product AS pp2 ON pp2.id  = ppri.to_product_id
  GROUP BY pp.id
  "; */
$q="SELECT  pp.*,epe.pricing_copy,epe.minimum_copy, epe.minimum_quantity AS Min_Qty,epe.lead_time AS Lead_Time,
    pimp.whats_inside_descrip AS whatInside,
    pprice2.price Price,GROUP_CONCAT(DISTINCT(CONCAT('0=',ROUND(pprice1.quantity),'=',pprice1.price)) SEPARATOR '|') AS Tier_prices,

    
    GROUP_CONCAT(DISTINCT(CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order)) SEPARATOR '<>')  AS custom_options



    
    
    , GROUP_CONCAT(DISTINCT(IFNULL(pprice.price,'0.0')) SEPARATOR '|') AS prodPrice 
    , GROUP_CONCAT(DISTINCT(IFNULL(pprice.id,'0.0')) SEPARATOR '|') AS prodPriceID 
    
    , GROUP_CONCAT(DISTINCT(IFNULL(pc.name,'-')) SEPARATOR ',')  AS prodCatName 
    , GROUP_CONCAT(DISTINCT(IFNULL(pc.id,'-')) SEPARATOR ',')  AS prodCatIds 
    , GROUP_CONCAT(DISTINCT(IFNULL(REPLACE(ppi.picture,'images',''),'-')) ) AS prodPicutre
    , GROUP_CONCAT(DISTINCT(IFNULL(REPLACE(epe.thumbnail,'images',''),'-')) SEPARATOR '|') AS thumbnail
    , GROUP_CONCAT(DISTINCT(IFNULL(m.entity_id,'1,2')) SEPARATOR ',') AS category_ids
    
    
    
    
    
    
      FROM product_product AS pp
      
      LEFT JOIN product_product_category AS ppc ON pp.id = ppc.product_id
      LEFT JOIN product_category AS pc ON ppc.category_id  = pc.id
      LEFT JOIN product_productpricelookup AS pprice ON pprice.parentid  = pp.id
      LEFT JOIN product_productimage AS ppi ON ppi.product_id  = pp.id
      LEFT JOIN configurable_configurableproduct AS ccp ON ccp.product_id  = pp.id
      LEFT JOIN configurable_configurableproduct_option_group AS ccpog ON ccpog.configurableproduct_id  = pp.id
      LEFT JOIN product_optiongroup AS pog ON pog.id  = ccpog.optiongroup_id
      LEFT JOIN product_product_related_items AS ppri ON ppri.from_product_id  = pp.id
      LEFT JOIN product_product AS pp2 ON pp2.id  = ppri.to_product_id
      LEFT JOIN custom_customtextfield AS cust ON cust.products_id=pp.id
      LEFT JOIN product_import AS pimp ON pp.sku=pimp.sku
      LEFT JOIN product_price AS pprice1 ON pprice1.product_id=pp.id
      LEFT JOIN product_price AS pprice2 ON pprice2.product_id=pp.id AND (pprice2.quantity =1 OR pprice2.quantity=0)
      LEFT JOIN mage_catalog_category_entity_varchar m ON m.value LIKE CONCAT('%',pc.name,'%')
      LEFT JOIN elenis_product_elenisproduct epe ON epe.product_ptr_id=pp.id
      
      GROUP BY pp.id
       ";
/*$q1 = "SELECT co.parent_id parentId,(select sku from product_product where id=co.parent_id) parentSku,cpo.productvariation_id id,(SELECT sku FROM product_product WHERE id=cpo.productvariation_id) sku,o.name AS option_values1,og.name AS config_attribute1
FROM configurable_productvariation_options cpo INNER JOIN configurable_productvariation co INNER JOIN product_option o INNER JOIN product_optiongroup og INNER JOIN product_product p
WHERE   cpo.productvariation_id IN(1505,1399,512,186,1548,1549,933) AND cpo.productvariation_id=co.product_id AND co.parent_id=p.id AND o.id=cpo.option_id AND o.option_group_id=og.id GROUP BY cpo.id order by cpo.id";
 */
$q1=" SELECT cpo.productvariation_id id,(SELECT sku FROM product_product WHERE id=cpo.productvariation_id) sku,o.name AS option_values1,og.name AS config_attribute1
FROM configurable_productvariation_options cpo INNER JOIN configurable_productvariation co INNER JOIN product_option o INNER JOIN product_optiongroup og INNER JOIN product_product p
WHERE co.product_id=p.id and  cpo.productvariation_id=p.id AND o.id=cpo.option_id AND o.option_group_id=og.id GROUP BY cpo.id order by cpo.id";
$q2 = "select co.customproduct_id id,og.name config_attribute2,
    GROUP_CONCAT(DISTINCT(IFNULL(o.name,'EMPTY')) SEPARATOR ',') AS option_values2 
    from custom_customproduct_option_group co 
    inner join product_option o 
    inner join product_product p 
    inner JOIN product_optiongroup og 
    where  co.customproduct_id=p.id and co.optiongroup_id=o.option_group_id and og.id=optiongroup_id and o.option_group_id=og.id group by co.id ORDER BY co.customproduct_id
";
/* $q1 = "SELECT   p.id,(IF(og.name = 'Accent',o.name , NULL)) AS 'Accent',
  (IF(og.name = 'Age', o.name, NULL)) AS 'Age',
  (IF(og.name = 'Color', o.name, NULL)) AS 'Color',
  (IF(og.name = 'Cupcake Flavor', o.name, NULL)) AS 'Cupcake Flavor',
  (IF(og.name = 'Edible Ink Colors', o.name, NULL)) AS 'Edible Ink Colors',
  (IF(og.name = 'Font', o.name, NULL)) AS 'Font',
  (IF(og.name = 'Font Color', o.name, NULL)) AS 'Font Color',
  (IF(og.name = 'Frame', o.name, NULL)) AS 'Frame',
  (IF(og.name = 'Frosting', o.name, NULL)) AS 'Frosting',
  (IF(og.name = 'Hair Color', o.name, NULL)) AS 'Hair Color',
  (IF(og.name = 'Skin Color', o.name, NULL)) AS 'Skin Color',
  (IF(og.name = 'Sport', o.name, NULL)) AS 'Sport',
  (IF(og.name = 'Sprinkles', o.name, NULL)) AS 'Sprinkles',
  (IF(og.name = 'Text Color', o.name, NULL)) AS 'Text Color'


  FROM custom_customproduct_option_group c INNER JOIN product_product p INNER JOIN
  product_option o INNER JOIN product_optiongroup og WHERE c.customproduct_id=p.id AND o.option_group_id=c.optiongroup_id AND og.id=o.option_group_id
  AND og.id=c.optiongroup_id";
  //echo "query1".$q1."<br/>";
  $q2 = "SELECT  co.product_id as id,(IF(og.name = 'Flavor', o.name, NULL)) AS 'Flavor',
  (IF(og.name = 'Flower candle colors', o.name, NULL)) AS 'Flower candle colors',
  (IF(og.name = 'Gender', o.name, NULL)) AS 'Gender',
  (IF(og.name = 'Hair Color', o.name, NULL)) AS 'Hair Color',
  (IF(og.name = 'Heel Color', o.name, NULL)) AS 'Heel Color',
  (IF(og.name = 'I''m a...', o.name, NULL)) AS 'I''m a...',
  (IF(og.name = 'Icing Color', o.name, NULL)) AS 'Icing Color',
  (IF(og.name = 'Number of Letters', o.name, NULL)) AS 'Number of Letters',
  (IF(og.name = 'Political Party', o.name, NULL)) AS 'Political Party',
  (IF(og.name = 'Quantity', o.name, NULL)) AS 'Quantity',
  (IF(og.name = 'Shape', o.name, NULL)) AS 'Shape',
  (IF(og.name = 'Sibling', o.name, NULL)) AS 'Sibling',
  (IF(og.name = 'Sigil', o.name, NULL)) AS 'Sigil',
  (IF(og.name = 'Size', o.name, NULL)) AS 'Size',
  (IF(og.name = 'Shape', o.name, NULL)) AS 'Shape',
  (IF(og.name = 'Style', o.name, NULL)) AS 'Style',
  (IF(og.name = 'Super Bowl Team', o.name, NULL)) AS 'Super Bowl Team',
  (IF(og.name = 'Type', o.name, NULL)) AS 'Type',
  (IF(og.name = 'Will you be my..', o.name, NULL)) AS  'Will you be my..' FROM configurable_productvariation_options cpo INNER JOIN configurable_productvariation co
  INNER JOIN product_product p INNER JOIN product_option o INNER JOIN product_optiongroup og WHERE cpo.productvariation_id=co.product_id  AND cpo.option_id=o.id AND o.option_group_id=og.id AND co.product_id=p.id
  "; */
/* $q3="SELECT  pp.id,pp.sku, (IF(cust.name='15 character maximum',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS  '15 character maximum:field:0:0'
  ,(IF(cust.name='15 Characters or less',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS '15 Characters or less:field:0:0'
  ,(IF(cust.name='15 characters or lest',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS '15 characters or lest:field:0:0'
  ,(IF(cust.name='30 characters or less',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS '30 characters or less:field:0:0'
  ,(IF(cust.name='Birth Date',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Birth Date:field:0:0'
  ,(IF(cust.name='Color 1',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Color 1:field:0:0'
  ,(IF(cust.name='Color 2',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Color 2:field:0:0'
  ,(IF(cust.name='Color One',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Color One:field:0:0'
  ,(IF(cust.name='Color Two',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Color Two:field:0:0'
  ,(IF(cust.name='Date',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Date:field:0:0'
  ,(IF(cust.name='Enter Name Here',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Enter Name Here:field:0:0'
  ,(IF(cust.name='Enter up to two characters below',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Enter up to two characters below:field:0:0'
  ,(IF(cust.name='Image',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Image:field:0:0'
  ,(IF(cust.name='Initials',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Initials:field:0:0'
  ,(IF(cust.name='Love Message 1',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Love Message 1:field:0:0'
  ,(IF(cust.name='Love Message 2',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Love Message 2:field:0:0'
  ,(IF(cust.name='Love Message 3',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Love Message 3:field:0:0'
  ,(IF(cust.name='Message (15 characters or less)',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Message (15 characters or less):field:0:0'
  ,(IF(cust.name='Message: 15 Characters or Less',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Message: 15 Characters or Less:field:0:0'
  ,(IF(cust.name='Mongram letters',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Mongram letters:field:0:0'
  ,(IF(cust.name='Name',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Name:field:0:0'
  ,(IF(cust.name='Name (15 characters or less)',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Name (15 characters or less):field:0:0'
  ,(IF(cust.name='Name 1 (7 character maximum)',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Name (15 characters or less):field:0:0'
  ,(IF(cust.name='Name 2 (7 character maximum)',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Name 2 (7 character maximum):field:0:0'
  ,(IF(cust.name='Name or Message (15 characters or less)',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Name or Message (15 characters or less):field:0:0'
  ,(IF(cust.name='Names',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Names:field:0:0'
  ,(IF(cust.name='Numbers',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Numbers:field:0:0'
  ,(IF(cust.name='Size',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Size:field:0:0'
  ,(IF(cust.name='Weight (in lb''s and oz''s)',CONCAT(cust.name,':','fixed',':',cust.price_change,':',pp.sku,':',cust.sort_order), NULL)) AS 'Weight (in lb''s and oz''s):field:0:0' FROM product_product AS pp
  inner JOIN custom_customtextfield AS cust where cust.products_id=pp.id order by pp.id
  "; */
//echo "query2". $q2."<br/>";exit;
$r = $db->query($q);
$r1 = $db->query($q1);
$r2 = $db->query($q2);
//$r3=$db->query($q3);
//echo "<pre>";print_r($r1);exit;
$result = $db->toArray($r);
$result1 = $db->toArray($r1);
$result2 = $db->toArray($r2);
//$result3 = $db->toArray($r3);
//echo "<pre>";print_r($result1['sku']);exit;
//$db->in_array_recursive(1, $result1);
/* echo "<pre>";
  print_r($db->in_array_recursive(1, $result1));
  exit;
 */

//echo "<pre>";print_r($result2);exit;

if (count($result) <= 0) {

    throw new ErrorException("No records Found." . "\n");
    die('Shuting Down Process !');
    exit;
}

print " Records Found! \n";
$base_path = dirname(__FILE__) . '/';
$ExportArray = array();
$productArray=array();

$csvHandler = new csvHandler($base_path . '/');
$file_name = 'Exported-' . date('dmY_His') . '.csv';
$intial_dicrectory = 'exports/';

/* $attributes = array('name', 'Accent', 'Accessories', 'Age', 'Assorted set of', 'Bear', 'Candidate', 'Choose your Game', 'Color', 'Color Scheme', 'Edible Ink Colors',
  'Flavor', 'Flower candle colors', 'Font', 'Font_Color', 'Frame', 'Frosting', 'Gender', 'Hair Color', 'Heel Color', 'Icing Color', 'Im_a', 'Number of Letters', 'i\'am',
  'Option Group', 'Pen Color', 'Pen_Pack', 'Political Party', 'Quantity', 'Shape', 'Sibling', 'Sigil', 'Size', 'Skin Color', 'Sport', 'Style', 'Super Bowl Team',
  'Text Color', 'Will you be my', 'With', 'cupcake flavor', 'Indicates the flavor of cupcake');
 */
print "Readying Records for CSV file!";
print ".......................... \n";

$i = 0;
foreach ($result as $singleResult) {

    //  echo "kk".$i;exit;
    $ExportArray[$i]["product_id"] = $singleResult['id'];
    
    $ExportArray[$i]["vw_minimum_copy"] = $singleResult['minimum_copy'];
    $ExportArray[$i]["vw_pricing_copy"] = $singleResult['pricing_copy'];
    
    
    
    $ExportArray[$i]["store"] = 'admin'; #STATIC
    $ExportArray[$i]["websites"] = 'base'; #STATIC
    $ExportArray[$i]["attribute_set"] = 'Default'; #STATIC
    //$ExportArray[$i]["Type"] = ($singleResult['ConfigProductId'] != '-' && $singleResult['ConfigProductId']) ? 'configurable ' : 'simple';
    $ExportArray[$i]["type"] = 'simple';
    $ExportArray[$i]["category_ids_sample"] = $singleResult['prodCatIds'];
    $ExportArray[$i]["categorys_name"] = $singleResult['prodCatName'];
    $ExportArray[$i]["category_ids"] = (string)$singleResult['category_ids'];
    $ExportArray[$i]["sku"] = $singleResult['sku'];
    $ExportArray[$i]["weight"] = ($singleResult['weight']!="")?$singleResult['weight']:0;


    #Product information
    //$verifyId=$db->in_array_recursive($singleResult['id'], $result1);
    /*   foreach ($result1 as $list) {

      if($singleResult['id']===$list['id'])
      {
      echo $list['id'].$singleResult['id'];exit;
      }
      }
     */




    /* $rows=$db->in_array_search($singleResult['id'], $result1);
      echo "<pre>";print_r($rows);
      foreach($rows as $key=>$list)
      {
      if($key!='id' )
      {

      $ExportArray[$i][strtolower($key)]=$list;
      }
      }
     */

    //$ExportArray[$i]["price"] = $singleResult['prodPrice'];
    $ExportArray[$i]["price"] = ($singleResult['Price'])?$singleResult['Price']:0;
    $ExportArray[$i]["meta_keyword"] = ($singleResult['meta']!="")?($singleResult['meta']):($singleResult['name']);
    $ExportArray[$i]["meta_title"] = trim($singleResult['name']);
    $ExportArray[$i]["meta_description"] = ($singleResult['description']!="")?($singleResult['description']):($singleResult['name']);
    $ExportArray[$i]["name"] = trim($singleResult['name']);
    //  print_r($singleResult['id']);exit;
    //echo "kk".$verifyId;exit;
    // $ExportArray[$i]["productID"] = ;
//    $ExportArray[$i]["meta_title"] = $singleResult['name'];
    //$ExportArray[$i]["Priceid"] = $singleResult['prodPriceID'];
    // $ExportArray[$i]["Super_Attribute_Pricing"] = $singleResult['superPriceInfo'];
    # Images insertion 
    $ExportArray[$i]["image"] = ($singleResult['prodPicutre']!="")?($singleResult['prodPicutre']):($singleResult['thumbnail']);
    $ImageArray = explode('|', $singleResult['prodPicutre']);

    $tumbnailImagesArr = array();
    $SmallImagesArr = array();
    foreach ($ImageArray as $SingleImage) {
        $thumbnailArray = explode('.', $SingleImage);
        $lastIndex = count($thumbnailArray) - 1;
        $tumbnailImages = '';
        $SmallImages = '';
        foreach ($thumbnailArray as $key => $thumb) {
            if (($lastIndex) == $key) {
                $tumbnailImages.= '.' . $thumb;
                $SmallImages.='.' . $thumb;
            } else {
                $tumbnailImages.=$thumb;
                $SmallImages.=$thumb;
            }
        }

        $tumbnailImagesArr[] = $tumbnailImages;
        $SmallImagesArr[] = $SmallImages;
    }


    //$ExportArray[$i]["thumbnail"] = implode(',', $tumbnailImagesArr); #*-thumb.*
    $ExportArray[$i]["thumbnail"] = ($singleResult['thumbnail']!="")?($singleResult['thumbnail']):implode(',', $tumbnailImagesArr);
    $ExportArray[$i]["small_image"] = ($singleResult['thumbnail']!="")?($singleResult['thumbnail']):implode(',', $SmallImagesArr);
    //$ExportArray[$i]["gallery"] = $singleResult['prodPicutre'];
   
    $ExportArray[$i]["short_description"] = ($singleResult['description']!="")?($singleResult['description']):($singleResult['name']);
    $ExportArray[$i]["description"] = ($singleResult['whatInside']!="")?($singleResult['whatInside']):($singleResult['name']);
    $ExportArray[$i]["status"] = trim(($singleResult['active'] == '1') ? 'Enabled' : 'Disabled');
    //$ExportArray[$i]["Option Group"] = $singleResult['prodOptionGroupId'];
    //Nowhere


    $ExportArray[$i]["visibility"] = 'Catalog, Search';
    //$ExportArray[$i]["visibility"] = 'Nowhere';
    if ($singleResult['taxClass_id'] == 1) {
        $ExportArray[$i]["tax_class_id"] = "Taxable Goods";
    
    } else {
        $ExportArray[$i]["tax_class_id"] = "None";
    }
    $ExportArray[$i]["qty"] = ($singleResult['items_in_stock']>0)?$singleResult['items_in_stock']:1;
    $ExportArray[$i]["is_in_stock"] = 1;
    $ExportArray[$i]["options_container"] = trim("Product Info Column");
    $ExportArray[$i]["associated"] = "";
    //$ExportArray[$i]['config_attributes'] = $singleResult['prodOptionGroupName'];
    $ExportArray[$i]['minqty'] = ($singleResult['Min_Qty'] != NULL) ? $singleResult['Min_Qty'] : 0;
    $ExportArray[$i]['lead_time'] = trim($singleResult['Lead_Time']);
    $ExportArray[$i]['tier_prices'] = trim($singleResult['Tier_prices']);
    //$ExportArray[$i]['whatInside']=$
    $ExportArray[$i]['custom_options'] = $singleResult['custom_options'];
    //echo "<pre>";print_r($result1);exit;
    $valuesArray = array();
    foreach ($result1 as $array) {
        $valuesArray[] = trim(strtolower($array['config_attribute1']));
        $valuesArray = array_unique(str_replace(" ", "_", $valuesArray));
    }
    $ExportArray[$i]['config_attributes1'] = NULL;
   // echo "<pre>";print_r($valuesArray);exit;
    foreach ($valuesArray as $attribute) {

        $ExportArray[$i][$attribute] = NULL;
    }
//echo "<pre>";print_r($result1);exit;
      $parentIds[]=array();
     $sku="";$super1="";
    foreach ($result1 as $array) {
        //echo $array['id'];
        //echo "not matched".$array['parentId'].$singleResult['id']."<br/>";
        /*if($array['parentId'] == $singleResult['id'])
        {
       
        $sku.=$array['sku'].",";
        $parentIds['id']=$array['parentId'];
        
        }
        
        $parentIds['sku']=$sku;
         */
        $q7="SELECT parent_id,(SELECT sku FROM product_product WHERE parent_id=id) sku FROM configurable_productvariation WHERE product_id =".$array['id'];
         //echo $q7."<br/>";  
            $r7 = $db->query($q7);
            $result7 = $db->toArray($r7);
            $sku.=$array['sku'].",";
            
         
        // $parentIds['sku'][]=$sku."<>".$result7[0]['parent_id']."<>".$result7[0]['sku'];
        // $parentIds['sku'][]=$sku."<>".$result7[0]['parent_id'];
         $parentIds['id'][]=$result7[0]['parent_id'];
         //$parentIds['id'][]=
            
        //echo "not matched".$array['id'].$singleResult['id']."<br/>";
        if ($array['id'] == $singleResult['id']) {
            //echo "matched".$array['id'].$singleResult['id']."<br/>";
            $q4="SELECT parent_id,(SELECT sku FROM product_product WHERE parent_id=id) sku FROM configurable_productvariation WHERE product_id =".$array['id'];
            
            $r4 = $db->query($q4);
            $result4 = $db->toArray($r4);
            $sku.=$array['sku'].",";
            if($result4[0]['parent_id']!="")
            {
                //$parentIds['id'][]=$result4[0]['parent_id'];
                $parentIds['id'][]=$result4[0]['sku'];
               
            }
           
            // echo "matched".$array['parentId'].$singleResult['id']."<br/>";
            //$q4="SELECT co.parent_id,(SELECT sku FROM product_product WHERE id=co.parent_id) sku FROM product_product p INNER JOIN configurable_productvariation co WHERE co.product_id=p.id  AND p.id=".$array['id'];
            //$r4 = $db->query($q4);
            //$result4 = $db->toArray($r4);
            //$result4[0][sku]
            //$result4[0][parent_id]
            //echo "<pre>";print_r($result4);exit;
            
            //echo $array['id'].$singleResult['id']."==".$i;
            $configOption=trim(strtolower($array['config_attribute1']));
           
             $configAttribute=str_replace(" ", "_",$configOption);
           
            //echo $array['id'].$singleResult['id'];
            // echo "<pre>";print_r($array);exit;
            //$ExportArray[$i]['config_attributes1'] = $array['config_attribute1'];
           
            $ExportArray[$i]['config_attributes1'] = $configAttribute;
            
            if (in_array($configAttribute, $valuesArray)) {
                $ExportArray[$i][$configAttribute] = $array['option_values1'];
                $super1.=$array['option_values1'].":".$ExportArray[$i]['price'].":0|";
                
            }
            
            
            break;
         
        }
        
       //echo "<pre>";print_r($parentIds);
       }
    
   //echo "<pre>";print_r($ExportArray[$i]);
    //echo "<pre>";print_r($parentIds);exit;
    /* if($singleResult['id']==337){
      echo "<pre>";print_r($ExportArray[$i]);
      exit;

      } */
    # Attributes insertion 
    /* $attributes_applied = explode('|', $singleResult['prodOptionGroupName']);
      $attributes_applied_description = explode('|', $singleResult['prodOptionGroupDescription']);
      foreach ($attributes as $singleAttributes) {
      $ExportArray[$i][strtolower($singleAttributes)] = 'Not Applicable';
      }
      foreach ($attributes_applied as $key => $singleattr) {
      $ExportArray[$i][strtolower($singleattr)] = $attributes_applied_description[$key];
      }
     */
    /* $customOptions=$db->in_array_search($singleResult['id'], $result3); 
      echo "<pre>";print_r($customOptions);
      foreach($customOptions as $key1=>$list1)
      {
      if($key1!='id')
      {

      $ExportArray[$i][strtolower($key1)]=$list1;
      }
      }
     */
    /*   if($i==174)
      {

      }
     */
    /*  if($i==242)
      {
      echo "<pre>";print count($ExportArray[$i]);print_r($ExportArray[$i]);exit;
      }
     */
    
    $valuesArray1 = array();$ids=array();
    foreach ($result2 as $array1) {
        $valuesArray1[] = trim(strtolower($array1['config_attribute2']));
     //   $ids[]=$array1['id'];
        $valuesArray1 = array_unique(str_replace(" ", "_",$valuesArray1));
    }
   /* $cnt_array = array_count_values($ids);
    foreach($cnt_array as $key=>$val){
   if($val != 1){
          $res[] = $key;
   }
}
*/
//echo "<pre>";print_r($res);exit;

    //echo "<pre>";print_r($ids);exit;
    //echo "<pre>";print_r($valuesArray1);exit;
    $ExportArray[$i]['config_attributes2'] = NULL;
    foreach ($valuesArray1 as $attribute1) {

        $ExportArray[$i][$attribute1] = NULL;
    }
//echo "<pre>";print_r($result2);exit;
    //array_unqiue(array_filter($parentIds));
   $productIds=array_filter($parentIds);
    
    $j=1;
    foreach ($result2 as $array2) {
       // echo "<pre>";print_r($array2);
      //  echo count($array2['id'] );
     /* if($array2['id']=='11')
      {
         if(in_array($array2['id'],$res))
         {
             echo "gh".$array2['id'];exit;
         }
      }
      */
       
        if ($array2['id'] == $singleResult['id'] ) {
          // echo  $array2['id']." == ". $singleResult['id'] ;
        // if(in_array($singleResult['id'],$ids)){   
         //  echo $array2['id']."==". $singleResult['id'] ."<br/>";
           
           $options=array();
         
           //  echo "id".$array2['id'];
           $options=explode(",",$array2['option_values2']);
         //  echo "<pre>".$array2['id']."=".$singleResult['id'];print_r($options)."<br/>";
         
            

           //echo "<pre>";print_r($options);exit;
           $sku="";$twoString=array(" ","/");$matchedValues="";$allConfig=array();
            foreach($options as $values)
            {
              // echo "<pre>";print_r($options);
                $parent=$i;
               $parentSku= explode("_",$ExportArray[$parent]['sku']);
               //$parId=explode("_",$ExportArray[$parent]['product_id']);
               // echo "values".$values."\t";
                $configOption=trim(strtolower($array2['config_attribute2']));
             $configAttribute=str_replace(" ", "_",$configOption);
             $allConfig[]=$configAttribute;
              //  $configAttribute=str_replace(" ", "_",strtolower());
               //echo $configAttribute."\t";
                  $i++;$j++;
                   $ExportArray[$i]=$ExportArray[$parent];
                   $matchedValues=$values;
                   $matchedValues=str_replace($twoString, "_",strtolower($matchedValues));
                  // $matchNum=$ExportArray[$parent]['product_id']."_";
                   //$matchNum1=str_replace("_", "",$j);
                   $actualNum=$ExportArray[$parent]['product_id']."_".$j;
                   $numberData= str_replace("_","",$actualNum);
                   //echo $numberData."<br/>";
                   //echo $matchedValues."<br/>";exit;
                  // echo str_replace($twoString, "_",strtolower($matchedValues))."<br/>";
               // $ExportArray[$i]['sku']=$ExportArray[$parent]['sku']."_".  str_replace(" ", "_",strtolower($values));
                    $ExportArray[$i]['sku']=$parentSku[0]."_".$matchedValues;
                    $ExportArray[$i]['product_id']=$numberData;
                   //  $ExportArray[$i]['product_id']=  $ExportArray[$parent]['product_id'].$actualNum;
                  //  $ExportArray[$i]['product_id']=  ;
                $sku.=$ExportArray[$i]['sku'].",";
                $ExportArray[$i]['weight']=($ExportArray[$parent]['weight']?$ExportArray[$parent]['weight']:0);
                $ExportArray[$i]['price']=($ExportArray[$parent]['price']?$ExportArray[$parent]['price']:0);
                 $ExportArray[$i]["visibility"] = 'Nowhere';
                  $ExportArray[$i]["type"]="simple";
                //$ExportArray[$i]['sku']=$ExportArray[$parent]['sku']."_".  $configAttribute;
               //$ExportArray[$parent]['associated']=$sku;
               // $ExportArray[$parent]['type']='configurable';
               // echo "\t".$array2['config_attribute2']."\t".strtolower($array2['config_attribute2']);
                //$ExportArray[$i]['config_attributes2'] = $array2['config_attribute2'];
                //$ExportArray[$i]['config_attributes2'] = $configAttribute;
                 $allCon=array_unique($allConfig);
                 //echo;
                 
                 $ExportArray[$i]['config_attributes2'] =implode(",",$allCon) ;
                

             if (in_array($configAttribute, $valuesArray1)) {
                // echo $ExportArray[$i]['sku']."===".$values."<br/>";
                $ExportArray[$i][$configAttribute] = $values;
                $super.=$values.":".$ExportArray[$i]['price'].":0|";
                
            }
            //$ExportArray[$i]['super_attribute_pricing']=$super;
            //echo  "<pre>main";print_r($ExportArray[$i]);
                }
               
        $skus['id'][]=$array2['id'];
        $skus['sku'][]=$sku;
        $skus['config2'][]=$configAttribute;
        $skus['super'][]=$super;
                
   //   break;   
      
           
        }
        
      
    }
  //  exit;
   // exit;
  //exit; 
  
 /*  foreach($productIds as $ids)
 {
      // echo $ids.$ExportArray[$i]['sku']."<br/>";
       if($ExportArray[$i]['sku'],$ids)==0)
    //if($ids==$ExportArray[$i]['sku'])
     {
           //echo "jj".$ids.$ExportArray[$i]['sku'];
         $q5="SELECT GROUP_CONCAT(DISTINCT(SELECT DISTINCT sku FROM product_product WHERE id=product_id)) sku,GROUP_CONCAT(DISTINCT(REPLACE(og.name, ' ','_'))) config1 FROM configurable_productvariation co INNER JOIN configurable_productvariation_options cpo INNER JOIN product_option o INNER JOIN product_optiongroup og WHERE
parent_id=(SELECT id FROM product_product WHERE sku LIKE ' ".$ExportArray[$i]['sku']." ') AND o.id=cpo.option_id AND o.option_group_id=og.id AND cpo.productvariation_id=co.product_id;";
            $r5 = $db->query($q5);
            $result5 = $db->toArray($r5);
         //  echo "<pre>".$ids.$productArray['product_id'];print_r($result5);
           // $datavalues[]=$productArray;
            $ExportArray[$i]['visibility'] = 'Catalog, Search';
            $ExportArray[$i]['associated']=$result5[0]['sku'];
            $ExportArray[$i]['type']='configurable';
            $ExportArray[$i]['config_attributes1']=strtolower($result5[0]['config1']);
            
     }   
 }
   for($i=0;$i<=count($skus['id']);$i++)
{
       if(strcmp($skus['sku'][$i],$ExportArray[$i]['sku'])==0)
    //if($ExportArray[$i]['sku']==$skus['sku'][$i])
    {
        $associate=$ExportArray[$i]['associated'].",";
        $config=$ExportArray[$i]['config_attributes2'].",";
        $ExportArray[$i]['type']='configurable';
        $ExportArray[$i]['associated']=$skus['sku'][$i].$associate;
        $ExportArray[$i]['config_attributes2']=trim($skus['config2'][$i].",".$config);
        $ExportArray[$i]["visibility"] = 'Catalog, Search';
    }
}*/
//echo "<pre>";print_r($ExportArray[$i]);   
    $i++;
//}


/*foreach($ExportArray as $productArray)
{
   // echo "<pre>";print_r($productIds);
//echo "<pre>";print_r($skus);
//echo $productArray['product_id'];
 foreach($productIds as $ids)
 {
     if($ids==$productArray['product_id'])
     {
         $q5="SELECT GROUP_CONCAT(DISTINCT(SELECT DISTINCT sku FROM product_product WHERE id=product_id)) sku,GROUP_CONCAT(DISTINCT(REPLACE(og.name, ' ','_'))) config1 FROM configurable_productvariation co INNER JOIN configurable_productvariation_options cpo INNER JOIN product_option o INNER JOIN product_optiongroup og WHERE
parent_id=".$productArray['product_id']." AND o.id=cpo.option_id AND o.option_group_id=og.id AND cpo.productvariation_id=co.product_id;";
            $r5 = $db->query($q5);
            $result5 = $db->toArray($r5);
           // echo "<pre>".$ids.$productArray['product_id'];print_r($result5);
           // $datavalues[]=$productArray;
            $productArray["visibility"] = 'Catalog, Search';
            $productArray['associated']=$result5[0]['sku'];
            $productArray['type']='configurable';
            $productArray['config_attributes1']=strtolower($result5[0]['config1']);
            
     }
 }   
/*if(in_array($productArray['product_id'],$productIds)){
     $q5="SELECT GROUP_CONCAT(DISTINCT(SELECT DISTINCT sku FROM product_product WHERE id=product_id)) sku,GROUP_CONCAT(DISTINCT(REPLACE(og.name, ' ','_'))) config1 FROM configurable_productvariation co INNER JOIN configurable_productvariation_options cpo INNER JOIN product_option o INNER JOIN product_optiongroup og WHERE
parent_id=".$productArray['product_id']." AND o.id=cpo.option_id AND o.option_group_id=og.id AND cpo.productvariation_id=co.product_id;";
            $r5 = $db->query($q5);
            $result5 = $db->toArray($r5);
            $productArray['associated']=$result5['sku'];
            $productArray['type']='configurable';
            $productArray['config_attributes1']=$result5['config1'];
            
}*/
/*for($i=0;$i<=count($skus['id']);$i++)
{
    if($productArray['product_id']==$skus['id'][$i])
    {
        $associate=$productArray['associated'].",";
        $config=$productArray['config_attributes2'].",";
        $productArray['associated']=$skus['sku'][$i].$associate;
        $productArray['config_attributes2']=trim($skus['config2'][$i].",".$config);
        $productArray["visibility"] = 'Catalog, Search';
    }
}
  
/*if(in_array($productArray['product_id'],$productIds)){
    
      
    $q5="SELECT GROUP_CONCAT(DISTINCT(SELECT DISTINCT sku FROM product_product WHERE id=product_id)) sku,GROUP_CONCAT(DISTINCT(og.name)) config1 FROM configurable_productvariation co INNER JOIN configurable_productvariation_options cpo INNER JOIN product_option o INNER JOIN product_optiongroup og WHERE
parent_id=".$productArray['product_id']." AND o.id=cpo.option_id AND o.option_group_id=og.id AND cpo.productvariation_id=co.product_id;";
            $r5 = $db->query($q5);
            $result5 = $db->toArray($r5);
             
}*/
 //echo "<pre>";print_r($productArray);   
}
//echo "<pre>";print_r($ExportArray);exit;
foreach($ExportArray as $key=> $productArray)
{
    //echo $key."kjh";
    
    $productId=array_unique($productIds);
    
     // echo "<pre>";print_r($productId);
    
    foreach($productId as $ids)
 {
    //"-".$productArray['sku'];  
    //echo "<pre>";print_r();
    //$pId=end($productId);
    
   //$allId=explode("<>",$pId);
    

     
    //echo "<pre>";print_r($allId);
    //echo $pId.$productArray['id'];exit;
    //echo $allId[1]."--".$productArray['id'];
     
     if($ids===$productArray['sku'])
     {
        
         //echo "j".$ids.$productArray['sku'];
         $q5="SELECT GROUP_CONCAT(DISTINCT(SELECT DISTINCT sku FROM product_product WHERE id=product_id)) sku,GROUP_CONCAT(DISTINCT(REPLACE(og.name, ' ','_'))) config1 FROM configurable_productvariation co INNER JOIN configurable_productvariation_options cpo INNER JOIN product_option o INNER JOIN product_optiongroup og WHERE
parent_id in(SELECT id FROM product_product WHERE sku LIKE '%".$productArray['sku']."%') AND o.id=cpo.option_id AND o.option_group_id=og.id AND cpo.productvariation_id=co.product_id;";
        
            $r5 = $db->query($q5);
            $result5 = $db->toArray($r5);
            //echo $q5."<br/>";
           // echo "<pre>".$ids.$productArray['product_id'];print_r($result5);
           // $datavalues[]=$productArray;
            if($result5[0]['sku']!="" && $result5[0]['config1']!="")
            {
                $ExportArray[$key]["visibility"] = 'Catalog, Search';
                $ExportArray[$key]['associated']=$result5[0]['sku'];
                $ExportArray[$key]['type']='configurable';
                $ExportArray[$key]['config_attributes1']=strtolower($result5[0]['config1']);
            }
            
     }
     
     
     
 }

//exit;
     
     

  // echo "<pre>";print_r($skus);exit;
   
 
//echo "<pre>";print_r($productArray);
}
//exit;
$associateSku="";$superAtt="";
//echo "<pre>";print_r($skus);exit;
foreach($ExportArray as $key1=>$productArray1)
{   $superAtt=$ExportArray[$key1]['super_attribute_pricing'];
    for($i=0;$i<=count($skus['id']);$i++)
    {

        $q6="SELECT sku FROM product_product WHERE id= ".(int)$skus['id'][$i];
        //echo $q6."<br/>";
        $r6 = $db->query($q6);
            $result6 = $db->toArray($r6);
        // echo $result6[0][sku]."-".$productArray1['sku']."<br/>";
         
        if($result6[0][sku]===$productArray1['sku'])
        //if($ExportArray[$i]['sku']==$skus['sku'][$i])
        {
            //$tempArr[]=$skus['config2'][$i];
            
            $associate=$productArray1['associated'].",";
            $associateSku.=$skus['sku'][$i];
           // echo $associate. $associateSku."<br/>";
            $configSku[]=array_unique($skus['config2'][$i]);
            $config[]=$productArray1['config_attributes2'].$skus['config2'][$i];
           // echo $config;
           // echo "<pre>";print_r($config);echo implode(",",$config);
           $ExportArray[$key1]['type']='configurable';
            
            $ExportArray[$key1]['associated']=$associateSku.$associate;
            $ExportArray[$key1]['config_attributes2']=trim(implode(",",$config));
            $arr = explode( "," , $ExportArray[$key1]['config_attributes2'] );
            $arr = array_unique( $arr );
            $string = implode("," , $arr);
            
            $ExportArray[$key1]['config_attributes2']=$string;
            //echo "<pre>";print_r($string);
            $ExportArray[$key1]['super_attribute_pricing']=$superAtt.$skus['super'][$i];
            //$ExportArray[$key1]["visibility"] = 'Catalog, Search';
        }
    }
    
    //echo "<pre>";print_r($productArray1);
}
//exit;
//echo "<pre>";print_r($ExportArray);
//exit;
//echo "<pre>";print_r($ExportArray);exit;
//exit;
#----------------------------------------------------------------------------
# Key Based 2D Array Sorting
# $ExportArray = csvHandler::aasort($ExportArray, 'Super_Attribute_Pricing');
#----------------------------------------------------------------------------
# Creating directory if dose not exist
if (!is_dir(str_replace('/', '', $intial_dicrectory))) {
    mkdir($base_path . $intial_dicrectory, 0777, TRUE);
}

print "Creating CSV File !\n";
$file_name_with_directory = $intial_dicrectory . $file_name;
//$csvHandler->CreateCSV($ExportArray, $file_name_with_directory);
$csvHandler->CreateCSV($ExportArray, $file_name_with_directory);



print "File Successfully Downloaded At $base_path$file_name_with_directory" . "\n \n";
print "End \n";
exit;


// If Accessing from Browser
//$csvHandler->DownloadCSV($file_name_with_directory, $file_name);
//
//Delete File from server after downloading File
//$csvHandler->DeleteFile($file_name_with_directory);
