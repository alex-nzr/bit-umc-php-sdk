<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - WsWriter.php
 * 04.08.2022 01:43
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service\OneC;

use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Core\Soap\SoapMethod;
use ANZ\BitUmc\SDK\Service\Builder\OrderBuilder;

/**
 * Class WsWriter
 * @package ANZ\BitUmc\SDK\Service\OneC
 */
class WsWriter extends Common
{
    /**
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function createWaitList(): OrderBuilder
    {
        return new OrderBuilder(OrderBuilder::WAIT_LIST_MODE);
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function createReserve(): OrderBuilder
    {
        return new OrderBuilder(OrderBuilder::RESERVE_MODE);
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function createOrder(): OrderBuilder
    {
        return new OrderBuilder(OrderBuilder::ORDER_MODE);
    }

    /**
     * @param \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder $builder
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     * @throws \Exception
     */
    public function sendReserve(OrderBuilder $builder): Result
    {
        $reserve = $builder->build();
        $params = [
            'Specialization' => $reserve->getSpecialtyName(),
            'Date'           => $reserve->getDate(),
            'TimeBegin'      => $reserve->getTimeBegin(),
            'EmployeeID'     => $reserve->getEmployeeUid(),
            'Clinic'         => $reserve->getClinicUid(),
        ];
        return $this->getResponse(SoapMethod::CREATE_RESERVE_ACTION_1C, $params);
    }
}

/*if ($this->useWaitList)
{
    $paramsToSend = [
        'Specialization'    => $params['specialty'] ?? "",
        'PatientSurname'    => $params['surname'],
        'PatientName'       => $params['name'],
        'PatientFatherName' => $params['middleName'],
        'Date'              => $params['orderDate'],
        'TimeBegin'         => $params['timeBegin'],
        'Phone'             => Utils::formatPhone($params['phone']),
        'Email'             => $params['email'] ?? '',
        'Address'           => $params['address'] ?? '',
        'Clinic'            => $params['clinicUid'],
        'Comment'           => Loc::getMessage('ANZ_APPOINTMENT_WAITING_LIST_COMMENT', [
            '#FULL_NAME#' => $params['name'] ." ". $params['middleName'] ." ". $params['surname'],
            '#PHONE#'     => Utils::formatPhone($params['phone']),
            '#DATE#'      => date("d.m.Y", strtotime($params['orderDate'])),
            '#TIME#'      => date("H:i", strtotime($params['timeBegin'])),
            '#COMMENT#'   => $params['comment'] ?? '',
        ]),
    ];
}
elseif ($this->useReserve)
{
    $paramsToReserve = [
        'Specialization' => $this->specialty,
        'Date'           => $this->date,
        'TimeBegin'      => $this->timeBegin,
        'EmployeeID'     => $this->employeeUid,
        'Clinic'         => $this->clinicUid,
    ];
    return new Reserve($specialty, $date, $start, $employee, $clinic);
}
elseif($this->useOrder)
{
    $paramsToSend = [
        'EmployeeID'        => $params['refUid'],
        'PatientSurname'    => $params['surname'],
        'PatientName'       => $params['name'],
        'PatientFatherName' => $params['middleName'],
        'Date'              => $params['orderDate'],
        'TimeBegin'         => $params['timeBegin'],
        'Comment'           => $params['comment'] ?? '',
        'Phone'             => Utils::formatPhone($params['phone']),
        'Email'             => $params['email'] ?? '',
        'Address'           => $params['address'] ?? '',
        'Clinic'            => $params['clinicUid'],
        'GUID'              => $xml_id,
        'Params'            => [
            'Birthday' => '',
            'Duration' => '',
            //'Service' => 'serviceGuid',
        ]
    ];
}*/