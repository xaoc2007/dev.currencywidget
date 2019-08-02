<?php
namespace Dev\CurrencyWidget;

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__DIR__);

/**
 * Класс для работы с валютами, импорта валют
 *
 * Class Currency
 * @package Dev\CurrencyWidget
 */
class Currency {

    private static $url = 'http://www.cbr.ru/scripts/XML_daily.asp';

    /**
     * Метод-агент для импорта курсов валют и последующей записи в файл
     *
     * @param string $valuteCharCode
     * @return string
     */
    static function getCurrency($valuteCharCode = 'USD')
    {
        try {
            //Инициализация curl
            $curlInit = curl_init(self::$url);
            curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
            curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

            //Получаем ответ
            $response = curl_exec($curlInit);
            curl_close($curlInit);

            if(!$response) {
                throw new ImportCurrencyException(
                    str_replace(
                        ['#VALUTE#', '#URL#'],
                        [$valuteCharCode, self::$url],
                        Loc::getMessage('DEV_CURRENCYWIDGET_IMPORT_CURRENCY_EMPTY_RESPONSE')
                    )
                );
            }

            $xml = simplexml_load_string($response);

            //хм.. valute..
            if(!$xml->Valute){
                throw new ImportCurrencyException(
                    str_replace(
                        ['#VALUTE#', '#URL#'],
                        [$valuteCharCode, self::$url],
                        Loc::getMessage('DEV_CURRENCYWIDGET_IMPORT_CURRENCY_ERROR_RESPONSE')
                    )
                );
            }

            $arValute = [];
            foreach($xml->Valute as $valute) {
                if($valute->CharCode == $valuteCharCode) {
                    $arValute = [
                        'CHAR_CODE' => (string)$valute->CharCode,
                        'NAME' => (string)$valute->Name,
                        'NOMINAL' => (int)$valute->Nominal,
                        'VALUE' => (float)str_replace(',','.',$valute->Value)
                    ];
                }
            }

            if(!count($arValute)){
                throw new ImportCurrencyException(
                    str_replace(
                        ['#VALUTE#', '#URL#'],
                        [$valuteCharCode, self::$url],
                        Loc::getMessage('DEV_CURRENCYWIDGET_IMPORT_CURRENCY_VALUTE_NOT_FOUND')
                    )
                );
            }

            Tools::writeToFile($valuteCharCode, $arValute);

        } catch (\Throwable $e) {
            $SEVERITY = "WARNING";
            $ERROR_TYPE = Module::getId() . "_ERROR";
            $MODULE_ID = Module::getId();
            $ITEM_ID = "getCurrency";
            $DESCRIPTION = $e->getMessage();

            \CEventLog::Add([
                "SEVERITY" => $SEVERITY,
                "AUDIT_TYPE_ID" => $ERROR_TYPE,
                "MODULE_ID" => $MODULE_ID,
                "ITEM_ID" => $ITEM_ID,
                "DESCRIPTION" => $DESCRIPTION,
            ]);
        }

        return "\Dev\CurrencyWidget\Currency::getCurrency('".$valuteCharCode."');";
    }
}