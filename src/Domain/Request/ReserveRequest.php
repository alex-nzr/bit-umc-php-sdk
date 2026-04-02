<?php

namespace ANZ\BitUmc\SDK\Domain\Request;

use ANZ\BitUmc\SDK\Domain\Exception\InvalidArgumentException;
use DateTimeInterface;

final class ReserveRequest
{
    public function __construct(
        public readonly string $clinicUid,
        public readonly string $employeeUid,
        public readonly DateTimeInterface $dateTimeBegin,
        public readonly string $specialtyName = '',
    ) {
        if ($this->clinicUid === '') {
            throw new InvalidArgumentException('ReserveRequest clinicUid can not be empty.');
        }

        if ($this->employeeUid === '') {
            throw new InvalidArgumentException('ReserveRequest employeeUid can not be empty.');
        }
    }
}
