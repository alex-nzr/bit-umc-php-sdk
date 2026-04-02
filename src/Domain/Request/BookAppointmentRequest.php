<?php

namespace ANZ\BitUmc\SDK\Domain\Request;

use ANZ\BitUmc\SDK\Domain\Exception\InvalidArgumentException;
use DateTimeInterface;

final class BookAppointmentRequest
{
    public function __construct(
        public readonly string $clinicUid,
        public readonly string $employeeUid,
        public readonly string $name,
        public readonly string $lastName,
        public readonly string $secondName,
        public readonly string $phone,
        public readonly DateTimeInterface $dateTimeBegin,
        public readonly string $specialtyName = '',
        public readonly string $email = '',
        public readonly string $address = '',
        public readonly string $comment = '',
        public readonly string $appointmentUid = '',
        public readonly ?DateTimeInterface $clientBirthday = null,
        public readonly ?int $appointmentDuration = null,
        public readonly array $services = [],
    ) {
        foreach (['clinicUid' => $this->clinicUid, 'employeeUid' => $this->employeeUid, 'name' => $this->name, 'lastName' => $this->lastName, 'secondName' => $this->secondName, 'phone' => $this->phone] as $field => $value) {
            if ($value === '') {
                throw new InvalidArgumentException(sprintf('BookAppointmentRequest %s can not be empty.', $field));
            }
        }

        if ($this->appointmentDuration !== null && $this->appointmentDuration <= 0) {
            throw new InvalidArgumentException('BookAppointmentRequest appointmentDuration must be greater than zero.');
        }
    }
}
