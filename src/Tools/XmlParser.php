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
                $employee['specialty']    = $item[$specialtyKey];
                $employee['specialtyUid'] = !empty($item[$specialtyKey]) ? base64_encode($item[$specialtyKey]) : '';
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
                $product['duration']    = Utils::formatDurationToSeconds($item[$durationKey]);
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
}