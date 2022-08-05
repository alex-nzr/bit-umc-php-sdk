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


namespace ANZ\BitUmc\SDK\Service\WebService;

use ANZ\BitUmc\SDK\Core\Contract\Result;
use ANZ\BitUmc\SDK\Core\Soap\SoapMethod;

/**
 * Class WsReader
 * @package ANZ\BitUmc\SDK\Service\WebService
 */
class WsReader extends WsCommon
{
    /**
     * @return \ANZ\BitUmc\SDK\Core\Soap\SoapResult
     */
    public function getClinics(): Result
    {
        return $this->getResponse(SoapMethod::CLINIC_ACTION_1C);
    }
}