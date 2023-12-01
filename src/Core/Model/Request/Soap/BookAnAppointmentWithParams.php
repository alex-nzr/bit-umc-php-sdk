<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - BookAnAppointmentWithParams.php
 * 29.11.2023 21:01
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

use ANZ\BitUmc\SDK\Core\Model\Request\Parameter;
use ANZ\BitUmc\SDK\Item\Order;

/**
 * @class BookAnAppointmentWithParams
 * @package ANZ\BitUmc\SDK\Core\Model\Request\Soap
 */
class BookAnAppointmentWithParams extends BaseAppointment
{
    protected string $EmployeeID;
    protected string $GUID;

    /**
     * BookAnAppointmentWithParams constructor
     * @param \ANZ\BitUmc\SDK\Item\Order $appointment
     */
    public function __construct(Order $appointment)
    {
        parent::__construct($appointment);
        $this->EmployeeID = $appointment->getEmployeeUid();
        $this->GUID = $appointment->getOrderUid();
        $this->Params[] = new Parameter(static::BIRTHDAY_PARAM_NAME, $appointment->getClientBirthday());
        $this->Params[] = new Parameter(static::DURATION_PARAM_NAME, $appointment->getServiceDuration());

        if (!empty($appointment->getServices()))
        {
            $this->Params[] = new Parameter(static::SERVICES_PARAM_NAME, $appointment->getServices());
            $this->Params[] = new Parameter(static::DURATION_TYPE_PARAM_NAME, static::DURATION_TYPE_PARAM_VALUE);
        }
    }
}