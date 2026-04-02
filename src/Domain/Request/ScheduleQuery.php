<?php

namespace ANZ\BitUmc\SDK\Domain\Request;

use ANZ\BitUmc\SDK\Domain\Exception\InvalidArgumentException;
use DateTimeInterface;

final class ScheduleQuery
{
    public function __construct(
        public readonly int $days = 14,
        public readonly string $clinicUid = '',
        public readonly array $employeeUids = [],
        public readonly ?DateTimeInterface $startDate = null,
    ) {
        if ($this->days <= 0) {
            throw new InvalidArgumentException('Schedule days must be greater than zero.');
        }
    }
}
