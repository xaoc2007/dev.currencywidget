<?php
namespace Dev\CurrencyWidget;

/**
 * Класс для работы с параметрами модуля как модуля
 * Class Module
 * @package Dev\CurrencyWidget
 */
class Module {

    static function getId() {
        return basename(realpath(__DIR__.'/../'));
    }
}