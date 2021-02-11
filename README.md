# shBorica
PHP библиотека за извършване на плащане чрез новата система на Борика.

## Подготовка
Преди всичко трябва да имам регистриран в Борика търговец, от който да взема неговото ID (MID) и ID на терминала (TID). С тяхна помощ ще създам частните ключове и заявките за сертификат за тестов режим и реална среда.

```shell script
mkdir ~/borica/myproject_9876543210
cd ~/borica/myproject_9876543210
PREF=V1234567_20201028
```

В примера създавам и отварям папка за проекта. Създавам си правило, числото в името на папката да е MID за да не се налага да го пазя отделно. На последния ред задавам префикс за имената на криптографските файлове, които ще създам. Лявата му част е TID, а дясната - текущата дата във формат `YYYYMMDD`.

### Генериране на частни ключове

```shell script
openssl genrsa -out ${PREF}_D.key 2048
openssl genrsa -out ${PREF}_P.key 2048
```

Последната буква от имената на файловете определя режима (`D` - тестов, `P` - реален).

### Създаване на заявки за сертификати

```shell script
openssl req -new -key ${PREF}_D.key -out ${PREF}_D.csr
openssl req -new -key ${PREF}_P.key -out ${PREF}_P.csr
```

При създаването на заявките ще ми се наложи да въведа едни и същи данни два пъти:

```
Country Name (2 letter code) [AU]:BG
State or Province Name (full name) [Some-State]:Sofia
Locality Name (eg, city) []:Sofia
Organization Name (eg, company) [Internet Widgits Pty Ltd]:Firma EOOD
Organizational Unit Name (eg, section) []:V1234567
Common Name (e.g. server FQDN or YOUR name) []:mysite.com
Email Address []:info@imysite.com

Please enter the following 'extra' attributes
to be sent with your certificate request
A challenge password []:
An optional company name []:
```

Има няколко особености при въвеждането:
* Данните трябва да са идентични при двете заявки.
* На "Organizational Unit Name" се въвежда TID.
* "А challenge password" може да не се въвежда, но ако се въведе, трябва да се запази някъде, защото после ще ми потрябва.

Генерираните заявки за сертификат, както и URL адрес за отговор се изпращат на банката.

## Разработка

Банката трябва да ме уведоми кога мога да започна разработка в тестови режим. Възможно е тя да ми предостави сертификат за проверка на отговора, но той засега е един и същ за всички търговци и може да бъде намерен в мрежата. Когато разработката е готова, се попълва една таблица и се прави лог на изпратените и получените данни в тестов режим, за всеки един тип транзакция, който се използва в разработката (обикновено само тип 1 - плащане). Таблицата и логът се изпращат на банката, която след одобрение ме уведомява че мога да мина към реална среда. Отново е възможно да ми предостави сертификат за проверка на отговора, който е възможно вече да притежавам. Таблицата, логът и върнатите сертификати (ако вече ги нямам) запазвам в папката, която създадох при подготовката.

Разработват се две стъпки: 
* Изпращане на заявка за плащане. За подписване на съобщението е необходим частния ключ. 
* Получаване на отговор за плащането. За проверка на автентичността на отговора е необходим публичния ключ от сертификата.

В разработката използвам класът `Borica`. При създаване на инстанция, от конструктура могат да се посочат настройки (първи параметър) и стойности по подразбиране на изпратените данни (втори параметър). И двата параметъра не са задължителни, но ако са подадени, трябва да са асоциативни масиви.

И настройките и стойностите по подразбиране си имат съответни полета в класа (`$config` и `$defaults`). Възможно е разширяване на тези полета, така, че при създаване на инстанция да подавам минимален брой елементи или нищо.

Настройките са:
* `suffix` - Суфикс на поръчката. Ползва се при генериране на `AD.CUST_BOR_ORDER_ID`.
* `test_mode` - Задава тестова среда.
* `private_key` - Частен ключ, генериран при подготовката. Взима се от съдържанието на `.key` файла.
* `private_key_password` - Парола за частния ключ. Ако е зададена парола при създаване на заявката за сертификат, тук също трябва да се зададе.
* `certificate` - Сертификат, с който се проверява отговора. Взима се от съдържанието на `.cer` файл.

Ето някои данни, за които има смисъл да бъдат зададени стойности по подразбиране:
* `MERCHANT` - ID на търговеца (MID)
* `TERMINAL` - ID на терминал (TID)
* `CURRENCY` - валута
* `COUNTRY` - държава
* `MERCH_GMT` - часови пояс
* `MERCH_NAME` - име на търговеца (появява се на страницата за плащане)
* `MERCH_URL` - интернет адрес на търговеца
* `ЕMAIL` 
* `ADDENDUM`

Данни, за които няма смисъл да се задава стойност по подразбиране:
* `ORDER` - номер на поръчката
* `AMOUNT` - сума за плащане
* `P_SIGN` - цифров подпис
* `TRTYPE` - специално за `TRTYPE` в кода си има стойност по подразбиране - `1`
* `TIMESTAMP` - ако липсва в подадените данни, стойността сама си се генерира
* `NONCE` - ако липсва в подадените данни, стойността сама си се генерира
* `AD.CUST_BOR_ORDER_ID` - ако липсва в подадените данни, стойността сама си се генерира

В примерите `request.php` и `response.php` настройките и стойностите по подразбиране се зареждат от конфигурационните файлове `config/config.php` и `config/defaults.php`. Достатъчно е да редактирам само тези конфигурационни файлове за да подкарам тестовете.

