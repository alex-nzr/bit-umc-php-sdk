<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - SoapClient.php
 * 05.08.2022 21:10
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Soap;

use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Tools\XmlParser;
use Exception;
use SimpleXMLElement;

/**
 * Class SoapClient
 * @package ANZ\BitUmc\SDK\Core\Soap
 */
class SoapClient extends \SoapClient
{
    /**
     * SoapClient constructor.
     * @param $wsdl
     * @param array|null $options
     * @throws \Exception
     */
    public function __construct($wsdl, array $options = null)
    {
        if (!class_exists('\SoapClient')) {
            throw new Exception("SOAP extension not found");
        }
        parent::__construct($wsdl, $options);
    }

    public function send(string $soapMethod, array $params = []): Result
    {
        error_reporting(E_ERROR | E_PARSE);

        $result = new Result();
        try {
            $soapParams = ['parameters' => $params];

            $response = $this->__soapCall($soapMethod, $soapParams);

            if (is_object($response) && property_exists($response, 'return'))
            {
                $xml = new SimpleXMLElement($response->return);
                $data = $this->handleXML($soapMethod, $xml);
                $result->setData($data);
            }
            else
            {
                throw new Exception("Unexpected format of response returned from 1C");
            }
        }
        catch (Exception $e){
            $result->addError($e);
        }
        return $result;
    }

    /**
     * @param string $soapMethod
     * @param \SimpleXMLElement $xml
     * @return array
     * @throws \Exception
     */
    private function handleXML(string $soapMethod, SimpleXMLElement $xml): array
    {
        $parser = XmlParser::getInstance();
        switch ($soapMethod)
        {
            case SoapMethod::CLINIC_ACTION_1C:
                $result = $parser->prepareClinicData($xml);
                break;
            case SoapMethod::EMPLOYEES_ACTION_1C:
                $result = $parser->prepareEmployeesData($xml);
                break;
            case SoapMethod::NOMENCLATURE_ACTION_1C:
                $result = $parser->prepareNomenclatureData($xml);
                break;
            case SoapMethod::SCHEDULE_ACTION_1C:
                $result = $parser->prepareScheduleData($xml);
                break;
            case SoapMethod::CREATE_RESERVE_ACTION_1C:
                $result = $parser->prepareReserveResultData($xml);
                break;
            case SoapMethod::CREATE_ORDER_ACTION_1C:
            case SoapMethod::CREATE_WAIT_LIST_ACTION_1C:
            case SoapMethod::DELETE_ORDER_ACTION_1C:
                $result = $parser->prepareCommonResultData($xml);
                break;
            case SoapMethod::GET_ORDER_STATUS_ACTION_1C:
                $result = $parser->prepareStatusResultData($xml);
                break;
            default:
                throw new Exception('Can not find way to process xml for method - '.$soapMethod.'.');
        }
        return $result;
    }
}