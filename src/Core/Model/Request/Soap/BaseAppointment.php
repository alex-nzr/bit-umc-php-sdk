<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - BaseAppointment.php
 * 29.11.2023 21:09
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

use ANZ\BitUmc\SDK\Item\Order;
use ANZ\BitUmc\SDK\Tools\PhoneFormatter;

/**
 * @class BaseAppointment
 * @package ANZ\BitUmc\SDK\Core\Model\Request\Soap
 */
abstract class BaseAppointment extends BaseEntity
{
    protected string $PatientSurname;
    protected string $PatientName;
    protected string $PatientFatherName;
    protected string $Date;
    protected string $TimeBegin;
    protected string $Phone;
    protected string $Email;
    protected string $Address;
    protected string $Clinic;
    protected string $Comment;

    /**
     * BaseAppointment constructor
     * @param \ANZ\BitUmc\SDK\Item\Order $appointment
     */
    public function __construct(Order $appointment)
    {
        $this->PatientSurname    = $appointment->getLastName();
        $this->PatientName       = $appointment->getName();
        $this->PatientFatherName = $appointment->getSecondName();
        $this->Date              = $appointment->getDate();
        $this->TimeBegin         = $appointment->getTimeBegin();
        $this->Phone             = PhoneFormatter::formatPhone($appointment->getPhone());
        $this->Email             = $appointment->getEmail();
        $this->Address           = $appointment->getAddress();
        $this->Clinic            = $appointment->getClinicUid();
        $this->Comment           = $appointment->getComment();
    }
}