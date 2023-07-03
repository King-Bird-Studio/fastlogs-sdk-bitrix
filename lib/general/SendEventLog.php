<?php

namespace KingbirdFastlog;

use Bitrix\Main\Config\Option;
use KingbirdFastlog;

/**
 * Отправка лога из Журнала событий по агенту
 */
class SendEventLog
{
    public static function SendEventLogAgent()
    {
        (new SendEventLog())->getEventLog();
        return "KingbirdFastlog\\SendEventLog::SendEventLogAgent();";
    }

    public function getEventLog()
    {
        if (Option::get(KingbirdFastlog::MODULE_ID, "active") != 'Y'){
            return;
        }

        if (Option::get(KingbirdFastlog::MODULE_ID, "event_log") != 'Y'){
            return;
        }

        global $DB;
        $lastSendId = Option::get(KingbirdFastlog::MODULE_ID, "last_send_id");
        $arlogType = explode(',', Option::get(KingbirdFastlog::MODULE_ID, "event_log_select_type"));

        $arSqlSearch = [];
        if ($lastSendId) {
            $arSqlSearch[] = "ID > $lastSendId";
        }

        if (!in_array('ALL', $arlogType) && !empty($arlogType[0])){
            $arSqlSearch[] = "AUDIT_TYPE_ID in ('".implode("', '", $arlogType)."')";
        }

        if (!empty($arSqlSearch)) {
            $filter = "WHERE " . implode(" AND ", $arSqlSearch);
        }

        $res = $DB->Query("SELECT * FROM b_event_log $filter ORDER BY ID DESC LIMIT 20");
        $arIdLog = [];
        while ($eventLog = $res->Fetch()) {
            $arIdLog[$eventLog['ID']] = $eventLog;
        }

        ksort($arIdLog);
        $lastId = 0;
        foreach ($arIdLog as $eventLog){
            $time = strtotime($eventLog['TIMESTAMP_X']) * 1000;
            $this->add($eventLog, $time);
            $lastId = $eventLog['ID'];
        }
        Option::set(KingbirdFastlog::MODULE_ID, "last_send_id", $lastId);
    }

    private function add($data, $time)
    {
        if (!empty($data)) {
            KingbirdFastlog::add($data, null, $time);
        }
    }
}
