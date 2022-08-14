<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - OrderBuilder.php
 * 10.08.2022 22:27
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service\Builder;

use ANZ\BitUmc\SDK\Core\Contract\BuilderInterface;
use ANZ\BitUmc\SDK\Entity\Order;
use DateTime;
use Exception;

/**
 * Class OrderBuilder
 * @package ANZ\BitUmc\SDK\Service\Builder
 */
class OrderBuilder implements BuilderInterface
{
    const WAIT_LIST_MODE = 'WAIT_LIST';
    const RESERVE_MODE   = 'RESERVE';
    const ORDER_MODE     = 'ORDER';

    private string $mode;

    private string $specialtyName = '';
    private string $date = '';
    private string $timeBegin = '';
    private string $employeeUid = '';
    private string $clinicUid = '';
    private string $name = '';
    private string $lastName = '';
    private string $secondName = '';
    private string $phone = '';
    private string $email = '';
    private string $address = '';
    private string $comment = '';
    private string $reserveUid = '';
    private array   $orderAdditionalParams = [];

    private array $requiredReserveParams = [ 'date', 'timeBegin', 'employeeUid', 'clinicUid' ];

    public function __construct(?string $mode = null)
    {
        $allowedModes = [static::WAIT_LIST_MODE, static::RESERVE_MODE, static::ORDER_MODE];

        if (empty($mode) || !in_array($mode, $allowedModes))
        {
            $mode = static::WAIT_LIST_MODE;
        }
        $this->mode = $mode;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public static function init(): OrderBuilder
    {
        return new static();
    }

    /**
     * @param string $specialtyName
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setSpecialtyName(string $specialtyName): OrderBuilder
    {
        $this->specialtyName = $specialtyName;
        return $this;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate(DateTime $date): OrderBuilder
    {
        $isoDate = \ANZ\BitUmc\SDK\Tools\DateTime::formatTimestampToISO($date->getTimestamp());
        $this->date = $isoDate;
        return $this;
    }

    /**
     * @param \DateTime $timeBegin
     * @return $this
     */
    public function setTimeBegin(DateTime $timeBegin): OrderBuilder
    {
        $isoDate = \ANZ\BitUmc\SDK\Tools\DateTime::formatTimestampToISO($timeBegin->getTimestamp());
        $this->timeBegin = $isoDate;
        return $this;
    }

    /**
     * @param string $employeeUid
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setEmployeeUid(string $employeeUid): OrderBuilder
    {
        $this->employeeUid = $employeeUid;
        return $this;
    }

    /**
     * @param string $clinicUid
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setClinicUid(string $clinicUid): OrderBuilder
    {
        $this->clinicUid = $clinicUid;
        return $this;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Entity\Order
     * @throws \Exception
     */
    public function build(): Order
    {
        switch ($this->mode)
        {
            case static::WAIT_LIST_MODE:
                $this->checkWaitListParams();
                break;
            case static::RESERVE_MODE:
                $this->checkReserveParams();
                break;
            case static::ORDER_MODE:
                $this->checkOrderParams();
                break;
        }

        return new Order(
            $this->specialtyName,
            $this->date,
            $this->timeBegin,
            $this->employeeUid,
            $this->clinicUid,
            $this->name,
            $this->lastName,
            $this->secondName,
            $this->phone,
            $this->email,
            $this->address,
            $this->comment,
            $this->reserveUid,
            $this->orderAdditionalParams,
        );
    }

    /**
     * @throws \Exception
     */
    public function checkReserveParams()
    {
        foreach ($this->requiredReserveParams as $param) {
            if (empty($this->$param)){
                throw new Exception('Required params ' . $param . ' is empty');
            }
        }

        if (empty($this->specialtyName)){
            $this->specialtyName = '';
        }
    }
}