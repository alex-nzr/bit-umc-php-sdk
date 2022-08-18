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
    private array  $orderAdditionalParams = [];

    private array $requiredReserveParams  = [ 'date', 'timeBegin', 'employeeUid', 'clinicUid' ];
    private array $requiredWaitListParams = [
        'date', 'timeBegin', 'clinicUid',
        'name', 'lastName', 'secondName',  'phone'
    ];

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
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public static function createWaitList(): OrderBuilder
    {
        return new static(static::WAIT_LIST_MODE);
    }

    /**
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public static function createReserve(): OrderBuilder
    {
        return new static(static::RESERVE_MODE);
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
     * @param string $name
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setName(string $name): OrderBuilder
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $lastName
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setLastName(string $lastName): OrderBuilder
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @param string $secondName
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setSecondName(string $secondName): OrderBuilder
    {
        $this->secondName = $secondName;
        return $this;
    }

    /**
     * @param string $phone
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setPhone(string $phone): OrderBuilder
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @param string $email
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setEmail(string $email): OrderBuilder
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $address
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setAddress(string $address): OrderBuilder
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string $comment
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setComment(string $comment): OrderBuilder
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param string $reserveUid
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setReserveUid(string $reserveUid): OrderBuilder
    {
        $this->reserveUid = $reserveUid;
        return $this;
    }

    /**
     * @param int $seconds
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setDuration(int $seconds): OrderBuilder
    {
        //$this->orderAdditionalParams['Duration'] = ...
        return $this;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Entity\Order
     * @throws \Exception
     */
    public function build(): Order
    {
        $this->checkRequiredParams();
        $this->checkAndFillNotRequiredParams();

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
    protected function checkRequiredParams()
    {
        $params = [];
        switch ($this->mode)
        {
            case static::WAIT_LIST_MODE:
                $params = $this->requiredWaitListParams;
                break;
            case static::RESERVE_MODE:
                $params = $this->requiredReserveParams;
                break;
            case static::ORDER_MODE:
                $params = $this->requiredOrderParams;
                break;
        }
        foreach ($params as $param) {
            if (empty($this->$param)){
                throw new Exception('Required params ' . $param . ' is empty');
            }
        }
    }

    /**
     * @return void
     */
    protected function checkAndFillNotRequiredParams(): void
    {
        if (empty($this->specialtyName)){
            $this->specialtyName = '';
        }
        if (empty($this->email)){
            $this->email = '';
        }
        if (empty($this->address)){
            $this->address = '';
        }
        if (empty($this->comment)){
            $this->comment = '';
        }
    }
}