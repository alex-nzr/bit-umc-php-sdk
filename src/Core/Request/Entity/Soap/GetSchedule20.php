<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - GetSchedule.php
 * 29.11.2023 01:18
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Request\Entity\Soap;

use ANZ\BitUmc\SDK\Core\Request\Entity\Parameter;
use ANZ\BitUmc\SDK\Tools\DateTime;

/**
 * @class GetSchedule
 * @package ANZ\BitUmc\SDK\Core\Soap\Request\Entity
 */
class GetSchedule20 extends BaseEntity
{
    const CLINIC_PARAM_NAME = 'Clinic';
    const EMPLOYEES_PARAM_NAME = 'Employees';

    protected string $StartDate;
    protected string $FinishDate;

    /**
     * GetSchedule constructor
     * @param int $days
     * @param string $clinicGuid
     * @param array $employees
     */
    public function __construct(int $days, string $clinicGuid, array $employees)
    {
        $this->StartDate = DateTime::formatTimestampToISO(strtotime('today + 1 days'));
        $this->FinishDate = DateTime::formatTimestampToISO(strtotime('today + ' . $days . ' days'));

        if (!empty($clinicGuid))
        {
            $this->Params[] = new Parameter(static::CLINIC_PARAM_NAME, $clinicGuid);
        }

        if (!empty($employees))
        {
            $this->Params[] = new Parameter(
                static::EMPLOYEES_PARAM_NAME,
                implode(';', array_filter($employees, function ($val){
                    return is_string($val) && !empty($val);
                }))
            );
        }
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->StartDate;
    }

    /**
     * @return string
     */
    public function getFinishDate(): string
    {
        return $this->FinishDate;
    }
}