<?php
/** Calendar
 *
 * @category   Webshopapps
 * @package    Webshopapps_upscalendar
 * @author     Karen Baker
 * @copyright  Copyright (c) 2012 Zowta Ltd (http://www.webshopapps.com)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 **/

class Webshopapps_Upscalendar_Model_Usa_Shipping_Transit extends Varien_Object
{

    private static $_debug;

    protected static $_quotesCache = array();

    protected $_timeInTransitArr = array();

    protected $_timeInTransitLiveUrl  = 'https://onlinetools.ups.com/ups.app/xml/TimeInTransit';

    protected $_deliveryDate = null;

    protected $_earliestDeliveryDate = null;

    protected $_maxTransitDays = null;

    /* ==============================================================
     * TIME IN TRANSIT  UPS Call
     * ==============================================================
    */
    public function _getTimeInTransitArr($r,$xmlRequest,$dispatchDate,&$earliestDeliveryDate,$maxTransitDays)
    {
        $dispatchDateArr = array();

        $this->_maxTransitDays = $maxTransitDays;

        self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Upscalendar');

        if (!is_null(Mage::registry('md_quotes_cache'))) {
            $this->_quotesCache = Mage::registry('md_quotes_cache');
            if (self::$_debug) {
                Mage::helper('wsalogger/log')->postInfo('upscalendar','Found quotes cache','');
            }
        }
        $this->getCheapestDeliveryDate($r, $xmlRequest, $dispatchDate); // Only need to call when registry value set to get the earliest date, rest is thrown away
        $earliestDeliveryDate = $this->_earliestDeliveryDate;

        if (is_null(Mage::registry('md_delivery_date'))) {
            // this is the furthest in, so get the cheapest rate then find others for this.

            if (is_null($this->_deliveryDate)) {
                return array();
            }
        } else {
            $this->_deliveryDate = Mage::registry('md_delivery_date');
        }

        /*want on this delivery date so need to work out dispatch date.
          Have max delivery days 4 so at most will be delivery date-4.
          Cant be dispatched earlier than earliest date
          JIRA - MD-46
        */

        $timeBasedDispatchDate = strtotime($dispatchDate);
        for ($i=4;$i>0;$i--) {
            $proposedDispatchDateTime = strtotime($this->_deliveryDate . ' -'.$i.' days');
            if ($proposedDispatchDateTime>=$timeBasedDispatchDate) {
                $proposedDispatchDate = date('Ymd',$proposedDispatchDateTime);
                if (Mage::helper('webshopapps_dateshiphelper')->isValidDispatchDate($proposedDispatchDate)) {
                    $dispatchDateArr[] = $proposedDispatchDate;
                }
            }
        }

        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postInfo('upscalendar','Delivery date set to',$this->_deliveryDate);
            Mage::helper('wsalogger/log')->postInfo('upscalendar','Possible Dispatch Dates',$dispatchDateArr);
        }

        foreach ($dispatchDateArr as $dispatchDate) {
            $this->addTransitTimes($r, $xmlRequest, $dispatchDate);
        }

        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postInfo('upscalendar','Transit Time Array',$this->_timeInTransitArr);
        };

        // store quotes cache in registry
        Mage::register('md_quotes_cache',$this->_quotesCache);
        return $this->_timeInTransitArr;

    }

    protected function addTransitTimes($r, $origXmlRequest, $dispatchDate) {

        $xmlResponse = $this->runTransitRequest($r,$origXmlRequest, $dispatchDate);
        $this->_parseTransitResponse($xmlResponse);

    }

    protected function getCheapestDeliveryDate($r, $origXmlRequest, $dispatchDate) {
        $xmlResponse = $this->runTransitRequest($r,$origXmlRequest, $dispatchDate);
        $this->_parseTransitResponse($xmlResponse,true,false);
    }

    protected function runTransitRequest($r,$origXmlRequest, $dispatchDate) {
        $r->setPickupDate($dispatchDate); // e.g. 20121213

        $xmlRequest = $origXmlRequest;  // Take copy

        $params = array(
            'accept_UPS_license_agreement' => 'yes',
            // '10_action'      => $r->getAction(),
            '13_product'     => $r->getProduct(),
            '14_origCountry' => $r->getOrigCountry(),
            '15_origPostal'  => $r->getOrigPostal(),
            'origCity'       => $r->getOrigCity(),
            'origRegionCode' => $r->getOrigRegionCode(),
            '19_destPostal'  => Mage_Usa_Model_Shipping_Carrier_Abstract::USA_COUNTRY_ID == $r->getDestCountry() ?
                                substr($r->getDestPostal(), 0, 5) :
                                $r->getDestPostal(),
            '22_destCountry' => $r->getDestCountry(),
            'destRegionCode' => $r->getDestRegionCode(),
            '23_weight'      => $r->getWeight(),
            '47_rate_chart'  => $r->getPickup(),
            '48_container'   => $r->getContainer(),
            '49_residential' => $r->getDestType(),

        );

        $xml = new SimpleXMLElement('<?xml version = "1.0"?><TimeInTransitRequest xml:lang="en-US"/>');

        $request = $xml->addChild('Request');
        $transactionReference = $request->addChild('TransactionReference');
        $transactionReference->addChild('CustomerContext','Rating and Service');
        $transactionReference->addChild('XpciVersion','1.0');
        $request->addChild('RequestAction','TimeInTransit');

        $transitFrom = $xml->addChild('TransitFrom');
        $addressArtifactFormatOrigin = $transitFrom->addChild('AddressArtifactFormat');
        $addressArtifactFormatOrigin->addChild('PoliticalDivision2',$params['origCity']);
        $addressArtifactFormatOrigin->addChild('PoliticalDivision1',$params['origRegionCode']);
        $addressArtifactFormatOrigin->addChild('CountryCode',$params['14_origCountry']);
        $addressArtifactFormatOrigin->addChild('PostcodePrimaryLow',$params['15_origPostal']);
        $addressArtifactFormatOrigin->addChild('ResidentialAddressIndicator',$params['49_residential']);

        $transitTo = $xml->addChild('TransitTo');
        $addressArtifactFormatDest = $transitTo->addChild('AddressArtifactFormat');
        $addressArtifactFormatDest->addChild('PoliticalDivision1',$params['destRegionCode']);
        $addressArtifactFormatDest->addChild('CountryCode',$params['22_destCountry']);
        $addressArtifactFormatDest->addChild('PostcodePrimaryLow',$params['19_destPostal']);

        $shipmentWeight = $xml->addChild('ShipmentWeight');
        $unitOfMeasurement = $shipmentWeight->addChild('UnitOfMeasurement');
        $unitOfMeasurement->addChild('Code',$r->getUnitMeasure());
        $shipmentWeight->addChild('Weight',$params['23_weight']);

        $xml->addChild('TotalPackagesInShipment',1);

        $invoiceLineTotal = $xml->addChild('InvoiceLineTotal');
        $invoiceLineTotal->addChild('CurrencyCode','USD');
        $invoiceLineTotal->addChild('MonetaryValue',$r->getValueWithDiscount());
        $xml->addChild('PickupDate',$r->getPickupDate());
        $xml = $xml->asXML();
        $xmlRequest.=$xml;

        $xmlResponse = $this->_getCachedQuotes($xmlRequest);

        if ($xmlResponse === null) {
            $debugData = array('request' => Mage::helper('wsacommon/shipping')->formatXML($origXmlRequest));
            $debugData['AccessLicense'] = Mage::helper('wsacommon/shipping')->formatXML($xml);

            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->_timeInTransitLiveUrl);  // TODO Can be on test?
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $xmlResponse = curl_exec ($ch);

                $formattedXmlResponse = Mage::helper('wsacommon/shipping')->formatXML($xmlResponse) ;
                $debugData['result'] = $formattedXmlResponse;
                $this->_setCachedQuotes($xmlRequest, $formattedXmlResponse);
            } catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                $xmlResponse = '';
            }
            if (self::$_debug) {
                Mage::helper('wsalogger/log')->postInfo('upscalendar','UPS Request/Response',$debugData);
            }
        }
        Mage::log(json_encode($xmlResponse),1,"hj2.log");
        return $xmlResponse;
    }

    protected function _parseTransitResponse($xmlResponse, $getDeliveryDateOnly = false, $gotEarliest = true)
    {
        $proposedDeliveryDate = '';

        if (strlen(trim($xmlResponse))>0) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);
            $arr = $xml->getXpath("//TimeInTransitResponse/Response/ResponseStatusCode/text()");
            $success = (int)$arr[0][0];
            $blackoutDaysArr = Mage::helper('webshopapps_dateshiphelper')->getBlackoutDeliveryDays();
            $blackoutDatesArr = Mage::helper('webshopapps_dateshiphelper')->getBlackoutDeliveryDates();

            if($success){
                $arr = $xml->getXpath("//TimeInTransitResponse/TransitResponse/ServiceSummary");

                foreach ($arr as $shipElement){
                    $code = (string)$shipElement->Service->Code;
                    $priceCode = strval(Mage::getSingleton('upscalendar/upscalendar')->getCode('transit_map_code',$code));
                    if (empty($priceCode)) {
                        continue;
                    }

                    $dateString = strval($shipElement->EstimatedArrival->Date);
                    $usDateFormatArr = explode('-',$dateString);
                    $usDateFormat = $usDateFormatArr[1].'/'.$usDateFormatArr[2].'/'.$usDateFormatArr[0];

                    $dayOfWeekStr = strval($shipElement->EstimatedArrival->DayOfWeek);
                    $dayOfWeek = date('N',strtotime($dayOfWeekStr));

                    if(in_array($dayOfWeek,$blackoutDaysArr) ||
                        (in_array($usDateFormat,$blackoutDatesArr))) {
                        continue;
                    }
                    
                    //This includes the pickup day which UPS doesn't count
                    $actualTransitTime = $this->dateDiff("-",strval($shipElement->EstimatedArrival->PickupDate),
                        strval($shipElement->EstimatedArrival->Date));

                    if ($shipElement->EstimatedArrival->BusinessTransitDays>$this->_maxTransitDays ||
                        $actualTransitTime>$this->_maxTransitDays) {

                        if (self::$_debug) {
                            $debugData = array();
                            $debugData['Service'] = $shipElement->Service->Description;
                            $debugData['Ship Element'] = $shipElement;
                            $debugData['Actual Ship Time'] = $actualTransitTime;

                            Mage::helper('wsalogger/log')->postDebug('upscalendar','Method Not Available - Transit Time Too Long',$debugData);
                        }
                        continue;
                    }

                    if (!$gotEarliest) {
                        $this->_earliestDeliveryDate = date(Mage::helper('webshopapps_dateshiphelper')->getDateFormat(),strtotime(
                            strval($shipElement->EstimatedArrival->Date)));
                        $gotEarliest = true;
                    }

                    if ($getDeliveryDateOnly) {
                        $proposedDeliveryDate = strval($shipElement->EstimatedArrival->Date);

                    } else {
                        $proposedDeliveryDate = str_replace('-','',strval($shipElement->EstimatedArrival->Date));
                        if ($proposedDeliveryDate == $this->_deliveryDate) {
                            $this->_timeInTransitArr[$priceCode] = array (
                                    'method' 	=> 		$code,
                                    'pickup'    =>      strval($shipElement->EstimatedArrival->PickupDate),
                                    'date'		=>		$dateString,
                                    'day'		=>		$dayOfWeekStr
                            );
                        }
                    }

                }
            } // else return empty array
        }

        if ($getDeliveryDateOnly) {
            $this->_deliveryDate = str_replace('-','',$proposedDeliveryDate);
        }
        Mage::log(json_encode($this->_deliveryDate),1,"hj.log");
    }

    function dateDiff($dFormat, $beginDate,$endDate)
    {
        $date_parts1=explode($dFormat, $beginDate);
        $date_parts2=explode($dFormat, $endDate);
        $start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
        $end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
        return $end_date - $start_date;
    }

    /**
     * Returns cache key for some request to carrier quotes service
     *
     * @param string|array $requestParams
     * @return string
     */
    private function _getQuotesCacheKey($requestParams)
    {
        if (is_array($requestParams)) {
            $requestParams = implode(',', array_merge(
                    array('md_quotes_cache'),
                    array_keys($requestParams),
                    $requestParams)
            );
        }
        return crc32($requestParams);
    }

    /**
     * Checks whether some request to rates have already been done, so we have cache for it
     * Used to reduce number of same requests done to carrier service during one session
     *
     * Returns cached response or null
     *
     * @param string|array $requestParams
     * @return null|string
     */
    private function _getCachedQuotes($requestParams)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        return isset(self::$_quotesCache[$key]) ? self::$_quotesCache[$key] : null;
    }

    /**
     * Sets received carrier quotes to cache
     *
     * @param string|array $requestParams
     * @param string $response
     * @return Mage_Usa_Model_Shipping_Carrier_Abstract
     */
    private function _setCachedQuotes($requestParams, $response)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        self::$_quotesCache[$key] = $response;
        return $this;
    }

    /**
     * Get shipment by code
     *
     * @param string $code
     * @param string $origin
     * @return array|bool
     */
    public function getShipmentByCode($code, $origin = null){
        if($origin===null){
            $origin = Mage::getStoreConfig('carriers/ups/origin_shipment');
        }
        $arr = $this->getCode('originShipment',$origin);
        if(isset($arr[$code]))
            return $arr[$code];
        else
            return false;
    }
}