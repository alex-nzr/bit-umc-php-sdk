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

use ANZ\BitUmc\SDK\Core\Model\Request\Soap\BookAnAppointmentWithParams;
use ANZ\BitUmc\SDK\Core\Model\Request\Soap\CancelBookAnAppointment;
use ANZ\BitUmc\SDK\Core\Model\Request\Soap\FastBookAnAppointment;
use ANZ\BitUmc\SDK\Core\Model\Request\Soap\GetAppointmentStatus;
use ANZ\BitUmc\SDK\Core\Model\Request\Soap\GetListClinic;
use ANZ\BitUmc\SDK\Core\Model\Request\Soap\GetListEmployees;
use ANZ\BitUmc\SDK\Core\Model\Request\Soap\GetNomenclatureAndPrices;
use ANZ\BitUmc\SDK\Core\Model\Request\Soap\GetReserve;
use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Core\Model\Request\Soap\GetSchedule20;
use ANZ\BitUmc\SDK\Item\Order;
use DateTime;

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
        return $this->getResponse(new GetListClinic());
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getEmployees(): Result
    {
        return $this->getResponse(new GetListEmployees());
    }

    /**
     * @param string $clinicGuid
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getNomenclature(string $clinicGuid): Result
    {
        return $this->getResponse(new GetNomenclatureAndPrices($clinicGuid));
    }

    /**
     * @param int $days
     * @param string $clinicGuid
     * @param array $employees
     * @param \DateTime|null $startDate
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getSchedule(int $days = 14, string $clinicGuid = '', array $employees = [], ?DateTime $startDate = null): Result
    {
        return $this->getResponse(new GetSchedule20($days, $clinicGuid, $employees, $startDate));
    }

    /**
     * @param string $orderUid
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getOrderStatus(string $orderUid): Result
    {
        return $this->getResponse(new GetAppointmentStatus($orderUid));
    }

    /**
     * @param \ANZ\BitUmc\SDK\Item\Order $reserve
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function sendReserve(Order $reserve): Result
    {
        return $this->getResponse(new GetReserve($reserve));
    }

    /**
     * @param \ANZ\BitUmc\SDK\Item\Order $waitList
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function sendWaitList(Order $waitList): Result
    {
        return $this->getResponse(new FastBookAnAppointment($waitList));
    }

    /**
     * @param \ANZ\BitUmc\SDK\Item\Order $order
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function sendOrder(Order $order): Result
    {
        return $this->getResponse(new BookAnAppointmentWithParams($order));
    }

    /**
     * @param string $orderUid
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function deleteOrder(string $orderUid): Result
    {
        return $this->getResponse(new CancelBookAnAppointment($orderUid));
    }
}