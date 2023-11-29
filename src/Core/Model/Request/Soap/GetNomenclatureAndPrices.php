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
    protected string $Clinic;

    /**
     * GetNomenclatureAndPrices constructor
     * @param string $clinicGuid
     */
    public function __construct(string $clinicGuid)
    {
        $this->Clinic = $clinicGuid;
    }
}