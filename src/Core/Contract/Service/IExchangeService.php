<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - IService.php
 * 25.11.2023 00:41
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Contract\Service;

use ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel;
use ANZ\BitUmc\SDK\Core\Operation\Result;

/**
 * @interface IService
 * @package ANZ\BitUmc\SDK\Core\Contract\OneC
 */
interface IExchangeService
{
    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel $requestModel
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getResponse(IRequestModel $requestModel): Result;
}