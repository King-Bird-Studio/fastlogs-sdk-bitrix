<?php

namespace KingbirdFastlog;

class EventModule
{
    public function up()
    {
        \Bitrix\Main\Loader::includeModule('fastlogs-sdk-bitrix');
    }

    public function down()
    {

    }

    /**
     * Обновления подменных классов Битрикса после обновления ядра
     * @return void
     */
    public function UpdateBitrix(){
        $dirToModule = dirname(__DIR__);

        if (file_exists($dirToModule . '/bitrixClass/logger.php')) {
            unlink($dirToModule . '/bitrixClass/logger.php');
        }

        if (file_exists($dirToModule . '/bitrixClass/')) {
            $fileInitFrom = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/lib/diag/logger.php';
            copy($fileInitFrom, $dirToModule . '/bitrixClass/logger.php');

            if (file_exists($dirToModule . '/bitrixClass/logger.php')) {
                $fileInit = file_get_contents($dirToModule . '/bitrixClass/logger.php');
                $past = "\KingbirdFastlog\SendFileLog::send(\$context['message']);\n            \$this->logMessage(\$level, \$message);";
                $str = $this->str_replace_once('$this->logMessage($level, $message);', $past, $fileInit);
                file_put_contents($dirToModule . '/bitrixClass/logger.php', $str);
            }
        }
    }
}
