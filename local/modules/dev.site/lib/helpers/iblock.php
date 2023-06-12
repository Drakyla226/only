<?php

namespace Dev\Site\Helpers;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class IBlock
{
    /**
     * @param string $code
     * @return int
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIBlockIdByCode(string $code): int
    {
        if (Loader::includeModule('iblock')) {
            return IblockTable::getRow(['filter' => ['=CODE' => $code]])['ID'];
        } else {
            return 0;
        }
    }
}