<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);
global $APPLICATION;

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

$urlAPI = Option::get($module_id, "urlWeb") . '/' . Option::get($module_id, "slug");

$arEventLogType = array_merge(['ALL' => 'Все'], CEventLog::GetEventTypes());

$aTabs = array( // набор полей настроек модуля для примера
    array(
        "DIV" => "edit",
        "TAB" => Loc::getMessage("FASTLOG_OPTIONS_TAB_COMMON"),
        "TITLE" => Loc::getMessage("FASTLOG_OPTIONS_TAB_NAME"),
        "OPTIONS" => array(
            Loc::getMessage("FASTLOG_OPTIONS_URL_TEXT") . Loc::getMessage("FASTLOG_OPTIONS_URL", ['#URL#' => $urlAPI]),
            array(
                "active",
                Loc::getMessage("FASTLOG_OPTIONS_TAB_ACTIVE"),
                "Y",
                array("checkbox"),
            ),
            array(
                "slug",
                Loc::getMessage("FASTLOG_OPTIONS_SLUG"),
                "",
                array("text", 10)
            ),
            array(
                "files_log",
                Loc::getMessage("FASTLOG_OPTIONS_FILES_LOG"),
                "N",
                array("checkbox"),
            ),
            array(
                "exceptions_log",
                Loc::getMessage("FASTLOG_OPTIONS_EXCEPTIONS_LOG"),
                "N",
                array("checkbox"),
            ),
            array(
                "exceptions_log_info",
                Loc::getMessage("FASTLOG_OPTIONS_EXCEPTIONS_LOG_INFO"),
                Loc::getMessage("FASTLOG_OPTIONS_EXCEPTIONS_LOG_INFO_TEXT"),
                array("statichtml"),
            ),
            array(
                "event_log",
                Loc::getMessage("FASTLOG_OPTIONS_EVENT_LOG"),
                "N",
                array("checkbox"),
            ),
            array(
                "event_log_select_type",
                Loc::getMessage("FASTLOG_OPTIONS_EVENT_LOG_SELECT_TYPE"),
                "ALL",
                array("multiselectbox", $arEventLogType),
            ),
        )
    )
);

if ($request->isPost() && check_bitrix_sessid()) { // сохранения настроек модуля

    foreach ($aTabs as $aTab) {

        foreach ($aTab["OPTIONS"] as $arOption) {

            if (!is_array($arOption)) {

                continue;
            }

            if ($arOption["note"]) {

                continue;
            }

            if ($request["apply"]) {

                $optionValue = $request->getPost($arOption[0]);

                if ($arOption[0] == "switch_on") {

                    if ($optionValue == "") {

                        $optionValue = "N";
                    }
                }

                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            } elseif ($request["default"]) {
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . $module_id . "&lang=" . LANG);
}

$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);
$tabControl->Begin();
?>
<form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">

    <?
    foreach ($aTabs as $aTab) {

        if ($aTab["OPTIONS"]) {

            $tabControl->BeginNextTab();

            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
    }

    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply" value="<? echo(Loc::GetMessage("FASTLOG_OPTIONS_INPUT_APPLY")); ?>"
           class="adm-btn-save"/>

    <?
    echo(bitrix_sessid_post());
    ?>

</form>

<? $tabControl->End(); ?>
