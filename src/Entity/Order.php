<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Order.php
 * 14.08.2022 14:23
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Entity;

use ANZ\BitUmc\SDK\Core\Contract\EntityInterface;

/**
 * Class Order
 * @package ANZ\BitUmc\SDK\Entity
 */
class Order implements EntityInterface
{
    private string $specialtyName;
    private string $date;
    private string $timeBegin;
    private string $employeeUid;
    private string $clinicUid;
    private string $name;
    private string $lastName;
    private string $secondName;
    private string $phone;
    private string $email;
    private string $address;
    private string $comment;
    private string $orderUid;
    private string $clientBirthday;
    private string $serviceDuration;
    private array  $services;

    public function __construct(
        string $specialtyName,  string $date,  string $timeBegin, string $employeeUid,
        string $clinicUid,      string $name,  string $lastName,  string $secondName,
        string $phone,          string $email, string $address,   string $comment,
        string $orderUid,     string $clientBirthday, string $serviceDuration,  array $services
    ){
        $this->specialtyName   = $specialtyName;
        $this->date            = $date;
        $this->timeBegin       = $timeBegin;
        $this->employeeUid     = $employeeUid;
        $this->clinicUid       = $clinicUid;
        $this->name            = $name;
        $this->lastName        = $lastName;
        $this->secondName      = $secondName;
        $this->phone           = $phone;
        $this->email           = $email;
        $this->address         = $address;
        $this->comment         = $comment;
        $this->orderUid      = $orderUid;
        $this->clientBirthday  = $clientBirthday;
        $this->serviceDuration = $serviceDuration;
        $this->services        = $services;
    }

    /**
     * @return string
     */
    public function getSpecialtyName(): string
    {
        return $this->specialtyName;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getTimeBegin(): string
    {
        return $this->timeBegin;
    }

    /**
     * @return string
     */
    public function getEmployeeUid(): string
    {
        return $this->employeeUid;
    }

    /**
     * @return string
     */
    public function getClinicUid(): string
    {
        return $this->clinicUid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return $this->secondName;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getOrderUid(): string
    {
        return $this->orderUid;
    }

    /**
     * @return string
     */
    public function getClientBirthday(): string
    {
        return $this->clientBirthday;
    }

    /**
     * @return string
     */
    public function getServiceDuration(): string
    {
        return $this->serviceDuration;
    }

    /**
     * @return string
     */
    public function getServices(): string
    {
        return implode(';', $this->services);
    }

    /**
     * @return string
     */
    public function getDurationType(): string
    {
        return 'ServiceDuration';
    }
}