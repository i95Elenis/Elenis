<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/27/13
 * Time   : 12:25 PM
 * File   : testhmac.php
 * Module : Ebizmarts_SagePaymentsPro
 */

    $cardNumber = '4111111111111111';
    $expirationDate = '0914';
    $application = 'DEMO';

    $data = '<![CDATA[<?xml version="1.0" encoding="utf-8"?>';
    $data .= '<VaultCreditCardTokenRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/wapiGateway.Models">';
    $data .= "<ApplicationId>$application</ApplicationId>";
    $data .=  "<CardExpirationDate>$expirationDate</CardExpirationDate>";
    $data .= "<CardNumber>$cardNumber</CardNumber>";
    $data .= "</VaultCreditCardTokenRequest>]]>";

    $url = 'https://gateway.sagepayments.net/web_services/gateway/api/hmacs';
    $key  = 'S6B7M6H8G7B7';
    $testurl = 'https://gateway.sagepayments.net/web_services/gateway/api/vaultcreditcardtokens';

    $xml = '<?xml version="1.0" encoding="utf-8"?>';
    $xml .= '<HMACRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/wapiGateway.Models">';
    $xml .= "<Data>$data</Data>";
    $xml .= "<Key>$key</Key>";
    $xml .= "<URL>$testurl</URL>";
    $xml .= "<Verb>POST</Verb>";
    $xml .= "</HMACRequest>";

    $curlSession = curl_init();

    curl_setopt($curlSession, CURLOPT_URL, $url);
    curl_setopt($curlSession, CURLOPT_HEADER, 1);
    curl_setopt($curlSession, CURLOPT_HTTPHEADER, array("Content-Type: application/xml; charset=utf-8","Content-length: ".strlen($xml), "Accept: application/xml"));
    curl_setopt($curlSession, CURLOPT_POST, 1);
    curl_setopt($curlSession, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 2);

    $rawresponse = curl_exec($curlSession);
    var_dump($rawresponse);
