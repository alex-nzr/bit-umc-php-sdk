<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 29.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

use ANZ\BitUmc\SDK\Item\Order;

class FastBookAnAppointment extends BaseAppointment
{
    protected string $Specialization;

    /**
     * FastBookAnAppointment constructor
     * @param \ANZ\BitUmc\SDK\Item\Order $appointment
     */
    public function __construct(Order $appointment)
    {
        parent::__construct($appointment);
        $this->Specialization = $appointment->getSpecialtyName();
    }
}