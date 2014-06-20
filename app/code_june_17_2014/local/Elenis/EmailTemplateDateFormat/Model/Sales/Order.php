<?php

class Elenis_EmailTemplateDateFormat_Model_Sales_Order extends Mage_Sales_Model_Order {

    /**
     * Get formated order created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormatDate($format) {     //  echo $format;
        //echo $format."<br/>".date_default_timezone_get();
        // echo Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format,true);
        //$customDate=date($format,strtotime($this->getCreatedAtStoreDate()))."<br/>";
        //$format = Mage::app()->getLocale()->getDateFormat($format);
        //echo $currentDate = Mage::getModel('core/date')->date($format, strtotime($this->getCreatedAtStoreDate()));
        //echo Mage::app()->getLocale()->date(strtotime($this->getCreatedAtStoreDate()), null, null, false)->toString($format);
        $timezone = Mage::app()->getStore(Mage::app()->getStore()->getStoreId())->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        //   echo $timezone;
        $myDateTime = new DateTime($this->getCreatedAtStoreDate(), new DateTimeZone($timezone));
        $myDateTime->setTimezone(new DateTimeZone($timezone));
        $date = $myDateTime->format($format);
        // echo   $date;exit;
        // a more complete example
        return $date;
        // return Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format,true);
    }

    public function getCustomFormWithInputText() {
        $formData = "";
        $formData.="<form action='https://app.e2ma.net/app2/audience/signup/1735160/1719846/?v=a' method='post' id='newsletter-validate-detail'>
 <div class='v-fix'><input name='email' type='text' id='newsletter' value='' /></div>
        <button type='submit' class='button' title='Subscribe'><span><span></span></span></button>
</form>";
        return $formData;
    }

}

