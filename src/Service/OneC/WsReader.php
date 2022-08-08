<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - WsReader.php
 * 04.08.2022 01:43
 * ==================================================
 */


namespace ANZ\BitUmc\SDK\Service\OneC;

use ANZ\BitUmc\SDK\Core\Operation\Result;
use ANZ\BitUmc\SDK\Core\Soap\SoapMethod;
use ANZ\BitUmc\SDK\Tools\Utils;

/**
 * Class WsReader
 * @package ANZ\BitUmc\SDK\Service\OneC
 */
class WsReader extends Common
{
    /**
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getClinics(): Result
    {
        return $this->getResponse(SoapMethod::CLINIC_ACTION_1C);
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getEmployees(): Result
    {
        return $this->getResponse(SoapMethod::EMPLOYEES_ACTION_1C);
    }

    /**
     * @param string $clinicGuid
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getNomenclature(string $clinicGuid): Result
    {
        $params = [
            'Clinic' => $clinicGuid,
            'Params' => []
        ];
        return $this->getResponse(SoapMethod::NOMENCLATURE_ACTION_1C, $params);
    }

    /**
     * @param int $days
     * @param string $clinicGuid
     * @param array $employees
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getSchedule(int $days = 14, string $clinicGuid = '', array $employees = []): Result
    {
        $period = Utils::getDateInterval($days);
        $params = array_merge($period, [
            'Params' => [
                'Clinic' => $clinicGuid,
                'Employees' => $employees
            ]
        ]);
        return $this->getResponse(SoapMethod::SCHEDULE_ACTION_1C, $params);
    }
}