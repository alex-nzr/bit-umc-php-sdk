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

namespace ANZ\BitUmc\SDK\Client;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Enumeration\ClientScope;
use ANZ\BitUmc\SDK\Core\Enumeration\Protocol;
use ANZ\BitUmc\SDK\Core\Enumeration\SoapMethod;
use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Tools\XmlParser;
use Exception;
use SimpleXMLElement;
use SoapVar;

/**
 * Class SoapClient
 * @package ANZ\BitUmc\SDK\Core\Soap
 */
class SoapClient extends \SoapClient implements IClient
{
    private ClientScope $scope;

    /**
     * SoapClient constructor closed. Use method create() instead
     * @param $wsdl
     * @param array|null $options
     * @throws \Exception
     */
    private function __construct($wsdl, array $options = null)
    {
        if (!class_exists('\SoapClient')) {
            throw new Exception("SOAP extension not found");
        }
        parent::__construct($wsdl, $options);
    }

    /**
     * @param string $login
     * @param string $password
     * @param \ANZ\BitUmc\SDK\Core\Enumeration\Protocol $protocol
     * @param string $address
     * @param string $baseName
     * @param \ANZ\BitUmc\SDK\Core\Enumeration\ClientScope $scope
     * @return static
     * @throws \Exception
     */
    public static function create(
        string $login, string $password, Protocol $protocol, string $address, string $baseName, ClientScope $scope
    ): static
    {
        return new static(
            static::getFullBaseUrl($protocol, $address, $baseName),
            [
                'login'          => $login,
                'password'       => $password,
                'stream_context' => stream_context_create(
                    [
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                        ]
                    ]
                ),
                'soap_version'       => SOAP_1_2,
                'location'           => static::getLocation($protocol, $address, $baseName),
                'cache_wsdl'         => WSDL_CACHE_NONE,
                'exceptions'         => true,
                'trace'              => 1,
                'connection_timeout' => 5000,
                'keep_alive'         => false,
            ]
        );
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Enumeration\Protocol $protocol
     * @param string $address
     * @param string $baseName
     * @return string
     */
    protected static function getFullBaseUrl(Protocol $protocol, string $address, string $baseName): string
    {
        return sprintf('%s://%s/%s/ws/ws1.1cws?wsdl', $protocol->value, $address, $baseName);
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Enumeration\Protocol $protocol
     * @param string $address
     * @param string $baseName
     * @return string
     */
    protected static function getLocation(Protocol $protocol, string $address, string $baseName): string
    {
        return sprintf('%s://%s/%s/ws/ws1.1cws', $protocol->value, $address, $baseName);
    }

    /**
     * @param string $method
     * @param array $params
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function send(string $method, array $params = []): Result
    {
        $result = new Result();
        try
        {
            if (array_key_exists('Params', $params) && is_array($params['Params']))
            {
                $params['Params'] = $this->prepareSoapParams($params['Params']);
            }

            $soapParams = ['parameters' => $params];

            $response = $this->__soapCall($method, $soapParams);

            if (is_object($response) && property_exists($response, 'return'))
            {
                //Sometimes the response formats from the Bit-umc api are amazing...
                if ($response->return === 'Ok')
                {
                    $result->setData(['success' => true]);
                }
                elseif($response->return === 'Error')
                {
                    throw new Exception('1c returned an unknown error to the request - ' . $method);
                }
                else
                {
                    try
                    {
                        $xml = @(new SimpleXMLElement($response->return));
                    }
                    catch (Exception $e)
                    {
                        throw new Exception(
                            "Error on parsing xml from response. Response data: " . $response->return .
                            ' Error message - ' . $e->getMessage()
                        );
                    }

                    $data = $this->handleXML($method, $xml);
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
     * @param string $method
     * @param \SimpleXMLElement $xml
     * @return array
     * @throws \Exception
     */
    private function handleXML(string $method, SimpleXMLElement $xml): array
    {
        $parser = XmlParser::getInstance();
        return match ($method) {
            SoapMethod::CLINIC_ACTION_1C->value => $parser->prepareClinicData($xml),

            SoapMethod::EMPLOYEES_ACTION_1C->value => $parser->prepareEmployeesData($xml),

            SoapMethod::NOMENCLATURE_ACTION_1C->value => $parser->prepareNomenclatureData($xml),

            SoapMethod::SCHEDULE_ACTION_1C->value => $parser->prepareScheduleData($xml),

            SoapMethod::CREATE_RESERVE_ACTION_1C->value => $parser->prepareReserveResultData($xml),

            SoapMethod::CREATE_ORDER_ACTION_1C->value,
            SoapMethod::CREATE_WAIT_LIST_ACTION_1C->value,
            SoapMethod::DELETE_ORDER_ACTION_1C->value => $parser->prepareCommonResultData($xml),

            SoapMethod::GET_ORDER_STATUS_ACTION_1C->value => $parser->prepareStatusResultData($xml),

            default => throw new Exception('Can not find way to process xml for method - ' . $method . '.'),
        };
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

    /**
     * @param \ANZ\BitUmc\SDK\Core\Enumeration\ClientScope $scope
     * @return void
     */
    public function setScope(ClientScope $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Enumeration\ClientScope
     */
    public function getScope(): ClientScope
    {
        return $this->scope;
    }
}