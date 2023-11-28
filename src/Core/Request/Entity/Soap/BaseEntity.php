<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - BaseEntity.php
 * 29.11.2023 01:23
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Request\Entity\Soap;

use ANZ\BitUmc\SDK\Core\Contract\Soap\IRequestEntity;
use ReflectionClass;
use stdClass;

/**
 * @class BaseEntity
 * @package ANZ\BitUmc\SDK\Core\Request\Entity
 */
abstract class BaseEntity extends stdClass implements IRequestEntity
{
    protected array $Params = [];

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return (new ReflectionClass(static::class))->getShortName();
    }
}