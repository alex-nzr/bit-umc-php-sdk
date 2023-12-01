<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - Parameter.php
 * 29.11.2023 01:35
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Model\Request;

use stdClass;

/**
 * @class Parameter
 * @package ANZ\BitUmc\SDK\Core\Model\Request
 */
class Parameter extends stdClass
{
    private string $name;
    private string $Value;

    /**
     * Parameter constructor
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->Value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->Value;
    }
}