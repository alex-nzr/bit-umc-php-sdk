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
use SoapVar;

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
        $result = new Result();
        try {
            if (is_array($params['Params']))
            {
                $params['Params'] = $this->prepareSoapParams($params['Params']);
            }

            $soapParams = ['parameters' => $params];

            $response = $this->__soapCall($soapMethod, $soapParams);

            if (is_object($response) && property_exists($response, 'return'))
            {
                //Damned KOSTYL, because sometimes api of Bit-umc is striking...
                if ($response->return === 'Ok')
                {
                    $result->setData(['success' => true]);
                }
                elseif($response->return === 'Error')
                {
                    throw new Exception('1c returned an unknown error to the request - ' . $soapMethod);
                }
                else
                {
                    try
                    {
                        $xml = @(new SimpleXMLElement($response->return));
                    }
                    catch (Exception $e)
                    {
                        throw new Exception("Error on parsing xml from response. Response data: " . $response->return);
                    }

                    $data = $this->handleXML($soapMethod, $xml);
                    $result->setData($data);
                }
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

    /**
     * @param array $params
     * @return SoapVar[]
     */
    protected function prepareSoapParams(array $params): array
    {
        $soapParams = [];
        foreach ($params as $key => $param)
        {
            $paramValue = $param;
            if (is_array($param))
            {
                $paramValue = implode(';', array_filter($param, function ($val){
                    return is_string($val);
                }));
            }
            $soapParams[] = new SoapVar(
                '<ns2:Property name="'.$key.'"><ns2:Value>'.$paramValue.'</ns2:Value></ns2:Property>',
                XSD_ANYXML
            );
        }
        return $soapParams;
    }
}