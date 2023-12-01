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
    /**
     * GetAppointmentStatus constructor
     * @param string $GUID
     */
    public function __construct(protected string $GUID)
    {
    }
}