<?php
class Elenis_EmailTemplateDateFormat_Model_Sales_Order extends Mage_Sales_Model_Order
{
     /**
     * Get formated order created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormatDate($format,$createdDate)
    {
        return date($format,strtotime($createdDate));
    }

}
		