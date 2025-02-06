<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 29.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

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