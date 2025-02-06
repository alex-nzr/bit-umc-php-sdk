<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 29.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

use ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel;
use ReflectionClass;
use stdClass;

abstract class BaseEntity extends stdClass implements IRequestModel
{
    const CLINIC_PARAM_NAME = 'Clinic';
    const EMPLOYEES_PARAM_NAME = 'Employees';
    const BIRTHDAY_PARAM_NAME = 'Birthday';
    const DURATION_PARAM_NAME = 'Duration';
    const SERVICES_PARAM_NAME = 'Services';
    const DURATION_TYPE_PARAM_NAME = 'DurationType';
    const DURATION_TYPE_PARAM_VALUE = 'ServiceDuration';

    protected array $Params = [];

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return (new ReflectionClass(static::class))->getShortName();
    }
}