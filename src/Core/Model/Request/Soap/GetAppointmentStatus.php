<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 29.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

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