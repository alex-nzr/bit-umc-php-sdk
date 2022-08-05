<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - SoapResult.php
 * 05.08.2022 21:41
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Soap;

use ANZ\BitUmc\SDK\Core\Contract\Result;
use Throwable;

/**
 * Class SoapResult
 * @package ANZ\BitUmc\SDK\Core\Soap
 */
class SoapResult implements Result
{
    protected bool $isSuccess;
    protected array $errors;
    protected array $data;

    public function __construct()
    {
        $this->isSuccess = true;
        $this->errors    = [];
        $this->data      = [];
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * @param \Throwable $error
     * @return \ANZ\BitUmc\SDK\Core\Contract\Result
     */
    public function addError(Throwable $error): Result
    {
        $this->isSuccess = false;
        $this->errors[] = $error;
        return $this;
    }

    /**
     * @return Throwable[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        $messages = [];
        foreach($this->getErrors() as $error)
        {
            $messages[] = $error->getMessage();
        }
        return $messages;
    }

    /**
     * @param array $data
     * @return \ANZ\BitUmc\SDK\Core\Contract\Result
     */
    public function setData(array $data): Result
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}