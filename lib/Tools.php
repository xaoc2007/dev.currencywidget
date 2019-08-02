<?php
namespace Dev\CurrencyWidget;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Application,
    \Bitrix\Main\IO\Directory,
    \Bitrix\Main\Context;

Loc::loadMessages(__DIR__);

/**
 * Класс с техническими методами, которые нужны в процессе работы модуля
 *
 * Class Tools
 * @package Dev\CurrencyWidget
 */
class Tools {

    public function checkAndAddAgent($valuteCharCode)
    {
        $res = \CAgent::GetList(
            ["ID" => "DESC"],
            ["NAME" => "\Dev\CurrencyWidget\Currency::getCurrency('".$valuteCharCode."');"]
        );
        if(!($agent = $res->Fetch())) {
            \CAgent::AddAgent(
                "\Dev\CurrencyWidget\Currency::getCurrency('".$valuteCharCode."');", // функция агента
                Module::getId(),                          // идентификатор модуля
                "N",                                  // агент не критичен к кол-ву запусков
                86400,                                // интервал запуска - 1 сутки
                date('d.m.Y H:i:s'),                // дата первой проверки на запуск
                "Y",                                  // агент активен
                date('d.m.Y H:i:s'),                // дата первого запуска
                30
            );
        }
    }

    public static function writeToFile($valuteCharCode, $arValute)
    {
        if(!count($arValute)) {
            return false;
        }

        $file_name = self::_getFileName($valuteCharCode);
        Directory::createDirectory(dirname($file_name));

        if(!file_put_contents($file_name, json_encode($arValute))) {
            throw new ImportCurrencyException(
                str_replace(
                    ['#VALUTE#', '#FILE_NAME#'],
                    [$valuteCharCode, $file_name],
                    Loc::getMessage('DEV_CURRENCYWIDGET_IMPORT_CURRENCY_WRITE_ERROR')
                )
            );
        }
    }

    public static function getFromFile($valuteCharCode)
    {
        if(!$valuteCharCode) {
            return false;
        }

        $file_name = self::_getFileName($valuteCharCode);

        if(!file_exists($file_name)) {
            return false;
            /*
            throw new ImportCurrencyException(
                str_replace(
                    ['#VALUTE#', '#FILE_NAME#'],
                    [$valuteCharCode, $file_name],
                    Loc::getMessage('DEV_CURRENCYWIDGET_IMPORT_CURRENCY_READ_ERROR')
                )
            );
            */
        }

        $res = file_get_contents($file_name);
        return $res;
    }

    private static function _getFileName($valuteCharCode) {
        $server = Context::getCurrent()->getServer();
        return $file_name = $server->getDocumentRoot() . '/' . Application::getPersonalRoot() .
            '/cache/' . Module::getId() . '/' . $valuteCharCode . '.php';
    }
}