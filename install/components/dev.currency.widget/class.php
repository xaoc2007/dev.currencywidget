<?php

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\SystemException;

/**
 * Класс компонента вывода виджета курсов валют
 */
class dev_currency_widget extends \CBitrixComponent
{
    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }

    public function onPrepareComponentParams($arParams)
    {
        if(!isset($arParams['CHAR_CODE']) || empty($arParams['CHAR_CODE'])) {
            $arParams['CHAR_CODE'] = 'USD';
        }
        return $arParams;
    }

    public function executeComponent()
    {
        try {
            if($this->loadModule()) {

                $this->getCurrencyResult();

                $this->includeComponentTemplate();
            }

        } catch (\Throwable $e) {
            $SEVERITY = "WARNING";

            //id модуля задано строкой, потому что при ошибках загрузки модуля его id не получить, а в лог записать надо
            $ERROR_TYPE = "dev_currencywidget_ERROR";
            $MODULE_ID = "dev_currencywidget";
            $ITEM_ID = "dev_currency_widget";

            $DESCRIPTION = $e->getMessage();

            //записываем в Журнал событий
            \CEventLog::Add(array(
                "SEVERITY" => $SEVERITY,
                "AUDIT_TYPE_ID" => $ERROR_TYPE,
                "MODULE_ID" => $MODULE_ID,
                "ITEM_ID" => $ITEM_ID,
                "DESCRIPTION" => $DESCRIPTION,
            ));
        }
    }

    public function loadModule()
    {
        if(!Loader::includeModule('dev.currencywidget')) {
            throw new SystemException(Loc::getMessage('DEV_CURRENCYWIDGET_IMPORT_CURRENCY_LOAD_MODULE_ERROR'));
        }

        return true;
    }

    public function getCurrencyResult()
    {
        $this->arResult['VALUTE_RAW'] = \Dev\CurrencyWidget\Tools::getFromFile($this->arParams['CHAR_CODE']);

        if(!$this->arResult['VALUTE_RAW']) {
            \Dev\CurrencyWidget\Tools::checkAndAddAgent($this->arParams['CHAR_CODE']);
        }

        $this->arResult['VALUTE'] = ($this->arResult['VALUTE_RAW'])?json_decode($this->arResult['VALUTE_RAW']):'';
    }
}