<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - GetAppointmentStatus.php
 * 29.11.2023 20:45
 * ==================================================
 */


namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

/**
 * @class GetAppointmentStatus
 * @package ANZ\BitUmc\SDK\Core\Model\Request\Soap
 */
class GetAppointmentStatus extends BaseEntity
{
    protected string $GUID;

    /**
     * GetAppointmentStatus constructor
     * @param string $appointmentUid
     */
    public function __construct(string $appointmentUid)
    {
        $this->GUID = $appointmentUid;
    }
}