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
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

use ANZ\BitUmc\SDK\Core\Model\Request\Parameter;
use ANZ\BitUmc\SDK\Tools\DateFormatter;
use DateTime;

/**
 * @class GetSchedule20
 * @package ANZ\BitUmc\SDK\Core\Model\Request\Soap
 */
class GetSchedule20 extends BaseEntity
{
    protected string $StartDate;
    protected string $FinishDate;

    /**
     * GetSchedule20 constructor
     * @param int $days
     * @param string $clinicGuid
     * @param array $employees
     * @param \DateTime|null $startDate
     */
    public function __construct(int $days, string $clinicGuid, array $employees, ?DateTime $startDate = null)
    {
        $startDateString = ($startDate instanceof DateTime) ? $startDate->format('d F Y') : 'today + 1 days';

        $this->StartDate = DateFormatter::formatTimestampToISO(strtotime($startDateString));
        $this->FinishDate = DateFormatter::formatTimestampToISO(strtotime($startDateString . ' + ' . $days . ' days'));

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
}