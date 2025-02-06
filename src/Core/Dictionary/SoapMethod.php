<?php
/*
 * ==================================================
 * This file is part of project bit-umc-php-sdk
 * 26.11.2023
 * ==================================================
*/
namespace ANZ\BitUmc\SDK\Core\Dictionary;

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