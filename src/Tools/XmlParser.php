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

namespace ANZ\BitUmc\SDK\Tools;

use ANZ\BitUmc\SDK\Core\Reusable\Singleton;
use SimpleXMLElement;

/**
 * Class XmlParser
 * @package ANZ\BitUmc\SDK\Tools
 * @method static XmlParser getInstance()
 */
class XmlParser
{
    use Singleton;

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function prepareClinicData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);

        $clinicKey      = "Клиника";
        $clinicTitleKey = "Наименование";
        $clinicUidKey   = "УИД";

        $clinics = [];
        if (is_array($xmlArr[$clinicKey]))
        {
            if (Utils::is_assoc($xmlArr[$clinicKey]))
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

    public function prepareEmployeesData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);

        $employeeKey     = "Сотрудник";
        $organizationKey = "Организация";
        $nameKey         = "Имя";
        $lastNameKey     = "Фамилия";
        $middleNameKey   = "Отчество";
        $photoKey        = "Фото";
        $descriptionKey  = "КраткоеОписание";
        $specialtyKey    = "Специализация";
        $servicesKey     = "ОсновныеУслуги";
        $oneServiceKey   = "ОсновнаяУслуга";
        $durationKey     = "Продолжительность";
        $ratingKey       = "СреднийРейтинг";

        $employees = [];
        if (is_array($xmlArr[$employeeKey]))
        {
            foreach ($xmlArr[$employeeKey] as $item)
            {
                $employee = [];
                $clinicUid = ($item[$organizationKey] == "00000000-0000-0000-0000-000000000000") ? "" : $item[$organizationKey];
                $uid = is_array($item['UID']) ? current($item['UID']) : $item['UID'];

                $employee['uid']          = $uid;
                $employee['name']         = $item[$nameKey];
                $employee['surname']      = $item[$lastNameKey];
                $employee['middleName']   = $item[$middleNameKey];
                $employee['fullName']     = $item[$lastNameKey] ." ". $item[$nameKey] ." ". $item[$middleNameKey];
                $employee['clinicUid']    = $clinicUid;
                $employee['photo']        = $item[$photoKey];
                $employee['description']  = $item[$descriptionKey];
                $employee['rating']       = $item[$ratingKey];
                $employee['specialtyName']= $item[$specialtyKey];
                $employee['specialtyUid'] = $this->getSpecialtyUid($item[$specialtyKey]);
                $employee['services']     = [];

                if (is_array($item[$servicesKey][$oneServiceKey]))
                {
                    foreach ($item[$servicesKey][$oneServiceKey] as $service)
                    {
                        if (!empty($service['UID']))
                        {
                            $employee['services'][$service['UID']] = [
                                'uid'              => $service['UID'],
                                'personalDuration' => strtotime($service[$durationKey])-strtotime('0001-01-01T00:00:00')
                            ];
                        }
                    }
                }

                $employees[$uid] = $employee;
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

        $catalogKey     = "Каталог";
        $isFolderKey    = "ЭтоПапка";
        $titleKey       = "Наименование";
        $typeKey        = "Вид";
        $artNumberKey   = "Артикул";
        $priceKey       = "Цена";
        $durationKey    = "Продолжительность";
        $measureUnitKey = "БазоваяЕдиницаИзмерения";
        $parent         = "Родитель";

        $nomenclature = [];
        if (is_array($xmlArr[$catalogKey]))
        {
            foreach ($xmlArr[$catalogKey] as $item)
            {
                if ($item[$isFolderKey] === true){
                    continue;
                }
                $uid = is_array($item['UID']) ? current($item['UID']) : $item['UID'];

                $product = [];
                $product['uid']         = $uid;
                $product['name']        = $item[$titleKey];
                $product['typeOfItem']  = $item[$typeKey];
                $product['artNumber']   = $item[$artNumberKey];
                $product['price']       = str_replace("[^0-9]", '', $item[$priceKey]);
                $product['duration']    = DateTime::formatDurationFromIsoToSeconds($item[$durationKey]);
                $product['measureUnit'] = $item[$measureUnitKey];
                $product['parent']      = $item[$parent];
                $nomenclature[$uid]     = $product;
            }
        }
        return $nomenclature;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function xmlToArray(SimpleXMLElement $xml): array
    {
        return json_decode(json_encode($xml), true);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function prepareScheduleData(SimpleXMLElement $xml): array
    {
        $xmlArr = $this->xmlToArray($xml);
        $scheduleKey = 'ГрафикДляСайта';

        $schedule = [];
        if (is_array($xmlArr[$scheduleKey])){
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
        if (Utils::is_assoc($schedule))
        {
            $schedule = [$schedule];
        }

        $employeeUidKey       = "СотрудникID";
        $employeeFullNameKey  = "СотрудникФИО";
        $scheduleDurationKey  = "ДлительностьПриема";
        $schedulePeriodsKey   = "ПериодыГрафика";
        $scheduleOnePeriodKey = "ПериодГрафика";
        $scheduleFreeTimeKey  = "СвободноеВремя";
        $scheduleBusyTimeKey  = "ЗанятоеВремя";
        $specialtyKey         = "Специализация";
        $clinicKey            = "Клиника";

        $formattedSchedule = [];
        foreach ($schedule as $item)
        {
            if (!empty($item[$clinicKey]))
            {
                $clinicUid = $item[$clinicKey];
                if (!is_array($formattedSchedule[$clinicUid]))
                {
                    $formattedSchedule[$clinicUid] = [];
                }

                if (!empty($item[$specialtyKey])){
                    $specialtyName = $item[$specialtyKey];
                    $specialtyUid  = $this->getSpecialtyUid($item[$specialtyKey]);

                    if (!is_array($formattedSchedule[$clinicUid][$specialtyUid]))
                    {
                        $formattedSchedule[$clinicUid][$specialtyUid] = [];
                    }

                    if (!empty($item[$employeeUidKey]))
                    {
                        $employeeUid     = $item[$employeeUidKey];
                        $employeeName    = $item[$employeeFullNameKey];

                        $durationSeconds = 1800;//default duration = 30min
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

                        if (Utils::is_assoc($freeTime)) {
                            $freeTime = [$freeTime];
                        }
                        if (Utils::is_assoc($busyTime)) {
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
        }

        return $formattedSchedule;
    }

    public function formatTimetable($array, int $duration, $useDefaultInterval = false): array
    {
        if (!is_array($array) || empty($array)){
            return [];
        }

        if (!$duration > 0){
            $duration = 1800;
        }

        if (!empty($array))
        {
            if (Utils::is_assoc($array)) {
                $array = [$array];
            }

            $scheduleDateKey  = "Дата";
            $scheduleStartKey = "ВремяНачала";
            $scheduleEndKey   = "ВремяОкончания";

            $formattedArray = [];
            foreach ($array as $item)
            {
                $timestampTimeBegin = strtotime($item[$scheduleStartKey]);
                $timestampTimeEnd = strtotime($item[$scheduleEndKey]);

                if ($useDefaultInterval)
                {
                    $newTimeTableItem = $this->formatTimeTableItem($item, (int)$timestampTimeBegin, (int)$timestampTimeEnd);
                    if (!is_array($formattedArray[$item[$scheduleDateKey]]))
                    {
                        $formattedArray[$item[$scheduleDateKey]] = [$newTimeTableItem];
                    }
                    else
                    {
                        $formattedArray[$item[$scheduleDateKey]][] = $newTimeTableItem;
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
                        if (!is_array($formattedArray[$item[$scheduleDateKey]]))
                        {
                            $formattedArray[$item[$scheduleDateKey]] = [$newTimeTableItem];
                        }
                        else
                        {
                            $formattedArray[$item[$scheduleDateKey]][] = $newTimeTableItem;
                        }
                    }
                }
            }
            return $formattedArray;
        }
        else
        {
            return [];
        }
    }
    public function formatTimeTableItem(array $item, int $start, int $end): array
    {
        $scheduleDateKey     = "Дата";
        $scheduleTimeTypeKey = "ВидВремени";

        return [
            "typeOfTimeUid" => $item[$scheduleTimeTypeKey],
            "date" => $item[$scheduleDateKey],
            "timeBegin" => date("Y-m-d", $start) ."T". date("H:i:s", $start),
            "timeEnd" => date("Y-m-d", $end) ."T". date("H:i:s", $end),
            "formattedDate" => date("d-m-Y", strtotime($item[$scheduleDateKey])),
            "formattedTimeBegin" => date("H:i", $start),
            "formattedTimeEnd" => date("H:i", $end),
        ];
    }

    /**
     * @param string|null $specialtyName
     * @return string
     */
    protected function getSpecialtyUid(?string $specialtyName): string
    {
        return !empty($specialtyName) ? base64_encode($specialtyName) : '';
    }
}