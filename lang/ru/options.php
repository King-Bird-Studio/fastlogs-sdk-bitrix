<?php
$MESS["FASTLOG_ZOPTIONS_TAB_NAME"] = "Настройки";

$MESS["FASTLOG_OPTIONS_TAB_COMMON"] = "Общие";
$MESS["FASTLOG_OPTIONS_TAB_ACTIVE"] = "Активность";
$MESS["FASTLOG_OPTIONS_SLUG"] = "Идентификатор лога";
$MESS["FASTLOG_OPTIONS_URL_TEXT"] = "Адрес лога: ";
$MESS["FASTLOG_OPTIONS_URL"] = "<a href='#URL#' target='_blank'>#URL#</a>";
$MESS["FASTLOG_OPTIONS_EVENT_LOG"] = "Логировать Журнал событий";
$MESS["FASTLOG_OPTIONS_EVENT_LOG_SELECT_TYPE"] = "Логируемые типы событий из журнала";
$MESS["FASTLOG_OPTIONS_FILES_LOG"] = "Логировать файлы логов";
$MESS["FASTLOG_OPTIONS_EXCEPTIONS_LOG"] = "Логировать exceptions";

$MESS["FASTLOG_OPTIONS_EXCEPTIONS_LOG_INFO"] = "Для работы лога exceptions:";
$MESS["FASTLOG_OPTIONS_EXCEPTIONS_LOG_INFO_TEXT"] = "<p>Изменить параметры в .setting.php в блоке exception_handling</p>
<pre>
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
</pre>
";


$MESS["FASTLOG_OPTIONS_INPUT_APPLY"] = "Применить";
$MESS["FASTLOG_OPTIONS_INPUT_DEFAULT"] = "По умолчанию";
