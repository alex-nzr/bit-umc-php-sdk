<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - SoapMethod.php
 * 26.11.2023 18:44
 * ==================================================
 */
namespace ANZ\BitUmc\SDK\Core\Dictionary;

/**
 * @enum SoapMethod
 * @package ANZ\BitUmc\SDK\Core\Dictionary
 */
enum SoapMethod: string
{
    case CLINIC_ACTION_1C           = "GetListClinic";
    case EMPLOYEES_ACTION_1C        = "GetListEmployees";
    case NOMENCLATURE_ACTION_1C     = "GetNomenclatureAndPrices";
    case SCHEDULE_ACTION_1C         = "GetSchedule20";
    case CREATE_ORDER_ACTION_1C     = "BookAnAppointmentWithParams";
    case DELETE_ORDER_ACTION_1C     = "CancelBookAnAppointment";
    case CREATE_WAIT_LIST_ACTION_1C = "FastBookAnAppointment";
    case CREATE_RESERVE_ACTION_1C   = "GetReserve";
    case GET_ORDER_STATUS_ACTION_1C = "GetAppointmentStatus";
}