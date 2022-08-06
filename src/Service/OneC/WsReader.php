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
use Exception;

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
        if (!$this->isWsScope())
        {
            return $this->createScopeError();
        }
        return $this->getResponse(SoapMethod::CLINIC_ACTION_1C);
    }

    /**
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getEmployees(): Result
    {
        if (!$this->isWsScope())
        {
            return $this->createScopeError();
        }
        return $this->getResponse(SoapMethod::EMPLOYEES_ACTION_1C);
    }
}