<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - GetReserve.php
 * 29.11.2023 20:48
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Model\Request\Soap;

use ANZ\BitUmc\SDK\Item\Order;

/**
 * @class GetReserve
 * @package ANZ\BitUmc\SDK\Core\Model\Request\Soap
 */
class GetReserve extends BaseEntity
{
    protected string $Specialization;
    protected string $Date;
    protected string $TimeBegin;
    protected string $EmployeeID;
    protected string $Clinic;

    /**
     * GetReserve constructor
     * @param \ANZ\BitUmc\SDK\Item\Order $reserve
     */
    public function __construct(Order $reserve)
    {
        $this->Specialization = $reserve->getSpecialtyName();
        $this->Date           = $reserve->getDate();
        $this->TimeBegin      = $reserve->getTimeBegin();
        $this->EmployeeID     = $reserve->getEmployeeUid();
        $this->Clinic         = $reserve->getClinicUid();
    }
}