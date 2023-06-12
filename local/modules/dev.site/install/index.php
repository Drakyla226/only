<?php

use Bitrix\Main\EventManager;
use Dev\Site\Handlers\IBlock;

class dev_site extends CModule
{
    const MODULE_ID = 'dev.site';
    public $MODULE_ID = 'dev.site';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME = 'Тренировочный модуль';
    public $PARTNER_NAME = 'dev';

    public static array $events = [
        [
            'module' => 'iblock',
            'event' => 'OnAfterIBlockElementAdd',
            'class' => IBlock::class,
            'method' => 'addLog',
            'sort' => 500,
        ],
        [
            'module' => 'iblock',
            'event' => 'OnAfterIBlockElementUpdate',
            'class' => IBlock::class,
            'method' => 'addLog',
            'sort' => 500,
        ],
    ];

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    function InstallFiles($arParams = array()): bool
    {
        return true;
    }

    function UnInstallFiles(): bool
    {
        return true;
    }

    public function DoInstall(): void
    {
        RegisterModule($this->MODULE_ID);

        $this->InstallFiles();
        $this->registerEvents();
    }

    public function DoUninstall(): void
    {
        UnRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
        $this->unRegisterEvents();
    }

    /**
     *
     * @return void
     */
    public function registerEvents(): void
    {
        $eventManager = EventManager::getInstance();

        foreach (self::$events as $event) {
            $eventManager->registerEventHandler(
                $event['module'],
                $event['event'],
                $this->MODULE_ID,
                $event['class'],
                $event['method'],
                $event['sort']
            );
        }
    }

    public function unRegisterEvents(): void
    {
        $eventManager = EventManager::getInstance();

        foreach (self::$events as $event) {
            $eventManager->unRegisterEventHandler(
                $event['module'],
                $event['event'],
                $this->MODULE_ID,
                $event['class'],
                $event['method']
            );
        }
    }
}
