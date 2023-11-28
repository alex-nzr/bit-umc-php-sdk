<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - Soap.php
 * 28.11.2023 22:54
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Service\Exchange;

use ANZ\BitUmc\SDK\Core\Dictionary\SoapMethod;
use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Core\Request\Entity\Soap\GetSchedule20;
use ANZ\BitUmc\SDK\Item\Order;
use ANZ\BitUmc\SDK\Tools\Utils;

/**
 * @class Soap
 * @package ANZ\BitUmc\SDK\Service\Exchange
 */
class Soap extends Base
{
    /**
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getClinics(): Result
    {
        return $this->getResponse(SoapMethod::CLINIC_ACTION_1C->value);
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getEmployees(): Result
    {
        return $this->getResponse(SoapMethod::EMPLOYEES_ACTION_1C->value);
    }

    /**
     * @param string $clinicGuid
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getNomenclature(string $clinicGuid): Result
    {
        /*$GetNomenclatureAndPrices = new \stdClass();

        $GetNomenclatureAndPrices->Clinic = $clinicGuid;

        $GetNomenclatureAndPrices->Params = new \stdClass();

        $method = SoapMethod::NOMENCLATURE_ACTION_1C->value;
        $response = $this->client->$method($GetNomenclatureAndPrices);

        Logger::print($response);die();*/

        $params = [
            'Clinic' => $clinicGuid,
            'Params' => []
        ];
        return $this->getResponse(SoapMethod::NOMENCLATURE_ACTION_1C->value, $params);
    }

    /**
     * @param int $days
     * @param string $clinicGuid
     * @param array $employees
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getSchedule(int $days = 14, string $clinicGuid = '', array $employees = []): Result
    {
        return $this->getResponse(new GetSchedule20($days, $clinicGuid, $employees));
    }

    /**
     * @param string $orderUid
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getOrderStatus(string $orderUid): Result
    {
        $params = [
            'GUID' => $orderUid
        ];
        return $this->getResponse(SoapMethod::GET_ORDER_STATUS_ACTION_1C->value, $params);
    }

    /**
     * @param \ANZ\BitUmc\SDK\Item\Order $reserve
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function sendReserve(Order $reserve): Result
    {
        $params = [
            'Specialization' => $reserve->getSpecialtyName(),
            'Date'           => $reserve->getDate(),
            'TimeBegin'      => $reserve->getTimeBegin(),
            'EmployeeID'     => $reserve->getEmployeeUid(),
            'Clinic'         => $reserve->getClinicUid(),
        ];
        return $this->getResponse(SoapMethod::CREATE_RESERVE_ACTION_1C->value, $params);
    }

    /**
     * @param \ANZ\BitUmc\SDK\Item\Order $waitList
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function sendWaitList(Order $waitList): Result
    {
        $params = [
            'Specialization'    => $waitList->getSpecialtyName(),
            'PatientSurname'    => $waitList->getLastName(),
            'PatientName'       => $waitList->getName(),
            'PatientFatherName' => $waitList->getSecondName(),
            'Date'              => $waitList->getDate(),
            'TimeBegin'         => $waitList->getTimeBegin(),
            'Phone'             => Utils::formatPhone($waitList->getPhone()),
            'Email'             => $waitList->getEmail(),
            'Address'           => $waitList->getAddress(),
            'Clinic'            => $waitList->getClinicUid(),
            'Comment'           => $waitList->getComment(),
        ];
        return $this->getResponse(SoapMethod::CREATE_WAIT_LIST_ACTION_1C->value, $params);
    }

    /**
     * @param \ANZ\BitUmc\SDK\Item\Order $order
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function sendOrder(Order $order): Result
    {
        $params = [
            'EmployeeID'        => $order->getEmployeeUid(),
            'PatientSurname'    => $order->getLastName(),
            'PatientName'       => $order->getName(),
            'PatientFatherName' => $order->getSecondName(),
            'Date'              => $order->getDate(),
            'TimeBegin'         => $order->getTimeBegin(),
            'Phone'             => Utils::formatPhone($order->getPhone()),
            'Email'             => $order->getEmail(),
            'Address'           => $order->getAddress(),
            'Clinic'            => $order->getClinicUid(),
            'Comment'           => $order->getComment(),
            'GUID'              => $order->getOrderUid(),
            'Params'            => [
                'Birthday' => $order->getClientBirthday(),
                'Duration' => $order->getServiceDuration(),
            ]
        ];

        if (!empty($order->getServices()))
        {
            $params['Params']['Services']     = $order->getServices();
            $params['Params']['DurationType'] = $order->getDurationType();
        }

        return $this->getResponse(SoapMethod::CREATE_ORDER_ACTION_1C->value, $params);
    }

    /**
     * @param string $orderUid
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function deleteOrder(string $orderUid): Result
    {
        $params = [
            'GUID' => $orderUid,
        ];
        return $this->getResponse(SoapMethod::DELETE_ORDER_ACTION_1C->value, $params);
    }
}