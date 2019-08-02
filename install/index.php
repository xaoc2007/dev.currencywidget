<?php
use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\ModuleManager,
    \Bitrix\Main\Loader;

Loc::loadMessages(__DIR__);

class dev_currencywidget extends \CModule
{
    public $MODULE_ID = 'dev.currencywidget';
    public $MODULE_VERSION = '0.1';
    public $MODULE_VERSION_DATE = '2019-07-23 12:30:10';

    public $MODULE_NAME = '';
    public $MODULE_DESCRIPTION = '';
    public $PARTNER_NAME = '';
    public $PARTNER_URI = '';

    /**
     * Папка с компонентами для установки
     *
     * @var string
     */
    private static $component_base_dir = __DIR__ . '/components';

    public function __construct() {
        $this->MODULE_NAME  = Loc::getMessage('DEV_CURRENCYWIDGET_MODULE_NAME');
        $this->MODULE_DESCRIPTION  = Loc::getMessage('DEV_CURRENCYWIDGET_MODULE_DESCRIPTION');
        $this->PARTNER_NAME  = Loc::getMessage('DEV_CURRENCYWIDGET_PARTNER_NAME');
        $this->PARTNER_URI  = Loc::getMessage('DEV_CURRENCYWIDGET_PARTNER_URI');
    }

    /**
     * Установка модуля
     */
    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallComponents();
    }

    /**
     * Деинсталяция модуля
     */
    public function DoUninstall()
    {
        $this->UnInstallComponents();
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    /**
     * Устанавливаем компоненты из папки /components/
     *
     * @return bool
     */
    function InstallComponents()
    {
        $base_dir = self::$component_base_dir;
        if(!file_exists($base_dir)) {
            return true;
        }

        CopyDirFiles( self::$component_base_dir, Loader::getLocal('components/'), true, true);

        return true;
    }

    /**
     * Ищем компоненты, которые устанавливали и удаляем
     *
     * @return bool
     */
    function UnInstallComponents()
    {
        $base_dir = self::$component_base_dir;
        if(!file_exists($base_dir)) {
            return true;
        }

        $dh = opendir($base_dir);
        if(!$dh) {
            return false;
        }

        while($component_dir = readdir($dh)) {
            if (is_dir($base_dir . '/' . $component_dir) && $component_dir != '.' && $component_dir != '..') {
                \Bitrix\Main\IO\Directory::deleteDirectory(Loader::getLocal('components/') . $component_dir);
            }
        }

        return true;
    }
}