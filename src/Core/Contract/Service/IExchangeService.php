<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 25.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Contract\Service;

use ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel;
use ANZ\BitUmc\SDK\Core\Operation\Result;

interface IExchangeService
{
    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel $requestModel
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getResponse(IRequestModel $requestModel): Result;
}