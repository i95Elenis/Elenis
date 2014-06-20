<?php

class Webshopapps_Timegrid_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Get time grid date with prices & slots from database
     * Could go over 2 weeks
     * @param $startDate
     * @param $slot
     * @return Array of timegrid results
     */
    public function getTimeGrid($startDate, $slot)
    {
        $dayOfWeek = date("w", strtotime($startDate));
        $dayOfWeek = $dayOfWeek == 0 ? 7 : $dayOfWeek;

        if ($dayOfWeek == 1) { // monday
            $collection = Mage::getModel('timegrid/timegrid')->getCollection()
                ->setWeekCommencing($startDate)
                ->setSlot($slot);
            $timeGrid = $collection->getData();

            if (count($timeGrid) == 0) {
                // default slot
                return $this->getTimeGridData($slot);
            }
            return $timeGrid;

        } else {
            $prevMonday = Mage::helper('calendarbase')->getPreviousMonday($startDate);
            $prevMonTimeGrid = $this->getTimeGridData($slot, $prevMonday);
            if (count($prevMonTimeGrid) == 0) {
                $nextMonday = date('Y-m-d', strtotime($prevMonday . "+7 days"));
                $nextMonTimeGrid = $this->getTimeGridData($slot, $nextMonday);
                if (count($nextMonTimeGrid) == 0) {
                    return $this->getTimeGridData($slot);
                }
                $timeGrid = $this->getTimeGridData($slot); // default slot
                $innerTimeGrid = $nextMonTimeGrid[0];
                for ($i = 1; $i < $dayOfWeek; $i++) {
                    $timeGrid[0][$i . '_price'] = $innerTimeGrid[$i . '_price'];
                    $timeGrid[0][$i . '_slots'] = $innerTimeGrid[$i . '_slots'];
                    $timeGrid[0][$i . '_dispatch'] = $innerTimeGrid[$i . '_dispatch'];
                }
                return $timeGrid;
            } else {
                // have found one matching timegrid
                // populate those slots that are in
                $nextMonday = date('Y-m-d', strtotime($prevMonday . "+7 days"));
                $nextMonTimeGrid = $this->getTimeGridData($slot, $nextMonday);

                if (count($nextMonTimeGrid) == 0) {
                    $timeGrid = $this->getTimeGridData($slot); // default
                    $innerTimeGrid = $prevMonTimeGrid[0];
                    for ($i = $dayOfWeek; $i < 8; $i++) {
                        $timeGrid[0][$i . '_price'] = $innerTimeGrid[$i . '_price'];
                        $timeGrid[0][$i . '_slots'] = $innerTimeGrid[$i . '_slots'];
                        $timeGrid[0][$i . '_dispatch'] = $innerTimeGrid[$i . '_dispatch'];
                    }
                    return $timeGrid;
                } else {
                    $timeGrid = $prevMonTimeGrid;
                    $innerTimeGrid = $nextMonTimeGrid[0];
                    for ($i = 1; $i < $dayOfWeek; $i++) {
                        $timeGrid[0][$i . '_price'] = $innerTimeGrid[$i . '_price'];
                        $timeGrid[0][$i . '_slots'] = $innerTimeGrid[$i . '_slots'];
                        $timeGrid[0][$i . '_dispatch'] = $innerTimeGrid[$i . '_dispatch'];
                    }
                    return $timeGrid;
                }
            }
        }
    }

    // cant find date, use default. If this isnt there will blow at present TODO
    private function getTimeGridData($slot, $date = '0000-00-00')
    {
        $collection = Mage::getModel('timegrid/timegrid')->getCollection()
            ->setWeekCommencing($date)
            ->setSlot($slot);
        $data = $collection->getData();

        if(Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Customcalendar')){
            $defaultShipRate = Mage::helper('customcalendar')->getDefaultPrice();
        } else {
            $defaultShipRate = 0;
        }

        $defaultNumOfSlots = Mage::helper('webshopapps_dateshiphelper')->getDefaultSlots();

        if (empty($data) && $date == '0000-00-00') {
            $data[] = array(
                'timegrid_id' => 4,
                'week_commencing' => 0000 - 00 - 00,
                'time_slot_id' => 2,
                '1_price' => $defaultShipRate,
                '2_price' => $defaultShipRate,
                '3_price' => $defaultShipRate,
                '4_price' => $defaultShipRate,
                '5_price' => $defaultShipRate,
                '6_price' => $defaultShipRate,
                '7_price' => $defaultShipRate,
                '1_slots' => $defaultNumOfSlots,
                '2_slots' => $defaultNumOfSlots,
                '3_slots' => $defaultNumOfSlots,
                '4_slots' => $defaultNumOfSlots,
                '5_slots' => $defaultNumOfSlots,
                '6_slots' => $defaultNumOfSlots,
                '7_slots' => $defaultNumOfSlots,
                '1_dispatch' => $defaultNumOfSlots,
                '2_dispatch' => $defaultNumOfSlots,
                '3_dispatch' => $defaultNumOfSlots,
                '4_dispatch' => $defaultNumOfSlots,
                '5_dispatch' => $defaultNumOfSlots,
                '6_dispatch' => $defaultNumOfSlots,
                '7_dispatch' => $defaultNumOfSlots,
            );
        }
        return $data;
    }
}