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

namespace ANZ\BitUmc\SDK\Item;

/**
 * Class Order
 * @package ANZ\BitUmc\SDK\Entity
 */
class Order
{
    /**
     * Order constructor
     * @param string $specialtyName
     * @param string $date
     * @param string $timeBegin
     * @param string $employeeUid
     * @param string $clinicUid
     * @param string $name
     * @param string $lastName
     * @param string $secondName
     * @param string $phone
     * @param string $email
     * @param string $address
     * @param string $comment
     * @param string $orderUid
     * @param string $clientBirthday
     * @param string $serviceDuration
     * @param array $services
     */
    public function __construct(
        protected readonly string $specialtyName,
        protected readonly string $date,
        protected readonly string $timeBegin,
        protected readonly string $employeeUid,
        protected readonly string $clinicUid,
        protected readonly string $name,
        protected readonly string $lastName,
        protected readonly string $secondName,
        protected readonly string $phone,
        protected readonly string $email,
        protected readonly string $address,
        protected readonly string $comment,
        protected readonly string $orderUid,
        protected readonly string $clientBirthday,
        protected readonly string $serviceDuration,
        protected readonly array $services
    ){
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
}