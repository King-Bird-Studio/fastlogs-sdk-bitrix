<?php
CModule::AddAutoloadClasses(
    "fastlogs-sdk-bitrix",
    array(
        'KingbirdFastlog' => "lib/KingbirdFastlog.php",
        'KingbirdFastlog\\EventModule' => "lib/EventModule.php",
        'KingbirdFastlog\\ExceptionLogger' => "lib/ExceptionLogger.php",
        'KingbirdFastlog\\Sender' => "lib/general/Sender.php",
        'KingbirdFastlog\\Config' => "lib/general/Config.php",
        'KingbirdFastlog\\SendEventLog' => "lib/general/SendEventLog.php",
        'KingbirdFastlog\\SendFileLog' => "lib/general/SendFileLog.php",
        'KingbirdFastlog\\SendExceptionLog' => "lib/general/SendExceptionLog.php",
        'KingbirdFastlog\\exceptions\\RuntimeException' => "lib/general/exceptions/RuntimeException.php",
        'KingbirdFastlog\\exceptions\\ConfigException' => "lib/general/exceptions/ConfigException.php",
        'KingbirdFastlog\\exceptions\\Error' => "lib/general/exceptions/Error.php",
    )
);



