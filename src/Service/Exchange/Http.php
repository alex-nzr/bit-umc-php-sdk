<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 28.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Service\Exchange;

use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Item\Order;
use DateTime;

/**
 * Class is not finished, because this SDK does not work with http-services yet
 */
class Http extends Base
{
    public function getClinics(): Result
    {
        // TODO: Implement getClinics() method.
        return new Result();
    }

    public function getEmployees(): Result
    {
        // TODO: Implement getEmployees() method.
        return new Result();
    }

    public function getNomenclature(string $clinicGuid): Result
    {
        // TODO: Implement getNomenclature() method.
        return new Result();
    }

    public function getSchedule(int $days = 14, string $clinicGuid = '', array $employees = [], ?DateTime $startDate = null): Result
    {
        // TODO: Implement getSchedule() method.
        return new Result();
    }

    public function getOrderStatus(string $orderUid): Result
    {
        // TODO: Implement getOrderStatus() method.
        return new Result();
    }

    public function sendReserve(Order $reserve): Result
    {
        // TODO: Implement sendReserve() method.
        return new Result();
    }

    public function sendWaitList(Order $waitList): Result
    {
        // TODO: Implement sendWaitList() method.
        return new Result();
    }

    public function sendOrder(Order $order): Result
    {
        // TODO: Implement sendOrder() method.
        return new Result();
    }

    public function deleteOrder(string $orderUid): Result
    {
        // TODO: Implement deleteOrder() method.
        return new Result();
    }
}