<?php

use Bitrix\Main\Config\Option;
use KingbirdFastlog\Sender;

class KingbirdFastlog
{
    const MODULE_ID = "fastlogs-sdk-bitrix";

    public static function add($data, $slug = null, $time = null)
    {
        if (Option::get(KingbirdFastlog::MODULE_ID, "active") != 'Y'){
            return;
        }

        $sender = new Sender();

        try {
            $sender->add($data, $slug, $time);
        }catch (\Exception $e){

        }
    }
}
