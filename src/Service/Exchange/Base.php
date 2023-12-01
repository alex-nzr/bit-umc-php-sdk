<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - Common.php
 * 04.08.2022 01:16
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Service\Exchange;

use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Contract\Service\IExchangeService;
use ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel;
use ANZ\BitUmc\SDK\Core\Operation\Result;

/**
 * Class Common
 * @package ANZ\BitUmc\SDK\Service\OneC
 */
abstract class Base implements IExchangeService
{
    protected IClient $client;

    /**
     * Base constructor
     * @param \ANZ\BitUmc\SDK\Core\Contract\Connection\IClient $client
     */
    public function __construct(IClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Model\IRequestModel $requestModel
     * @return \ANZ\BitUmc\SDK\Core\Operation\Result
     */
    public function getResponse(IRequestModel $requestModel): Result
    {
        return $this->client->send($requestModel);
    }
}