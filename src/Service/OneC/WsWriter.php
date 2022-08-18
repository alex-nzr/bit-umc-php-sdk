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
use ANZ\BitUmc\SDK\Entity\Order;
use ANZ\BitUmc\SDK\Tools\Utils;

/**
 * Class WsWriter
 * @package ANZ\BitUmc\SDK\Service\OneC
 */
class WsWriter extends Common
{
    /**
     * @param \ANZ\BitUmc\SDK\Entity\Order $reserve
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
        return $this->getResponse(SoapMethod::CREATE_RESERVE_ACTION_1C, $params);
    }

    /**
     * @param \ANZ\BitUmc\SDK\Entity\Order $waitList
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
        return $this->getResponse(SoapMethod::CREATE_WAIT_LIST_ACTION_1C, $params);
    }
}

/*
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