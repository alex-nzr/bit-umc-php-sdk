<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - XmlParser.php
 * 04.08.2022 22:08
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Service;

use ANZ\BitUmc\SDK\Core\Config\Parameters;
use ANZ\BitUmc\SDK\Core\Dictionary\SoapResponseKey;
use ANZ\BitUmc\SDK\Tools\DateFormatter;
use Exception;
use SimpleXMLElement;

/**
 * @class XmlParser
 * @package ANZ\BitUmc\SDK\Service
 */
class XmlParser
{
    /**
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function xmlToArray(SimpleXMLElement $xml): array
    {
        return json_decode(json_encode($xml), true);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareClinicData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);

        $clinicKey      = SoapResponseKey::CLINIC->value;
        $clinicTitleKey = SoapResponseKey::TITLE->value;
        $clinicUidKey   = SoapResponseKey::UID_RU->value;

        $clinics = [];
        if (key_exists($clinicKey, $xmlArr) && is_array($xmlArr[$clinicKey]))
        {
            if (!array_is_list($xmlArr[$clinicKey]))
            {
                $clinics[$xmlArr[$clinicKey][$clinicUidKey]] = [
                    'uid' => $xmlArr[$clinicKey][$clinicUidKey],
                    'name' => $xmlArr[$clinicKey][$clinicTitleKey]
                ];
            }
            else
            {
                foreach ($xmlArr[$clinicKey] as $item) {
                    $clinic = [];
                    $clinic['uid'] = $item[$clinicUidKey];
                    $clinic['name'] = $item[$clinicTitleKey];
                    $clinics[$item[$clinicUidKey]] = $clinic;
                }
            }
        }
        return $clinics;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareEmployeesData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);

        $employeeKey = SoapResponseKey::EMPLOYEE->value;

        $employees = [];
        if (key_exists($employeeKey, $xmlArr) && is_array($xmlArr[$employeeKey]))
        {
            if (!array_is_list($xmlArr[$employeeKey]))
            {
                $employee = $this->buildEmployeeData($xmlArr[$employeeKey]);
                $employees[$employee['uid']] = $employee;
            }
            else
            {
                foreach ($xmlArr[$employeeKey] as $item)
                {
                    if (!is_array($item) || empty($item))
                    {
                        continue;
                    }

                    $employee = $this->buildEmployeeData($item);
                    $employees[$employee['uid']] = $employee;
                }
            }
        }

        return $employees;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareNomenclatureData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);

        $catalogKey     = SoapResponseKey::CATALOG->value;
        $isFolderKey    = SoapResponseKey::IS_FOLDER->value;

        $nomenclature = [];
        if (key_exists($catalogKey, $xmlArr) && is_array($xmlArr[$catalogKey]))
        {
            if (!array_is_list($xmlArr[$catalogKey]))
            {
                $product = $this->buildProductData($xmlArr[$catalogKey]);
                $nomenclature[$product['uid']] = $product;
            }
            else
            {
                foreach ($xmlArr[$catalogKey] as $item)
                {
                    if (!is_array($item) || empty($item) || ($item[$isFolderKey] === true)){
                        continue;
                    }
                    $product = $this->buildProductData($item);
                    $nomenclature[$product['uid']] = $product;
                }
            }
        }
        return $nomenclature;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return array
     * @throws \Exception
     */
    public function prepareScheduleData(SimpleXMLElement $xml): array
    {
        $xmlArr           = $this->xmlToArray($xml);
        $scheduleKey      = SoapResponseKey::SCHEDULE->value;
        $scheduleErrorKey = SoapResponseKey::SCHEDULE_ERROR->value;

        if (key_exists($scheduleErrorKey, $xmlArr)){
            throw new Exception((string)$xmlArr[$scheduleErrorKey]);
        }

        $schedule = [];
        if (key_exists($scheduleKey, $xmlArr) && is_array($xmlArr[$scheduleKey])){
            $schedule = $this->processScheduleData($xmlArr[$scheduleKey]);
        }
        return $schedule;
    }

    /**
     * @param array $schedule
     * @return array
     */
    protected function processScheduleData(array $schedule): array
    {
        if (!array_is_list($schedule))
        {
            $schedule = [$schedule];
        }

        $employeeUidKey       = SoapResponseKey::EMPLOYEE_UID->value;
        $employeeFullNameKey  = SoapResponseKey::EMPLOYEE_FULL_NAME->value;
        $scheduleDurationKey  = SoapResponseKey::SCHEDULE_DURATION->value;
        $schedulePeriodsKey   = SoapResponseKey::SCHEDULE_PERIODS->value;
        $scheduleOnePeriodKey = SoapResponseKey::SCHEDULE_PERIOD->value;
        $scheduleFreeTimeKey  = SoapResponseKey::SCHEDULE_FREE_TIME->value;
        $scheduleBusyTimeKey  = SoapResponseKey::SCHEDULE_BUSY_TIME->value;
        $specialtyKey         = SoapResponseKey::SPECIALTY->value;
        $clinicKey            = SoapResponseKey::CLINIC->value;

        $formattedSchedule = [];
        foreach ($schedule as $item)
        {
            if (!empty($item[$clinicKey]))
            {
                $clinicUid = $item[$clinicKey];
                if (!key_exists($clinicUid, $formattedSchedule) || !is_array($formattedSchedule[$clinicUid]))
                {
                    $formattedSchedule[$clinicUid] = [];
                }

                $specialtyName = !empty($item[$specialtyKey]) ? $item[$specialtyKey] : SoapResponseKey::EMPTY_SPECIALTY->value;
                $specialtyUid  = $this->getSpecialtyUid($specialtyName);

                if (!key_exists($specialtyUid, $formattedSchedule[$clinicUid])
                    || !is_array($formattedSchedule[$clinicUid][$specialtyUid])
                ){
                    $formattedSchedule[$clinicUid][$specialtyUid] = [];
                }

                if (!empty($item[$employeeUidKey]))
                {
                    $employeeUid     = $item[$employeeUidKey];
                    $employeeName    = $item[$employeeFullNameKey];

                    $durationSeconds = Parameters::DEFAULT_DURATION;
                    $durationFrom1C  = '';
                    if (!empty($item[$scheduleDurationKey]))
                    {
                        $durationFrom1C  = $item[$scheduleDurationKey];
                        $durationSeconds = intval(date("H", strtotime($durationFrom1C))) * 3600
                            + intval(date("i", strtotime($durationFrom1C))) * 60;
                    }

                    if (empty($formattedSchedule[$clinicUid][$specialtyUid][$employeeUid]))
                    {
                        $formattedSchedule[$clinicUid][$specialtyUid][$employeeUid] = [
                            'specialtyName'     => $specialtyName,
                            'employeeName'      => $employeeName,
                            'durationFrom1C'    => $durationFrom1C,
                            'durationInSeconds' => $durationSeconds,
                            'timetable'         => [
                                'freeFormatted' => [],
                                'busy'          => [],
                                'free'          => [],
                            ]
                        ];
                    }

                    $timetable = [];

                    $freeTime = (is_array($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) && count($item[$schedulePeriodsKey][$scheduleFreeTimeKey]) > 0)
                        ? $item[$schedulePeriodsKey][$scheduleFreeTimeKey][$scheduleOnePeriodKey] : [];
                    $busyTime = (is_array($item[$schedulePeriodsKey][$scheduleBusyTimeKey]) && count($item[$schedulePeriodsKey][$scheduleBusyTimeKey]) > 0)
                        ? $item[$schedulePeriodsKey][$scheduleBusyTimeKey][$scheduleOnePeriodKey] : [];

                    if (!array_is_list($freeTime)) {
                        $freeTime = [$freeTime];
                    }
                    if (!array_is_list($busyTime)) {
                        $busyTime = [$busyTime];
                    }

                    $timetable["free"] = array_merge(
                        $formattedSchedule[$clinicUid][$specialtyUid][$employeeUid]['timetable']["free"],
                        $this->formatTimetable($freeTime, 0, true)
                    );
                    $timetable["busy"] = array_merge(
                        $formattedSchedule[$clinicUid][$specialtyUid][$employeeUid]['timetable']["busy"],
                        $this->formatTimetable($busyTime, 0, true)
                    );
                    $timetable["freeFormatted"] = array_merge(
                        $formattedSchedule[$clinicUid][$specialtyUid][$employeeUid]['timetable']["freeFormatted"],
                        $this->formatTimetable($freeTime, $durationSeconds)
                    );

                    $formattedSchedule[$clinicUid][$specialtyUid][$employeeUid]['timetable'] = $timetable;
                }
            }
        }

        return $formattedSchedule;
    }

    /**
     * @param $array
     * @param int $duration
     * @param bool $useDefaultInterval
     * @return array
     */
    public function formatTimetable($array, int $duration, bool $useDefaultInterval = false): array
    {
        if (!is_array($array) || empty($array)){
            return [];
        }

        if (!$duration > 0){
            $duration = 1800;
        }

        if (!array_is_list($array)) {
            $array = [$array];
        }

        $scheduleDateKey  = SoapResponseKey::SCHEDULE_DATE_TIME->value;
        $scheduleStartKey = SoapResponseKey::SCHEDULE_START_TIME->value;
        $scheduleEndKey   = SoapResponseKey::SCHEDULE_END_TIME->value;

        $formattedArray = [];
        foreach ($array as $item)
        {
            $formattedDateKey = date("d-m-Y", strtotime($item[$scheduleDateKey]));

            $timestampTimeBegin = strtotime($item[$scheduleStartKey]);
            $timestampTimeEnd = strtotime($item[$scheduleEndKey]);

            if ($useDefaultInterval)
            {
                $newTimeTableItem = $this->formatTimeTableItem($item, (int)$timestampTimeBegin, (int)$timestampTimeEnd);
                if (!key_exists($formattedDateKey, $formattedArray) || !is_array($formattedArray[$formattedDateKey]))
                {
                    $formattedArray[$formattedDateKey] = [$newTimeTableItem];
                }
                else
                {
                    $formattedArray[$formattedDateKey][] = $newTimeTableItem;
                }
            }
            else
            {
                $timeDifference = $timestampTimeEnd - $timestampTimeBegin;
                $appointmentsCount = round($timeDifference / $duration);

                for ($i = 0; $i < $appointmentsCount; $i++)
                {
                    $start = $timestampTimeBegin + ($duration * $i);
                    $end = $timestampTimeBegin + ($duration * ($i+1));

                    $newTimeTableItem = $this->formatTimeTableItem($item, (int)$start, (int)$end);
                    if (!key_exists($formattedDateKey, $formattedArray) || !is_array($formattedArray[$formattedDateKey]))
                    {
                        $formattedArray[$formattedDateKey] = [$newTimeTableItem];
                    }
                    else
                    {
                        $formattedArray[$formattedDateKey][] = $newTimeTableItem;
                    }
                }
            }
        }
        return $formattedArray;
    }

    /**
     * @param array $item
     * @param int $start
     * @param int $end
     * @return array
     */
    public function formatTimeTableItem(array $item, int $start, int $end): array
    {
        $scheduleDateKey     = SoapResponseKey::SCHEDULE_DATE_TIME->value;
        $scheduleTimeTypeKey = SoapResponseKey::SCHEDULE_TYPE_OF_TIME->value;

        return [
            "typeOfTimeUid" => !empty($item[$scheduleTimeTypeKey]) ? $item[$scheduleTimeTypeKey] : '',
            "date" => $item[$scheduleDateKey],
            "timeBegin" => date("Y-m-d", $start) ."T". date("H:i:s", $start),
            "timeEnd" => date("Y-m-d", $end) ."T". date("H:i:s", $end),
            "formattedDate" => date("d-m-Y", strtotime($item[$scheduleDateKey])),
            "formattedTimeBegin" => date("H:i", $start),
            "formattedTimeEnd" => date("H:i", $end),
        ];
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array|string[]
     * @throws \Exception
     */
    public function prepareReserveResultData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);

        $commonResKey        = SoapResponseKey::COMMON_RESULT->value;
        $commonErrDescKey    = SoapResponseKey::COMMON_ERROR->value;
        $commonBookingUidKey = SoapResponseKey::UID_RU->value;

        if (key_exists($commonResKey, $xmlArr) && key_exists($commonBookingUidKey, $xmlArr)
            && $xmlArr[$commonResKey] === "true" && !empty($xmlArr[$commonBookingUidKey])
        ){
            return [
                'uid'  => $xmlArr[$commonBookingUidKey]
            ];
        }
        else
        {
            if (key_exists($commonErrDescKey, $xmlArr))
            {
                throw new Exception((string)$xmlArr[$commonErrDescKey]);
            }
            else
            {
                throw new Exception('Unexpected response from 1C - ' . json_encode($xmlArr));
            }
        }
    }

    /**
     * Parse result for add order, delete order and add wait list requests
     * @param \SimpleXMLElement $xml
     * @return array
     * @throws \Exception
     */
    public function prepareCommonResultData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);
        $commonResKey     = SoapResponseKey::COMMON_RESULT->value;
        $commonErrDescKey = SoapResponseKey::COMMON_ERROR->value;

        if ($xmlArr[$commonResKey] === "true"){
            return ['success' => true];
        }
        else {
            throw new Exception((string)$xmlArr[$commonErrDescKey]);
        }
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     * @throws \Exception
     */
    public function prepareStatusResultData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);

        $commonResKey       = SoapResponseKey::COMMON_RESULT->value;
        $commonResDescKey   = SoapResponseKey::COMMON_RESULT_DESCRIPTION->value;
        $commonErrDescKey   = SoapResponseKey::COMMON_ERROR->value;
        $reservedStatusText = SoapResponseKey::STATUS_BOOKED->value;

        if ((int)$xmlArr[$commonResKey] > 0)
        {
            $statusCode  = $xmlArr[$commonResKey];
            $statusTitle = ((int)$statusCode === 9) ? $reservedStatusText : $xmlArr[$commonResDescKey];

            return [
                'statusId'    => $xmlArr[$commonResKey],
                'statusTitle' => (is_array($statusTitle)) ? implode("; ", $statusTitle) : $statusTitle,
            ];
        }
        else {
            throw new Exception($xmlArr[$commonResKey] ." - ". $xmlArr[$commonErrDescKey]);
        }
    }

    /**
     * @param string|null $specialtyName
     * @return string
     */
    protected function getSpecialtyUid(?string $specialtyName): string
    {
        return !empty($specialtyName) ? preg_replace("/[^a-z0-9\s]/", '', strtolower(base64_encode($specialtyName))) : '';
    }

    /**
     * @param array $item
     * @return array
     */
    protected function buildEmployeeData(array $item): array
    {
        $organizationKey = SoapResponseKey::ORGANIZATION->value;
        $nameKey         = SoapResponseKey::NAME->value;
        $lastNameKey     = SoapResponseKey::LAST_NAME->value;
        $middleNameKey   = SoapResponseKey::MIDDLE_NAME->value;
        $photoKey        = SoapResponseKey::PHOTO->value;
        $descriptionKey  = SoapResponseKey::DESCRIPTION->value;
        $specialtyKey    = SoapResponseKey::SPECIALTY->value;
        $servicesKey     = SoapResponseKey::SERVICES->value;
        $oneServiceKey   = SoapResponseKey::MAIN_SERVICE->value;
        $durationKey     = SoapResponseKey::DURATION->value;
        $ratingKey       = SoapResponseKey::RATING->value;
        $serviceUidKey   = SoapResponseKey::UID_EN->value;

        $employee = [];

        $clinicUid = ($item[$organizationKey] == SoapResponseKey::EMPTY_UID->value) ? '' : $item[$organizationKey];
        $uid = is_array($item['UID']) ? current($item['UID']) : $item['UID'];

        $specialtyName = key_exists($specialtyKey, $item) && !empty($item[$specialtyKey])
            ? (string)$item[$specialtyKey]
            : SoapResponseKey::EMPTY_SPECIALTY->value;
        $specialtyUid  = $this->getSpecialtyUid($specialtyName);

        $employee['uid']          = $uid;
        $employee['name']         = key_exists($nameKey, $item) ? $item[$nameKey] : '';
        $employee['surname']      = key_exists($lastNameKey, $item) ? $item[$lastNameKey] : '';
        $employee['middleName']   = key_exists($middleNameKey, $item) ? $item[$middleNameKey] : '';
        $employee['fullName']     = $employee['surname'] ." ". $employee['name'] ." ". $employee['middleName'];
        $employee['clinicUid']    = $clinicUid;
        $employee['photo']        = key_exists($photoKey, $item) ? $item[$photoKey] : '';
        $employee['description']  = key_exists($descriptionKey, $item) && !empty($item[$descriptionKey])
            ? $item[$descriptionKey] : '';
        $employee['rating']       = key_exists($ratingKey, $item) ? $item[$ratingKey] : '';
        $employee['specialtyName']= $specialtyName;
        $employee['specialtyUid'] = $specialtyUid;
        $employee['services']     = [];

        if (key_exists($servicesKey, $item) && is_array($item[$servicesKey])
            && key_exists($oneServiceKey, $item[$servicesKey]) && is_array($item[$servicesKey][$oneServiceKey])
        ){
            foreach ($item[$servicesKey][$oneServiceKey] as $service)
            {
                if (is_array($service) && key_exists($serviceUidKey, $service) && !empty($service[$serviceUidKey]))
                {
                    $employee['services'][$service[$serviceUidKey]] = [
                        'uid'              => $service[$serviceUidKey],
                        'personalDuration' => strtotime($service[$durationKey])-strtotime('0001-01-01T00:00:00')
                    ];
                }
            }
        }
        return $employee;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function buildProductData(array $item): array
    {
        $titleKey       = SoapResponseKey::TITLE->value;
        $typeKey        = SoapResponseKey::TYPE->value;
        $artNumberKey   = SoapResponseKey::ART_NUMBER->value;
        $priceKey       = SoapResponseKey::PRICE->value;
        $durationKey    = SoapResponseKey::DURATION->value;
        $measureUnitKey = SoapResponseKey::MEASURE_UNIT->value;
        $parent         = SoapResponseKey::PARENT->value;

        $product = [];

        $product['uid']         = is_array($item['UID']) ? current($item['UID']) : $item['UID'];
        $product['name']        = $item[$titleKey];
        $product['typeOfItem']  = $item[$typeKey];
        $product['artNumber']   = !empty($item[$artNumberKey]) ? $item[$artNumberKey] : '';
        $product['price']       = str_replace("[^0-9]", '', $item[$priceKey]);
        $product['duration']    = DateFormatter::formatDurationFromIsoToSeconds($item[$durationKey]);
        $product['measureUnit'] = !empty($item[$measureUnitKey]) ? $item[$measureUnitKey] : '';
        $product['parent']      = $item[$parent];

        return $product;
    }
}