<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Result.php
 * 05.08.2022 21:41
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Operation;

use Throwable;

/**
 * Class Result
 * @package ANZ\BitUmc\SDK\Core\Operation
 */
class Result
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
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
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
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
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