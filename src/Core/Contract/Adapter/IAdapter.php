<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - IAdapter.php
 * 25.11.2023 00:30
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Contract\Adapter;

/**
 * @interface IAdapter
 * @package ANZ\BitUmc\SDK\Core\Contract\Adapter
 */
interface IAdapter
{
    /**
     * @param \ANZ\BitUmc\SDK\Core\Contract\Adapter\IAdaptableItem $item
     * @return array
     */
    public function adapt(IAdaptableItem $item): array;
}