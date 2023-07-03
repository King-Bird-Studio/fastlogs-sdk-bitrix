<?php

namespace KingbirdFastlog;

use Bitrix\Main\Config\Option;
use KingbirdFastlog;

/**
 * Отправка лога при создании файлового лога средствами Битрикс
 */
class SendFileLog
{
    public static function send($message)
    {
        if (Option::get(KingbirdFastlog::MODULE_ID, "active") != 'Y'){
            return;
        }

        if (Option::get(KingbirdFastlog::MODULE_ID, "files_log") != 'Y'){
            return;
        }

        if (!empty($message)) {
            KingbirdFastlog::add($message);
        }
    }
}
