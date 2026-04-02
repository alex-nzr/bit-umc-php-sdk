<?php

namespace ANZ\BitUmc\SDK\Domain\Request;

use ANZ\BitUmc\SDK\Domain\Exception\InvalidArgumentException;
use DateTimeInterface;

final class WaitListRequest
{
    public function __construct(
        public readonly string $clinicUid,
        public readonly string $name,
        public readonly string $lastName,
        public readonly string $secondName,
        public readonly string $phone,
        public readonly DateTimeInterface $dateTimeBegin,
        public readonly string $specialtyName = '',
        public readonly string $email = '',
        public readonly string $address = '',
        public readonly string $comment = '',
    ) {
        foreach (['clinicUid' => $this->clinicUid, 'name' => $this->name, 'lastName' => $this->lastName, 'secondName' => $this->secondName, 'phone' => $this->phone] as $field => $value) {
            if ($value === '') {
                throw new InvalidArgumentException(sprintf('WaitListRequest %s can not be empty.', $field));
            }
        }
    }
}
