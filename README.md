# SDK по работе с SOAP-API 1C БИТ.УМЦ


## Требования:
`PHP >= 7.4`

`BIT.UMC >=2.0.48.15(Prof)`

`BIT.UMC >=2.1.24.9(Corp)`


## Установка

### Установка через composer
Добавить пакет в зависимости `composer.json`:
```
{
    "require":{"alex-nzr/bit-umc-sdk":"dev-master"}
}
```

Запустить установку [composer](https://getcomposer.org/doc/00-intro.md#installation):
```
$ php composer.phar install
```
или
```
composer require alex-nzr/bit-umc-sdk:dev-master
```

### Скачать исходники

[Актуальная версия](https://github.com/alex-nzr/bit-umc-php-sdk/archive/refs/heads/master.zip),
распаковать и скопировать в папку с проектом.

## Использование

### Публикация базы 1С
Для работы интеграции база 1С должна быть опубликована на веб-сервере
и иметь возможность принимать запросы с внешних ресурсов.


### API
Для начала работы с API, нужно создать экземпляр клиента и передать ему необходимые конфигурационные данные.

Ниже образец. Логин пользователя 1С -  `1cUser`, пароль -  `1cUserPassword`. 
База 1С опубликована на веб-сервере и имеет адрес `http://88.29.123.512:3500/umc/`.
```
    use ANZ\BitUmc\SDK\Service\Builder\ClientBuilder;

    $client = ClientBuilder::init()
                ->setLogin('1cUser')
                ->setPassword('1cUserPassword')
                ->setHttps(false)
                ->setAddress('88.29.123.512:3500')
                ->setBaseName('umc')
                ->build();
```
_Также ClientBuilder имеет метод setHsScope() для подключения не по soap, а по http.
Но данная возможность ещё не реализована полностью
и большинство классов представляют собой заглушки, 
так как http сервисы в БИТ.УМЦ слабо развиты
(на момент написания этой инструкции имеются сервисы для интеграции
с Битрикс24 и ЕГИСЗ, а также сервис для создания лида (лист ожидания))._

Следующий шаг — получение сервисов для чтения и записи информации.
```
    use ANZ\BitUmc\SDK\Service\Factory\ServiceFactory;
    use ANZ\BitUmc\SDK\Tools\Debug;

    $factory = new ServiceFactory($client);
    $reader  = $factory->getReader();
    $writer  = $factory->getWriter();
```

После этого можно начинать "общение" с 1С.

### Список клиник
```
    $result = $reader->getClinics();
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [4c68deb4-22c3-11df-8618-002618dcef2c] => Array
                (
                    [uid] => 4c68deb4-22c3-11df-8618-002618dcef2c
                    [name] => Второй центр
                )
        
            [66abf7b4-2ff9-11df-8625-002618dcef2c] => Array
                (
                    [uid] => 66abf7b4-2ff9-11df-8625-002618dcef2c
                    [name] => Третий центр
                )
        
            [f679444a-22b7-11df-8618-002618dcef2c] => Array
                (
                    [uid] => f679444a-22b7-11df-8618-002618dcef2c
                    [name] => Центральная клиника
                )
        )
        */
    }
    else
    {
        Debug::print($result->getErrorMessages());
        /*
        Array
        (
            [0] => some error-1
            [1] => some error-2
        )
        */
    }
```

Обработка неуспешного ответа во всех методах происходит аналогично
примеру выше, поэтому в последующих примерах рассматриваться не будет.

### Список сотрудников
***Примечание:*** *уникальный id специализации генерируется в рантайме
из её названия, так как нет возможности получить его из 1С по имеющемуся API.
Также возвращается только специализация, выбранная основной. 
Получение дополнительных специализаций в API БИТ.УМЦ не предусмотрено. 
В случае, когда основная специализация не указана, 
у сотрудника в соответствующей графе
будет указано "Без основной специализации"*
```
    $result = $reader->getEmployees();
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [2eb1f97b-6a3c-11e9-936d-1856809fe650] => Array
            (
                [uid] => 2eb1f97b-6a3c-11e9-936d-1856809fe650
                [name] => Юрий
                [surname] => Безногов
                [middleName] => Сергеевич
                [fullName] => Безногов Юрий Сергеевич
                [clinicUid] => f679444a-22b7-11df-8618-002618dcef2c
                [photo] => "base64_encoded_photo"
                [description] => 'Краткое описание из 1с'
                [rating] => 
                [specialtyName] => Офтальмология
                [specialtyUid] => 0j7rhngc0ldqu9gm0lzqvtc70l7qs9c40y8
                [services] => Array
                    (
                        [5210c9dc-65a2-11e9-936d-1856809fe650] => Array
                            (
                                [uid] => 5210c9dc-65a2-11e9-936d-1856809fe650
                                [personalDuration] => 0
                            )
    
                        [dc58bfa0-65b4-11e9-936d-1856809fe650] => Array
                            (
                                [uid] => dc58bfa0-65b4-11e9-936d-1856809fe650
                                [personalDuration] => 0
                            )
                    )
            )
        )
        */
    }
```

### Номенклатура
```
    $clinicUid  = 'f679444a-22b7-11df-8618-002618dcef2c';
    $result     = $reader->getNomenclature($clinicUid);
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [a0230570-3ef7-11de-8086-001485c0d477] => Array
            (
                [uid] => a0230570-3ef7-11de-8086-001485c0d477
                [name] => Массаж век
                [typeOfItem] => Услуга
                [artNumber] => 
                [price] => 160
                [duration] => 1800
                [measureUnit] => мин
                [parent] => 5210c9bf-65a2-11e9-936d-1856809fe650
            )
    
            [a0230571-3ef7-11de-8086-001485c0d477] => Array
            (
                [uid] => a0230571-3ef7-11de-8086-001485c0d477
                [name] => Гель для массажа
                [typeOfItem] => Материал
                [artNumber] => K-101-55
                [price] => 100
                [duration] => 0
                [measureUnit] => мл
                [parent] => 85812f74-1cc9-11dc-b7bd-000461ac6871
            )
        )
        */
```

### Расписание
***Примечание:*** *уникальный id специализации генерируется в рантайме
из её названия, так как нет возможности получить его из 1С по имеющемуся API*

Ни один из параметров метода не является обязательным. 
По умолчанию возвращается расписание по всем врачам всех клиник за 14 дней.

Структура возвращаемого массива следующая:

**Ключи первого уровня** — уникальные идентификаторы клиник. 

**Ключи второго уровня** — уникальные идентификаторы специализаций доступных в данной клинике.

**Ключи третьего уровня** — уникальные идентификаторы врачей, 
для которых сформировано расписание в вышестоящей клинике
и вышестоящая специализация выбрана основной в настройках 1С.

В массиве данных по врачу есть его ФИО, название специализации,
длительность приёма из настроек 1С (durationFrom1C), 
длительность приёма из 1С, переведённая в секунды (durationInSeconds)
и расписание(timetable).

Расписание имеет три ключа:

`free` - общее свободное время без разбивки на интервалы длительности приёма. 
Может быть разделено на части, если есть занятые участками времени. 
В противном случае будет представлять собой один интервал,
равный длине рабочего дня соответствующего сотрудника.

`busy` - занятое время.

`freeFormatted` - свободное время, разбитое на 
интервалы длительности приёма из 1С (durationFrom1C), 
либо на интервалы по 30 минут, если данных по длительности не получено.

В каждый из этих трёх разделов вложены массивы 
под ключами в виде даты в формате "d-m-Y". 
Внутри, соответственно, временные отрезки относящиеся к данному дню.

`typeOfTimeUid` - идентификатор вида времени графика. Кроме идентификатора, никакой информации о нём не предоставляется.
```
    $period       = 21;//days
    $clinicUid    = 'f679444a-22b7-11df-8618-002618dcef2c';
    $employeeUids = [
        '19cb6fa5-1578-11ed-9bee-5c3a455eb0d0', 
        '99868528-0928-11dc-93d1-0004614ae652',
        '2eb1f97b-6a3c-11e9-936d-1856809fe650'
    ];
    
    $res = $reader->getSchedule($period, $clinicUid, $employeUids);
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [f679444a-22b7-11df-8618-002618dcef2c] => Array
                (
                    [0klqtdga0ldqv9c40y8] => Array
                        (
                            [99868528-0928-11dc-93d1-0004614ae652] => Array
                                (
                                    [specialtyName] => Терапия
                                    [employeeName] => Денисов Дмитрий Алексеевич
                                    [durationFrom1C] => 0001-01-01T00:00:00
                                    [durationInSeconds] => 0
                                    [timetable] => Array
                                        (
                                            [free] => Array
                                                (
                                                    [21-09-2022] => Array
                                                        (
                                                            [0] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T08:00:00
                                                                    [timeEnd] => 2022-09-21T12:00:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 08:00
                                                                    [formattedTimeEnd] => 12:00
                                                                )
        
                                                        )
        
                                                )
        
                                            [busy] => Array
                                                (
                                                )
        
                                            [freeFormatted] => Array
                                                (
                                                    [21-09-2022] => Array
                                                        (
                                                            [0] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T08:00:00
                                                                    [timeEnd] => 2022-09-21T08:30:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 08:00
                                                                    [formattedTimeEnd] => 08:30
                                                                )
        
                                                            [1] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T08:30:00
                                                                    [timeEnd] => 2022-09-21T09:00:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 08:30
                                                                    [formattedTimeEnd] => 09:00
                                                                )
        
                                                            [2] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T09:00:00
                                                                    [timeEnd] => 2022-09-21T09:30:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 09:00
                                                                    [formattedTimeEnd] => 09:30
                                                                )
        
                                                            ...
        
                                                        )
        
                                                )
        
                                        )
        
                                )
        
                        )
        
                    [0j7rhngc0ldqu9gm0lzqvtc70l7qs9c40y8] => Array
                        (
                            [ac30e13a-3087-11dc-8594-005056c00008] => Array
                                (
                                    [specialtyName] => Офтальмология
                                    [employeeName] => Барбышева Евгения Петровна
                                    [durationFrom1C] => 0001-01-01T00:00:00
                                    [durationInSeconds] => 0
                                    [timetable] => Array
                                        (
                                            [free] => Array
                                                (
                                                    [21-09-2022] => Array
                                                        (
                                                            [0] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T12:00:00
                                                                    [timeEnd] => 2022-09-21T14:00:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 12:00
                                                                    [formattedTimeEnd] => 14:00
                                                                )
        
                                                            [1] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T15:00:00
                                                                    [timeEnd] => 2022-09-21T16:00:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 15:00
                                                                    [formattedTimeEnd] => 16:00
                                                                )
        
                                                        )
        
                                                )
        
                                            [busy] => Array
                                                (
                                                    [21-09-2022] => Array
                                                        (
                                                            [0] => Array
                                                                (
                                                                    [typeOfTimeUid] => 
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T14:00:00
                                                                    [timeEnd] => 2022-09-21T15:00:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 14:00
                                                                    [formattedTimeEnd] => 15:00
                                                                )
        
                                                        )
        
                                                )
        
                                            [freeFormatted] => Array
                                                (
                                                    [21-09-2022] => Array
                                                        (
                                                            [0] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T12:00:00
                                                                    [timeEnd] => 2022-09-21T12:30:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 12:00
                                                                    [formattedTimeEnd] => 12:30
                                                                )
        
                                                            [1] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T12:30:00
                                                                    [timeEnd] => 2022-09-21T13:00:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 12:30
                                                                    [formattedTimeEnd] => 13:00
                                                                )
        
                                                            [2] => Array
                                                                (
                                                                    [typeOfTimeUid] => 624f2a40-5aa8-4f01-83f4-0f38535364bb
                                                                    [date] => 2022-09-21T00:00:00
                                                                    [timeBegin] => 2022-09-21T13:00:00
                                                                    [timeEnd] => 2022-09-21T13:30:00
                                                                    [formattedDate] => 21-09-2022
                                                                    [formattedTimeBegin] => 13:00
                                                                    [formattedTimeEnd] => 13:30
                                                                )
        
                                                            ...
        
                                                        )
        
                                                )
        
                                        )
        
                                )
        
                        )
        
                )
        
        )
        */
```

### Статус заявки
```
    $orderUid  = '39d9b2f9-35db-11ed-9bf2-5e3a455eb0cf';
    $result     = $reader->getOrderStatus($orderUid);
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [statusId] => 1
            [status] => Новая
        )
        */
```

### Создание листа ожидания
Для конфигурирования параметров заявок используется отдельный класс OrderBuilder
```
    use ANZ\BitUmc\SDK\Service\Builder\OrderBuilder;
    
    //В качестве даты и времени записи передаётся php-объект \DateTime, созданный любым удобным способом
    $dateTimeBegin = \DateTime::createFromFormat("d.m.Y H:i:s", "21.09.2022 14:00:00");
    
    $clinicUid   = 'f679444a-22b7-11df-8618-002618dcef2c';
    
    $waitList   = OrderBuilder::createWaitList()
        ->setSpecialtyName('Стоматология')
        ->setName('Иван')
        ->setLastName('Иванов')
        ->setSecondName('Иванович')
        ->setDateTimeBegin($dateTimeBegin)
        ->setPhone("+7 (915) 5415935")
        ->setEmail('example@gmail.com')
        ->setAddress('г. Москва, проспект Ленина 45')
        ->setClinicUid($clinicUid)
        ->setComment('Comment text')
        ->build();

    $result = $writer->sendWaitList($waitList);
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [success] => true
        )
        */
```

### Бронирование времени
По сути — это такое же создание заявки, как и при записи на приём,
но требуется меньшее количество параметров, 
статус заявки в 1С будет "Забронировано" и в ответе придёт
уникальный идентификатор созданной заявки, который можно использовать
для её обновления или удаления. 
При создании полноценной заявки (Запись на приём), API БИТ.УМЦ 
не возвращает её идентификатор. Поэтому, если нужно где-то хранить
созданные заявки и в дальнейшем как-то использовать, то запись нужно
производить с предварительным бронированием.
```
    use ANZ\BitUmc\SDK\Service\Builder\OrderBuilder;
    
    //В качестве даты и времени записи передаётся php-объект \DateTime, созданный любым удобным способом
    $dateTimeBegin = \DateTime::createFromFormat("d.m.Y H:i:s", "21.09.2022 14:00:00");
    
    $clinicUid   = 'f679444a-22b7-11df-8618-002618dcef2c';
    $employeeUid = '19cb6fa5-1578-11ed-9bee-5c3a455eb0d0';
    
    $reserve   = OrderBuilder::createReserve()
        ->setClinicUid($clinicUid)
        ->setSpecialtyName('Стоматология')
        ->setEmployeeUid($employeeUid)
        ->setDateTimeBegin($dateTimeBegin)
        ->build();
    $res = $writer->sendReserve($reserve);
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [uid] => 54cd6a89-3912-11ed-9bf2-5e3a455eb0cf
        )
        */
```

### Запись на приём
```
    use ANZ\BitUmc\SDK\Service\Builder\OrderBuilder;
    
    //В качестве даты и времени записи передаётся php-объект \DateTime, созданный любым удобным способом
    $dateTimeBegin = \DateTime::createFromFormat("d.m.Y H:i:s", "21.09.2022 14:00:00");
    
    $clinicUid   = 'f679444a-22b7-11df-8618-002618dcef2c';
    $employeeUid = '19cb6fa5-1578-11ed-9bee-5c3a455eb0d0';
    $serviceUid1 = '22d1b486-b34b-11de-8171-001583078ee5';
    $serviceUid2 = '24d1b331-b562-11de-8133-001583078ee6';
    
    $clientBirthday = \DateTime::createFromFormat("d.m.Y", "05.08.1962");
    
    $order   = OrderBuilder::createOrder()
        ->setEmployeeUid($employeeUid)
        ->setName('Антон')
        ->setLastName('Печкин')
        ->setSecondName('Павлович')
        ->setDateTimeBegin($dateTimeBegin)
        ->setPhone("+79000803126")
        ->setEmail('ppp@gmail.com')
        ->setAddress('г. Москва, проспект Ленина 46')
        ->setClinicUid($clinicUid)
        ->setComment('Comment text')
        ->setServices( [ $serviceUid1, $serviceUid2 ] )
        ->setClientBirthday($clientBirthday)
        //переданная длительность не учитывается если указаны услуги (setServices)
        ->setAppointmentDuration(2700)
        //если не устанавливать идентификатор или передать пустую строку, то создастся новая заявка, иначе будет изменена старая
        ->setOrderUid('39d9b2f9-35db-11ed-9bf2-5e3a455eb0cf')
        ->build();

    $res = $writer->sendOrder($order);
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [success] => true
        )
        */
```

### Удаление заявки
```
    $orderUid  = '39d9b2f9-35db-11ed-9bf2-5e3a455eb0cf';
    $result     = $writer->deleteOrder($orderUid);
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            [success] => true
        )
        */
```


## Примеры работы
На данный момент можно только ознакомиться с примерами вызова методов
[тут](https://github.com/alex-nzr/bit-umc-php-sdk/tree/master/examples).

В будущем, возможно, будут полноценные примеры, включающие в себя и фронтенд.