Бібліотека для організації шлюзу прийому платежів від __EasyPay.ua__ по протоколу взаємодії EasySoft-Provider v3.1 (деталі дивись в EasySoft-Gate specification).

### Встановлення

```
composer require dshovchko/php_easypay
```

### Використання

Перш за все необхідно написати клас який реалізує інтерфейс _EasyPay\Callback_:

```
interface Callback
{
        public function check($account);
        public function payment($account, $orderid, $amount);
        public function confirm($paymentid);
        public function cancel($paymentid);
}
```

Це ваш обробник для команд Check, Payment, Confirm і Cancel (деталі дивись в EasySoft-Gate specification).

Метод _check()_ повинен повертати в разі успіху екземпляр класу _EasyPay\Provider31\AccountInfo_

Метод _payment()_ повинен повертати в разі успіху унікальний код платежу.

Метод _confirm()_ повинен повертати в разі успіху дату і час платежу.

Метод _cancel()_ повинен повертати в разі успіху дату і час скасування платежу.

У разі будь-якої помилки следет генерувати виняток. Код і повідомлення будуть передані шлюзу EasyPay.ua.

```
use EasyPay\Provider31\AccountInfo as AccountInfo;

class My_EasyPay_Callback implements EasyPay\Callback
{
        ....
        
        public function check($account)
        {
                $cl = $this->find_account($account);
                
                return new AccountInfo(array(
                        'Account' => $account,
                        'Fio' => $cl['fio'],
                        'Address' => $cl['adress'],
                ));
        }
        
        public function payment($account, $orderid, $amount)
        {
                $paymentid = $this->insert_payment($account, $orderid, $amount);
                
                return $paymentid;
        }
        
        public function confirm($paymentid)
        {
                $orderdate = $this->confirm_payment($paymentid)
                
                return $orderdate;
        }
        
        public function cancel($paymentid)
        {
                $canceldate = $this->cancel_payment($paymentid)
                
                return $canceldate;
        }

        ...
}
```

Потім створити файл наприклад _request.easypay.php_

```
<?php

$options = include('../etc/config.php');

require_once('../vendor/autoload.php');
require_once('../class/My_EasyPay_Callback.php');

$log = new EasyPay\Logger(
    realpath('../log').DIRECTORY_SEPARATOR,     // log path
    $options['log_prefix'],                     // prefix for log files (default 'my')
    $options['log_debug']                       // enable/disable debug (default false)
);

$cb = new My_EasyPay_Callback($options);

$p = new EasyPay\Provider31($options['easypay'], $cb, $log);
$p->process();

```

Також буде потрібно створити файл налаштувань _/your_path/etc/config.php_:
```
return array(

    // logger setings
    'log_prefix' => 'easypay',
    'log_debug' => true,

    // EasyPay settings
    'easypay' => array(
        'ServiceId' => 'myserviceid',                                           // service id
        'UseSign' => true,                                                      // use sign? (true/false)
        'EasySoftPKey' => '/your_path/etc/EasyPay/EasySoftPublicKey2.pem',      // path for EasyPay public key
        'ProviderPKey' => '/your_path/etc/EasyPay/My.ppk',                      // path for yours private key
    ),


```

І останнє це сконфігурувати веб-сервер, щоб був доступ до _request.easypay.php_ з ip-адрес easypay.ua.