<?php

class Elenis_VideoLink_Block_Adminhtml_Videolink_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("videolinkGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel("videolink/videolink")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("id", array(
            "header" => Mage::helper("videolink")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "id",
        ));

        $this->addColumn("title", array(
            "header" => Mage::helper("videolink")->__("Video Title"),
            "index" => "title",
        ));
         $this->addColumn("video_link", array(
            "header" => Mage::helper("videolink")->__("Video Link"),
            "index" => "video_link",
        ));
        $this->addColumn("width", array(
            "header" => Mage::helper("videolink")->__("Video Width"),
            "index" => "width",
        ));
        $this->addColumn("height", array(
            "header" => Mage::helper("videolink")->__("Video Height"),
            "index" => "height",
        ));
        /*$this->addColumn('category_id', array(
            'header' => Mage::helper('videolink')->__('Category Id'),
            'index' => 'category_id',
            'type' => 'options',
            'options' => Elenis_VideoLink_Block_Adminhtml_Videolink_Grid::getOptionArray4(),
        ));
        */
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_videolink', array(
            'label' => Mage::helper('videolink')->__('Remove Videolink'),
            'url' => $this->getUrl('*/adminhtml_videolink/massRemove'),
            'confirm' => Mage::helper('videolink')->__('Are you sure?')
        ));
        return $this;
    }

    static public function getCategoriesOption() {
        $catIds = array();
        $category = Mage::getSingleton('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $catIds = $tree->getCollection()->getAllIds();
        foreach ($catIds as $id) {
                $categories[$i]['id'] = $id;
                $categoryObject = $category->load($id);
                $categories[$i]['name'] = $categoryObject->getName();
                $i++;
        }
        return($categories);
    }

    static public function getCategoriesValue() {
        $data_array = array();
        foreach (Elenis_VideoLink_Block_Adminhtml_Videolink_Grid::getCategoriesOption() as $values) {
                 $data_array[] = array('value' => $values['id'], 'label' => $values['name']);
        }
        
        return($data_array);
    }

    

}