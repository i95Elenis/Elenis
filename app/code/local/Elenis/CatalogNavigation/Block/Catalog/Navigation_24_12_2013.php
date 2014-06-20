<?php

class Elenis_CatalogNavigation_Block_Catalog_Navigation extends Mage_Catalog_Block_Navigation {

    public function sortChildCategories($cats) {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $table = $resource->getTableName('catalog/category');
        $qry = "select entity_id FROM {$table} WHERE entity_id IN({$cats}) ORDER BY POSITION; "; //query           
        $res = $read->fetchAll($qry); //get array
        foreach ($res as $result) {
            $catArray[] = $result['entity_id'];
        }
        return $catArray;
    }

    public function get_categories($categories, $crcat, $i) {

        $excludeNavCategories = explode(",", Mage::getStoreConfig('elenissec/elenisgrp/elenisexcludenav'));
        // echo "<pre>";print_r($excludeNavCategories);exit;
        /*   if (in_array($categories->getId(), $excludeNavCategories)) {
          echo "Got Irix";
          }
          exit;
         */

        //if ($categories->getId() == Mage::getStoreConfig('elenissec/elenisgrp/elenisexcludenav')) {
        if (in_array($categories->getId(), $excludeNavCategories)) {
            $string.= "<div class='block-content'><dl id='narrow-by-list'><dt><b>" . $categories->getName() . "</b></dt>";
        }

        // $childrenIds = $categories->getChildren();
        $childrenIds = $this->sortChildCategories($categories->getChildren());


        // $childrens = explode(",", $childrenIds);
        $childrens = $childrenIds;


        // echo "gere--".$categories->getId();
        //echo "<pre>";print_R(count($childrens));

        foreach ($childrens as $category) {

            $cat = Mage::getModel('catalog/category')->load($category);


            if ($cat->hasChildren() == "") {

                if ($crcat == $cat->getId()) {

                    $string.= '<a href="' . $cat->getURL() . '"><div class="selectcat"></div><dt>' . $cat->getName() . '</dt></a>';
                } else {
                    //  echo $cat->getId()."-".$cat->getParentId()."-".$category."<br/>";
                    // echo "<pre>";print_r(Mage::app()->getRequest()->getParam('id'));exit;
                    //if (!is_array(explode(",", $cat->getChildren()))) {
                    //echo "<pre>test1";print_r($cat->getChildrenCount());

                    if ($cat->getChildrenCount() == 0 && $cat->getLevel() == Mage::getStoreConfig('elenissec/elenisgrp/eleniscatlevel') && !in_array($categories->getId(), $excludeNavCategories)) {
                        // echo "<pre>1";print_r($childrens);exit;
                        $string.= "<dt><b>" . $cat->getName() . "</b></dt>";
                    } else if ($cat->getId()) {
                        //echo "<pre>2";print_r($childrens);exit;
                        $string.= '<a href="' . $cat->getURL() . '"><div class="unselectcat"></div><dt>' . $cat->getName() . '</dt></a>';
                    }
                }

                $string.= "<br />";
            }





            if ($cat->hasChildren()) {
                // $children = Mage::getModel('catalog/category')->load($cat->getId());

                $string.='<div class="block-content-' . $i . '"><dl id="narrow-by-list">';

                $string.= "<dt><b>" . $cat->getName() . "</b></dt>";
                //$string.="</dl></div>";
                //$string.="<div class='block-content-'.$cat->getId().'><dl id='narrow-by-list'>";
                $i = $i + 1;
                $string.= $this->get_categories($cat, $crcat, $i);
                $string.="</dl></div>";
            }
        }

        if (in_array($categories->getId(), $excludeNavCategories)) {
            $string.="</dl></div>";
        }
        return $string;
    }

}

