<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class fastlog_kingbird extends CModule
{

    public function __construct()
    {

        if (file_exists(__DIR__ . "/version.php")) {

            $arModuleVersion = array();

            include_once(__DIR__ . "/version.php");

            $this->MODULE_ID = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME = Loc::getMessage("FASTLOG_TOTOP_NAME");
            $this->MODULE_DESCRIPTION = Loc::getMessage("FASTLOG_TOTOP_DESCRIPTION");
            $this->PARTNER_NAME = Loc::getMessage("FASTLOG_TOTOP_PARTNER_NAME");
            $this->PARTNER_URI = Loc::getMessage("FASTLOG_TOTOP_PARTNER_URI");
        }

        return false;
    }

    /**
     * Начала установки модуля
     * @return false
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function DoInstall()
    {
        global $APPLICATION;

        if (CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) {

            $this->InstallFiles();
            $this->InstallDB();

            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallEvents();

            $this->InstallAgent();

            $urlAPI = 'https://fastlogs-backend.i.kingbird.ru';
            Option::set($this->MODULE_ID, "urlAPI", $urlAPI);
            Option::set($this->MODULE_ID, "urlWeb", 'https://fastlogs-web.i.kingbird.ru');
            Option::set($this->MODULE_ID, "slug", $this->sendSlugApiLog($urlAPI));
            Option::set($this->MODULE_ID, "active", "Y");

        } else {
            $APPLICATION->ThrowException(
                Loc::getMessage("FASTLOG_TOTOP_INSTALL_ERROR_VERSION")
            );
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("FASTLOG_TOTOP_INSTALL_TITLE") . " \"" . Loc::getMessage("FASTLOG_TOTOP_NAME") . "\"",
            __DIR__ . "/step.php"
        );


        return false;
    }

    public function InstallFiles()// копирования файлов
    {
        $dirToModule = dirname(__DIR__);

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/';
        $fileInitFrom = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/init.php';
        $fileInitTo = $dir . 'init.php';

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!file_exists($dir . 'init.php')) {
            copy($fileInitFrom, $fileInitTo);
        }

        if (file_exists($fileInitTo)) {
            $fileInit = file_get_contents($fileInitTo);
            $dirIncludeFile = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dirToModule . '/includeBitrixClass.php');
            $past = "<?\ninclude_once(\$_SERVER['DOCUMENT_ROOT'].'$dirIncludeFile');";
            $str = $this->str_replace_once('<?', $past, $fileInit);
            file_put_contents($fileInitTo, $str);
        }

        if (!file_exists($dirToModule . '/bitrixClass/')) {
            mkdir($dirToModule . '/bitrixClass/', 0755, true);
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

        return false;
    }

    /**
     * Метод замены первого вхождения в строке
     * @param $search
     * @param $replace
     * @param $text
     * @return array|string|string[]
     */
    public function str_replace_once($search, $replace, $text)
    {
        $pos = strpos($text, $search);
        return $pos !== false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }

    public function InstallDB() // добовления в БД
    {

        return false;
    }

    public function InstallEvents() // регистрация событий модуля
    {

        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnPageStart", // событие после которого будет вызыватся модуль
            $this->MODULE_ID,
            "KingbirdFastlog\EventModule",
            "up"
        );

        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnEpilog", // событие после которого будет вызыватся модуль
            $this->MODULE_ID,
            "KingbirdFastlog\EventModule",
            "down"
        );

        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnUpdatesInstalled", // событие после которого будет вызыватся модуль
            $this->MODULE_ID,
            "KingbirdFastlog\EventModule",
            "UpdateBitrix"
        );

        return false;
    }

    public function InstallAgent() //Создания агента
    {
        \CAgent::AddAgent("KingbirdFastlog\\SendEventLog::SendEventLogAgent();", $this->MODULE_ID, "N", 60, "", "Y");
    }

    /**
     * Старт удаления модуля
     * @return false
     */
    public function DoUninstall()
    {

        global $APPLICATION;

        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallAgent();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("FASTLOG_TOTOP_UNINSTALL_TITLE") . " \"" . Loc::getMessage("FASTLOG_TOTOP_NAME") . "\"",
            __DIR__ . "/unstep.php"
        );

        return false;
    }

    public function UnInstallFiles() // удаления файлов
    {

        $dirToModule = dirname(__DIR__);

        $fileInitTo = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/init.php';
        if (file_exists($fileInitTo)) {
            $fileInit = file_get_contents($fileInitTo);
            $dirIncludeFile = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__DIR__) . '/includeBitrixClass.php');
            $past = "include_once(\$_SERVER['DOCUMENT_ROOT'].'$dirIncludeFile');";
            $str = str_replace($past, '', $fileInit);

            file_put_contents($fileInitTo, $str);
        }

        if (file_exists($dirToModule . '/bitrixClass/logger.php')) {
            unlink($dirToModule . '/bitrixClass/logger.php');
        }

        return false;
    }

    public function UnInstallDB() // Удаления из БД
    {

        Option::delete($this->MODULE_ID);

        return false;
    }

    public function UnInstallEvents() // удаления регистрации события
    {

        EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnPageStart", // событие после которого будет вызыватся модуль
            $this->MODULE_ID,
            "KingbirdFastlog\EventModule",
            "up"
        );

        EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnEpilog", // событие после которого будет вызыватся модуль
            $this->MODULE_ID,
            "KingbirdFastlog\EventModule",
            "down"
        );

        EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnUpdatesInstalled", // событие после которого будет вызыватся модуль
            $this->MODULE_ID,
            "KingbirdFastlog\EventModule",
            "UpdateBitrix"
        );


        return false;
    }

    public function UnInstallAgent()
    {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

    /**
     * запрос slug у API fastlog
     * @param $urlAPI
     * @return mixed
     */
    public function sendSlugApiLog($urlAPI)
    {
        $ch = curl_init();
        $chOptions = [
            CURLOPT_URL => $urlAPI . '/api/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST"
        ];
        curl_setopt_array($ch, $chOptions);

        $jsonResponse = curl_exec($ch);

        return json_decode($jsonResponse, true)['bucket']['slug'];
    }
}
