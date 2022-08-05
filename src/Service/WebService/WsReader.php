<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Reader.php
 * 04.08.2022 01:43
 * ==================================================
 */


namespace ANZ\BitUmc\SDK\Service\OneC;

use ANZ\BitUmc\SDK\Core\Contract\Result;
use ANZ\BitUmc\SDK\Core\Soap\SoapMethod;

/**
 * Class Reader
 * @package ANZ\BitUmc\SDK\Service\OneC
 */
class Reader extends BaseService
{
    /**
     * @return \ANZ\BitUmc\SDK\Core\Soap\SoapResult
     */
    public function getClinics(): Result
    {
        return $this->getResponse(SoapMethod::CLINIC_ACTION_1C);
    }
}