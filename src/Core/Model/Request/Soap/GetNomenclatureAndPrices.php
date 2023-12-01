<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - GetNomenclatureAndPrices.php
 * 29.11.2023 20:38
 * ==================================================
 */


namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

/**
 * @class GetNomenclatureAndPrices
 * @package ANZ\BitUmc\SDK\Core\Model\Request\Soap
 */
class GetNomenclatureAndPrices extends BaseEntity
{
    /**
     * GetNomenclatureAndPrices constructor
     * @param string $Clinic
     */
    public function __construct(protected string $Clinic)
    {
    }
}