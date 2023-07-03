<?
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/local/modules/fastlogs-sdk-bitrix/')){
    require($_SERVER['DOCUMENT_ROOT'].'/local/modules/fastlogs-sdk-bitrix/bitrixClass/logger.php');
}

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/fastlogs-sdk-bitrix/')) {
    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/fastlogs-sdk-bitrix/bitrixClass/logger.php');
}
