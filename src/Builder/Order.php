<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Order.php
 * 10.08.2022 22:27
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Builder;

use ANZ\BitUmc\SDK\Core\Contract\IBuilder;
use ANZ\BitUmc\SDK\Item\Order as OrderItem;
use ANZ\BitUmc\SDK\Tools\DateFormatter;
use DateTime;
use Exception;

/**
 * Class Order
 * @package ANZ\BitUmc\SDK\Service\Builder
 */
class Order implements IBuilder
{
    const WAIT_LIST_MODE = 'WAIT_LIST';
    const RESERVE_MODE   = 'RESERVE';
    const ORDER_MODE     = 'ORDER';

    protected string $mode;

    protected string $specialtyName   = '';
    protected string $date            = '';
    protected string $timeBegin       = '';
    protected string $employeeUid     = '';
    protected string $clinicUid       = '';
    protected string $name            = '';
    protected string $lastName        = '';
    protected string $secondName      = '';
    protected string $phone           = '';
    protected string $email           = '';
    protected string $address         = '';
    protected string $comment         = '';
    protected string $orderUid        = '';
    protected string $clientBirthday  = '';
    protected string $serviceDuration = '';
    protected array  $services        = [];

    protected array $requiredReserveParams  = [ 'date', 'timeBegin', 'employeeUid', 'clinicUid' ];
    protected array $requiredWaitListParams = [
        'date', 'timeBegin', 'clinicUid',
        'name', 'lastName', 'secondName',  'phone'
    ];
    protected array $requiredOrderParams = [
        'date', 'timeBegin', 'clinicUid', 'employeeUid',
        'name', 'lastName', 'secondName',  'phone'
    ];

    /**
     * Order constructor.
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
     * @param string|null $mode
     * @return static
     */
    public static function init(?string $mode = null): static
    {
        return new static($mode);
    }

    /**
     * @return static
     */
    public static function createWaitList(): static
    {
        return new static(static::WAIT_LIST_MODE);
    }

    /**
     * @return static
     */
    public static function createReserve(): static
    {
        return new static(static::RESERVE_MODE);
    }

    /**
     * @return static
     */
    public static function createOrder(): static
    {
        return new static(static::ORDER_MODE);
    }

    /**
     * @param string $specialtyName
     * @return $this
     */
    public function setSpecialtyName(string $specialtyName): static
    {
        $this->specialtyName = $specialtyName;
        return $this;
    }

    /**
     * @param \DateTime $dateTimeBegin
     * @return $this
     */
    public function setDateTimeBegin(DateTime $dateTimeBegin): static
    {
        $isoDate = DateFormatter::formatTimestampToISO($dateTimeBegin->getTimestamp());
        $this->date = $isoDate;
        $this->timeBegin = $isoDate;
        return $this;
    }

    /**
     * @param string $employeeUid
     * @return $this
     */
    public function setEmployeeUid(string $employeeUid): static
    {
        $this->employeeUid = $employeeUid;
        return $this;
    }

    /**
     * @param string $clinicUid
     * @return $this
     */
    public function setClinicUid(string $clinicUid): static
    {
        $this->clinicUid = $clinicUid;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @param string $secondName
     * @return $this
     */
    public function setSecondName(string $secondName): static
    {
        $this->secondName = $secondName;
        return $this;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @param string $email
     * @return $this
     * @throws \Exception
     */
    public function setEmail(string $email): static
    {
        if (!empty($email))
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                throw new Exception("Email address $email is not valid");
            }
            $this->email = $email;
        }

        return $this;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param string $orderUid
     * @return $this
     */
    public function setOrderUid(string $orderUid): static
    {
        $this->orderUid = $orderUid;
        return $this;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setClientBirthday(DateTime $date): static
    {
        $isoDate = DateFormatter::formatTimestampToISO($date->getTimestamp());
        $this->clientBirthday = $isoDate;
        return $this;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setAppointmentDuration(int $seconds): static
    {
        $this->serviceDuration = DateFormatter::calculateDurationFromSeconds($seconds);
        return $this;
    }

    /**
     * @param array $services
     * @return $this
     */
    public function setServices(array $services): static
    {
        $this->services = $services;
        return $this;
    }

    /**
     * @return \ANZ\BitUmc\SDK\Item\Order
     * @throws \Exception
     */
    public function build(): OrderItem
    {
        $this->checkRequiredParams();
        $this->checkAndFillNotRequiredParams();

        return new OrderItem(
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
        if (empty($this->orderUid)){
            $this->orderUid = '';
        }
    }
}