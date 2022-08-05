<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Result.php
 * 05.08.2022 21:54
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract;

use Throwable;

/**
 * Interface Result
 * @package ANZ\BitUmc\SDK\Core\Contract
 */
interface Result
{
    /**
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * @param \Throwable $error
     * @return \ANZ\BitUmc\SDK\Core\Contract\Result
     */
    public function addError(Throwable $error): Result;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @return array
     */
    public function getErrorMessages(): array;

    /**
     * @param array $data
     * @return \ANZ\BitUmc\SDK\Core\Contract\Result
     */
    public function setData(array $data): Result;

    /**
     * @return array
     */
    public function getData(): array;
}