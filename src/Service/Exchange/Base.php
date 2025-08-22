<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 04.08.2022
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Service\Exchange;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\Service\IExchangeService;
use ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel;
use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Item\Order;
use DateTime;

abstract class Base implements IExchangeService
{
    protected IClient $client;

    /**
     * Base constructor
     * @param \ANZ\BitUmc\SDK\Core\Contract\Connection\IClient $client
     */
    public function __construct(IClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel $requestModel
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getResponse(IRequestModel $requestModel): Result
    {
        return $this->client->send($requestModel);
    }

    abstract public function getClinics(): Result;
    abstract public function getEmployees(): Result;
    abstract public function getNomenclature(string $clinicGuid): Result;
    abstract public function getSchedule(int $days = 14, string $clinicGuid = '', array $employees = [], ?DateTime $startDate = null): Result;
    abstract public function getOrderStatus(string $orderUid): Result;
    abstract public function sendReserve(Order $reserve): Result;
    abstract public function sendWaitList(Order $waitList): Result;
    abstract public function sendOrder(Order $order): Result;
    abstract public function deleteOrder(string $orderUid): Result;
}