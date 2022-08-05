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

use ANZ\BitUmc\SDK\Core\Trait\Singleton;
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

    /**
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function xmlToArray(SimpleXMLElement $xml): array
    {
        return json_decode(json_encode($xml), true);
    }
}