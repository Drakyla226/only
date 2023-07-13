<?php

use \Bitrix\Main\EventManager;

class only_task7 extends CModule
{
    const MODULE_ID = 'only.task7';

    function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . '/version.php';
        
        $this->MODULE_ID = 'only.task7';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = 'Модуль разработанный для задания 7';
        $this->MODULE_DESCRIPTION = 'Комплексное свойство и HTML редактор';
        $this->PARTNER_NAME = 'Олег';
    }

    public function DoInstall(): void
    {
        RegisterModule($this->MODULE_ID);
        $this->InstallEvents();
        $this->InstallFiles();
        $this->InstallDB();
    }

    public function DoUninstall(): void
    {
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallDB();
    }

    function InstallFiles($arParams = array()): bool
    {
        return true;
    }

    function UnInstallFiles(): bool
    {
        return true;
    }
    

    function InstallDB()
    {
        return true;
    }

    function UnInstallDB()
    {
        return true;
    }

    function getEvents()
    {
        return [
            ['FROM_MODULE' => 'iblock', 'EVENT' => 'OnIBlockPropertyBuildList', 'TO_METHOD' => 'GetUserTypeDescription'],
        ];
    }

    function InstallEvents()
    {
        $classHandler = 'CIBlockPropertyTask7';
        $eventManager = EventManager::getInstance();

        $arEvents = $this->getEvents();
        foreach($arEvents as $arEvent){
            $eventManager->registerEventHandler(
                $arEvent['FROM_MODULE'],
                $arEvent['EVENT'],
                $this->MODULE_ID,
                $classHandler,
                $arEvent['TO_METHOD']
            );
        }

        return true;
    }

    function UnInstallEvents()
    {
        $classHandler = 'CIBlockPropertyTask7';
        $eventManager = EventManager::getInstance();

        $arEvents = $this->getEvents();
        foreach($arEvents as $arEvent){
            $eventManager->unregisterEventHandler(
                $arEvent['FROM_MODULE'],
                $arEvent['EVENT'],
                $this->MODULE_ID,
                $classHandler,
                $arEvent['TO_METHOD']
            );
        }

        return true;
    }
}