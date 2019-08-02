<?php
/**
 * Функция автолоадера модуля
 * Проверяет директорию /lib
 */
spl_autoload_register('dev_currencywidget__autoloadClasses');
function dev_currencywidget__autoloadClasses($sClassName) {

    if(strpos($sClassName, 'Dev\CurrencyWidget') !== 0) {
        return;
    }

    $sClassFile = __DIR__.'/lib';

    $sClassName_m = $sClassName;

    $sClassName_m = str_replace('Dev\CurrencyWidget', '', $sClassName_m);
    $sClassName_m = str_replace('\\', '/', $sClassName_m);

    if (file_exists($sClassFile.''.$sClassName_m.'.php')) {
        require_once($sClassFile.''.$sClassName_m.'.php');
    }
}