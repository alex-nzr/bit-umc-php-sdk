<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - IAdaptableItem.php
 * 25.11.2023 00:31
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Contract\Adapter;

/**
 * @interface IAdaptableItem
 * @package ANZ\BitUmc\SDK\Core\Contract\Adapter
 */
interface IAdaptableItem
{
    /**
     * @return array
     */
    public function getCompatibleData(): array;

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static;
}