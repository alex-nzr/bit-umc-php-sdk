<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * bit-umc-php-sdk - SoapResponseKey.php
 * 28.11.2023 23:49
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\Dictionary;

/**
 * @enum SoapResponseKey
 * @package ANZ\BitUmc\SDK\Core\Dictionary
 */
enum SoapResponseKey: string
{
    case EMPTY_SPECIALTY = 'Без основной специализации';
    case EMPTY_UID = "00000000-0000-0000-0000-000000000000";

    case TITLE  = 'Наименование';
    case UID_RU = 'УИД';
    case UID_EN = 'UID';
    case CLINIC = 'Клиника';

    case EMPLOYEE     = "Сотрудник";
    case ORGANIZATION = "Организация";
    case NAME         = "Имя";
    case LAST_NAME    = "Фамилия";
    case MIDDLE_NAME  = "Отчество";
    case PHOTO        = "Фото";
    case DESCRIPTION  = "КраткоеОписание";
    case SPECIALTY    = "Специализация";
    case SERVICES     = "ОсновныеУслуги";
    case MAIN_SERVICE = "ОсновнаяУслуга";
    case DURATION     = "Продолжительность";
    case RATING       = "СреднийРейтинг";

    case CATALOG      = "Каталог";
    case IS_FOLDER    = "ЭтоПапка";
    case TYPE         = "Вид";
    case ART_NUMBER   = "Артикул";
    case PRICE        = "Цена";
    case MEASURE_UNIT = "БазоваяЕдиницаИзмерения";
    case PARENT       = "Родитель";

    case SCHEDULE              = 'ГрафикДляСайта';
    case SCHEDULE_ERROR        = 'ОшибкаПараметров';
    case EMPLOYEE_UID          = "СотрудникID";
    case EMPLOYEE_FULL_NAME    = "СотрудникФИО";
    case SCHEDULE_DURATION     = "ДлительностьПриема";
    case SCHEDULE_PERIODS      = "ПериодыГрафика";
    case SCHEDULE_PERIOD       = "ПериодГрафика";
    case SCHEDULE_FREE_TIME    = "СвободноеВремя";
    case SCHEDULE_BUSY_TIME    = "ЗанятоеВремя";
    case SCHEDULE_DATE_TIME    = "Дата";
    case SCHEDULE_START_TIME   = "ВремяНачала";
    case SCHEDULE_END_TIME     = "ВремяОкончания";
    case SCHEDULE_TYPE_OF_TIME = 'ВидВремени';

    case COMMON_RESULT = 'Результат';
    case COMMON_ERROR  = 'ОписаниеОшибки';
    case COMMON_RESULT_DESCRIPTION = 'ОписаниеРезультата';
    case STATUS_BOOKED = 'Забронирована';
}
