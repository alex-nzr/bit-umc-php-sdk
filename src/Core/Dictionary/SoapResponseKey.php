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

    case CLINIC       = 'Клиника';
    case CLINIC_TITLE = 'Наименование';
    case CLINIC_UID   = 'УИД';

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
}
