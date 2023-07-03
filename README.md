<p align="center">
    <h1 align="center">Fastlogs Bitrix SDK</h1>
    <br>
</p>

Требования
------------

PHP 5.6+, установленные расширения json и curl.
Битрикс версии 14+

Установка
---------------

1. Клонировать репозиторий в директорию с модулями `/bitrix/modules/` или `/local/modules/`
2. Выполнить установку модуля через админ часть сайта `/bitrix/admin/partner_modules.php`

Для работы лога exception нужно: 

1. Открыть файл /bitrix/.settings.php
2. Изменить параметры в блоке exception_handling
    ```php
    'handled_errors_types' => E_ALL & ~E_NOTICE & ~E_DEPRECATED,
    'exception_errors_types' => E_ALL & ~E_NOTICE & ~E_DEPRECATED,
    
    'log' => array(
      'settings' => array(
         'dont_show' => [\Bitrix\Main\Diag\ExceptionHandlerLog::LOW_PRIORITY_ERROR]
      ),
      'class_name' => 'KingbirdFastlog\\SendExceptionLog',
      'extension' => '',
      'required_file' => 'modules/fastlogs-sdk-bitrix/lib/general/SendExceptionLog.php',
    )
    
    ```

Использование
------------

Отправть свой лог
```php
KingbirdFastlog::add(['TEST']);
```

Если нужно писать в разные логи, то slug нужного лога можно передавать опциональным параметром:

```php
KingbirdFastlog::add(['TEST'], '23402493202');
```
