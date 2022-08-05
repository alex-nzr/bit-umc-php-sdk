<?php /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
/** @noinspection PhpPureAttributeCanBeAddedInspection */

/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - UmcClient.php
 * 04.08.2022 02:12
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service\Api;

use ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface;
use ANZ\BitUmc\SDK\Tools\Utils;
use ANZ\BitUmc\SDK\Tools\XmlParser;
use Exception;
use SimpleXMLElement;
use SoapClient;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UmcClient
 * @package ANZ\BitUmc\SDK\Service\Api
 */
class UmcClient implements ApiClientInterface
{
    private string $login;
    private string $password;
    private bool   $https;
    private string $address;
    private string $baseName;
    private SoapClient $soapClient;

    public static function create(): ApiClientInterface
    {
        return new static();
    }

    /**
     * @param string $login
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setLogin(string $login): ApiClientInterface
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @param string $password
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setPassword(string $password): ApiClientInterface
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setHttps(bool $enabled): ApiClientInterface
    {
        $this->https = $enabled;
        return $this;
    }

    /**
     * @param string $address
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setAddress(string $address): ApiClientInterface
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string $baseName
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     */
    public function setBaseName(string $baseName): ApiClientInterface
    {
        $this->baseName = $baseName;
        return $this;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Contract\ApiClientInterface
     * @throws \Exception
     */
    public function init(): ApiClientInterface
    {
        if (!class_exists('\SoapClient')) {
            throw new Exception("SOAP extension not found");
        }
        $this->soapClient = new SoapClient(
            $this->getFullBaseUrl(),
            $this->getSoapOptions()
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getFullBaseUrl(): string
    {
        $protocol = $this->https ? 'https' : 'http';
        return sprintf('%s://%s/%s/ws/ws1.1cws?wsdl', $protocol, $this->address, $this->baseName);
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getSoapOptions(): array
    {
        if (empty($this->login) || empty($this->password)){
            throw new Exception('Can not init client without login or password');
        }

        return [
            'login'          => $this->login,
            'password'       => $this->password,
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                    ]
                ]
            ),
            'soap_version' => SOAP_1_2,
            'trace' => 1,
            'connection_timeout' => 5000,
            'keep_alive' => false,
        ];
    }

    /***/
    public function call(string $soapMethod, array $params = []): Response
    {
        $result = new Response;
        try {
            $soapParams = ['parameters' => $params];

            $response = $this->soapClient->__soapCall($soapMethod, $soapParams);

            try {
                $xml = new SimpleXMLElement($response->return);
            }
            catch(Exception $e){
                throw new Exception($e->getMessage() . " | " . $response->return);
            }

            $jsonData = $this->handleXML($soapMethod, $xml);
            $result->setContent($jsonData);
        }
        catch (Exception $e){
            $result->setContent(Utils::getErrorResponse($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param string $soapMethod
     * @param \SimpleXMLElement $xml
     * @return string
     * @throws \Exception
     */
    private function handleXML(string $soapMethod, SimpleXMLElement $xml): string
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
        return json_encode($result);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getClinics(): Response
    {
        return $this->call(SoapMethod::CLINIC_ACTION_1C);
    }
}