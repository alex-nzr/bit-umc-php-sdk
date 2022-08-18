<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - SoapMethod.php
 * 04.08.2022 22:16
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Soap;

/**
 * Class SoapMethod
 * @package ANZ\BitUmc\SDK\Core\Soap
 */
class SoapMethod
{
    const CLINIC_ACTION_1C           = "GetListClinic";
    const EMPLOYEES_ACTION_1C        = "GetListEmployees";
    const NOMENCLATURE_ACTION_1C     = "GetNomenclatureAndPrices";
    const SCHEDULE_ACTION_1C         = "GetSchedule20";
    const CREATE_ORDER_ACTION_1C     = "BookAnAppointmentWithParams";
    const DELETE_ORDER_ACTION_1C     = "CancelBookAnAppointment";
    const CREATE_WAIT_LIST_ACTION_1C = "FastBookAnAppointment";
    const CREATE_RESERVE_ACTION_1C   = "GetReserve";
    const GET_ORDER_STATUS_ACTION_1C = "GetAppointmentStatus";
}