<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - FastBookAnAppointment.php
 * 29.11.2023 20:54
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

use ANZ\BitUmc\SDK\Item\Order;

/**
 * @class FastBookAnAppointment
 * @package ANZ\BitUmc\SDK\Core\Model\Request\Soap
 */
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