<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - CancelBookAnAppointment.php
 * 29.11.2023 21:27
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

/**
 * @class CancelBookAnAppointment
 * @package ANZ\BitUmc\SDK\Core\Model\Request\Soap
 */
class CancelBookAnAppointment extends BaseEntity
{
    /**
     * CancelBookAnAppointment constructor
     * @param string $GUID
     */
    public function __construct(protected string $GUID)
    {
    }
}