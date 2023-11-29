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

namespace ANZ\BitUmc\SDK\Builder;

use ANZ\BitUmc\SDK\Core\Contract\BuilderInterface;
use ANZ\BitUmc\SDK\Item\Order;
use ANZ\BitUmc\SDK\Tools\Utils;
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

    private string $specialtyName   = '';
    private string $date            = '';
    private string $timeBegin       = '';
    private string $employeeUid     = '';
    private string $clinicUid       = '';
    private string $name            = '';
    private string $lastName        = '';
    private string $secondName      = '';
    private string $phone           = '';
    private string $email           = '';
    private string $address         = '';
    private string $comment         = '';
    private string $orderUid      = '';
    private string $clientBirthday  = '';
    private string $serviceDuration = '';
    private array  $services        = [];

    private array $requiredReserveParams  = [ 'date', 'timeBegin', 'employeeUid', 'clinicUid' ];
    private array $requiredWaitListParams = [
        'date', 'timeBegin', 'clinicUid',
        'name', 'lastName', 'secondName',  'phone'
    ];
    private array $requiredOrderParams = [
        'date', 'timeBegin', 'clinicUid', 'employeeUid',
        'name', 'lastName', 'secondName',  'phone'
    ];

    /**
     * OrderBuilder constructor.
     * @param string|null $mode
     */
    private function __construct(?string $mode = null)
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
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public static function createOrder(): OrderBuilder
    {
        return new static(static::ORDER_MODE);
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
     * @param \DateTime $dateTimeBegin
     * @return $this
     */
    public function setDateTimeBegin(DateTime $dateTimeBegin): OrderBuilder
    {
        $isoDate = \ANZ\BitUmc\SDK\Tools\DateFormatter::formatTimestampToISO($dateTimeBegin->getTimestamp());
        $this->date      = $isoDate;
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
     * @param string $orderUid
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setOrderUid(string $orderUid): OrderBuilder
    {
        $this->orderUid = $orderUid;
        return $this;
    }

    /**
     * @param \DateTime $date
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setClientBirthday(DateTime $date): OrderBuilder
    {
        $isoDate = \ANZ\BitUmc\SDK\Tools\DateFormatter::formatTimestampToISO($date->getTimestamp());
        $this->clientBirthday = $isoDate;
        return $this;
    }

    /**
     * @param int $seconds
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setAppointmentDuration(int $seconds): OrderBuilder
    {
        $this->serviceDuration = Utils::calculateDurationFromSeconds($seconds);
        return $this;
    }

    /**
     * @param array $services
     * @return \ANZ\BitUmc\SDK\Service\Builder\OrderBuilder
     */
    public function setServices(array $services): OrderBuilder
    {
        $this->services = $services;
        return $this;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Item\Order
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
            $this->orderUid,
            $this->clientBirthday,
            $this->serviceDuration,
            $this->services,
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
                throw new Exception('Required param "' . $param . '" is empty');
            }

            if ($param ==='date' || $param === 'timeBegin')
            {
                $date        = new DateTime($this->$param);
                $dateString  = ($param ==='date') ? 'today' : 'now';
                $compareDate = new DateTime($dateString);
                if ($date < $compareDate) {
                    throw new Exception('Param "' . $param . '" can not be less then ' . $compareDate->format("d.m.Y H:i:s"));
                }
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
        if (empty($this->clientBirthday)){
            $this->clientBirthday = '';
        }
        if (empty($this->serviceDuration)){
            $this->serviceDuration = '';
        }
        if (empty($this->serviceUid)){
            $this->serviceUid = '';
        }
        if (empty($this->orderUid)){
            $this->orderUid = '';
        }
    }
}