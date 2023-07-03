<?php

namespace KingbirdFastlog;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\ExceptionHandlerFormatter;
use Bitrix\Main\Diag\ExceptionHandlerLog;
use KingbirdFastlog;

/**
 * Отправка лога при возникновении Exception Битрикса
 * Для работы нужно указать параметры в .settings.php
 * в блоке exception_handling
 */
class SendExceptionLog extends ExceptionHandlerLog
{
    private $level;
    private array $dont_show;

    public function write($exception, $logType)
    {
        if (in_array($logType, $this->dont_show)) {
            return;
        }

        $log_type = $this::logTypeToString($logType);
        $text = ExceptionHandlerFormatter::format($exception, false, $this->level);

        $message = [
            'message' => $exception->getMessage(),
            'error_level' => $log_type,
            'stack_trace' => $text,
            'uri' => $_SERVER['REQUEST_URI']
        ];
        $this->send($message);
    }

    public function initialize(array $options)
    {
        try {
            $this->level = $options['level'] ?? 0;
            $this->dont_show = $options['dont_show'] ?? [];
        } finally {
            $this->file_logger = new \Bitrix\Main\Diag\FileExceptionHandlerLog();
            $this->file_logger->initialize($options);
        }
    }

    public function send($message)
    {
        if (Option::get(KingbirdFastlog::MODULE_ID, "active") != 'Y'){
            return;
        }

        if (Option::get(KingbirdFastlog::MODULE_ID, "exceptions_log") != 'Y'){
            return;
        }

        if (!empty($message)) {
            KingbirdFastlog::add($message);
        }
    }
}
