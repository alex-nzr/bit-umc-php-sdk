# Project in development
**Добавить в расписание врачей без специализации

##Requirements:
`PHP >= 7.4`

`BIT.UMC >=2.0.48.15(Prof)`

`BIT.UMC >=2.1.24.9(Corp)`


## Installation

### Install composer package
Set up `composer.json` in your project directory:
```
{
    "require":{"alex-nzr/bit-umc-php-sdk":"dev-master"}
}
```

Run [composer](https://getcomposer.org/doc/00-intro.md#installation):
```
$ php composer.phar install
```
or
```
composer require alex-nzr/bit-umc-php-sdk:dev-master
```

### Direct download

Download [latest version](https://github.com/alex-nzr/bit-umc-php-sdk/archive/refs/heads/master.zip), unzip and copy to your project folder.


## Usage

### Install 1C extension
At first, you need to publish your 1C base on web-server.


###API Client
To start working with the API, you need to create an API client,  giving it the necessary data to connect to the 1c database.

For example, in your database there is a user with the username `1cUser` and the password `1cUserPassword`. 
The database is published on a web server and is accessed via a browser at `http://88.29.123.512:3500/umc/`.

In this case, the parameters for creating an API client will be as follows:
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
Next, need to get services for reading and writing data.
```
    use ANZ\BitUmc\SDK\Service\Factory\ServiceFactory;
    use ANZ\BitUmc\SDK\Tools\Debug;

    $factory = new ServiceFactory($client);
    $reader  = $factory->getReader();
    $writer  = $factory->getWriter();
```

And then can start working with 1c

###Get clinics
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

###Get employees
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
                [specialtyName] => Хирургия
                [specialtyUid] => 0KXQuNGA0YPRgNCz0LjRjw==
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

###Get catalog
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
                [duration] => 1800
                [measureUnit] => мл
                [parent] => 85812f74-1cc9-11dc-b7bd-000461ac6871
            )
        )
        */
```

###Get catalog
```
    $period       = 14;//days
    $clinicUid    = 'f679444a-22b7-11df-8618-002618dcef2c';
    $employeeUids = [
        '19cb6fa5-1578-11ed-9bee-5c3a455eb0d0', 
        '99868528-0928-11dc-93d1-0004614ae652',
        '2eb1f97b-6a3c-11e9-936d-1856809fe650'
    ];
    //all params are not required
    $res = $reader->getSchedule($period, $clinicUid, $employeUids);
    if ($result->isSuccess())
    {
        Debug::print($result->getData());
        /*
        Array
        (
            
        )
        */
```