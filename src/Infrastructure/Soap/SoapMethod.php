<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap;

enum SoapMethod: string
{
    case GET_CLINICS = 'GetListClinic';
    case GET_EMPLOYEES = 'GetListEmployees';
    case GET_NOMENCLATURE = 'GetNomenclatureAndPrices';
    case GET_SCHEDULE = 'GetSchedule20';
    case CREATE_APPOINTMENT = 'BookAnAppointmentWithParams';
    case DELETE_APPOINTMENT = 'CancelBookAnAppointment';
    case CREATE_WAIT_LIST = 'FastBookAnAppointment';
    case CREATE_RESERVE = 'GetReserve';
    case GET_APPOINTMENT_STATUS = 'GetAppointmentStatus';
}
