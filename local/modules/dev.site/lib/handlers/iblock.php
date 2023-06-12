<?php

namespace Dev\Site\Handlers;

use Bitrix\Iblock\ElementTable;
use \CIBlockElement;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use http\Exception;

class IBlock
{
    public const I_BLOCK_CODE = 'LOG';

    public static function addLog(&$arFields): void
    {
        try {
            $iBlockID = (int)$arFields['IBLOCK_ID'];
            $iBlock = static::getIBlockById($iBlockID);
            if (!empty($iBlock) && $iBlock['CODE'] !== static::I_BLOCK_CODE) :
                $logIBlockId = \Dev\Site\Helpers\IBlock::getIBlockIdByCode(static::I_BLOCK_CODE);
                $logIBlockSectionId = static::sectionFirstOrCreate($iBlock);
                $sectionId = $arFields['IBLOCK_SECTION'][0];

                $rsSections = SectionTable::getList([
                    'filter' => ['=ID' => $sectionId],
                    'select' => ['NAME'],
                ]);
                if ($arSection = $rsSections->fetch()) {
                    $sectionName = $arSection['NAME'];
                }

                if ($sectionName == NULL){
                    $sectionName = 'элемент создан в родительском';
                }

                $newElementFields = [
                    'IBLOCK_ID' => $logIBlockId,
                    'IBLOCK_SECTION_ID' => $logIBlockSectionId,
                    'NAME' => 'ID ' . $arFields['ID'],
                    'ACTIVE' => 'Y',
                    'PREVIEW_TEXT' => 'Инфоблок: ' . $iBlock['NAME'] . ' | Раздел: ' . $sectionName . ' | Элемент: ' . $arFields['NAME'],
                    'TIMESTAMP_X' => new DateTime()
                ];
                $newElement = new CIBlockElement;
                $newElement->Add($newElementFields);
            endif;
        } catch (\Exception $exception) {
            
        }
    }

    /**
     * @param array $iBlock
     * @return int ID связанного радела в инфоблоке LOG
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws LoaderException
     * @throws \Exception
     */
    public static function sectionFirstOrCreate(array $iBlock): int
    {
        $filter = [
            'IBLOCK.CODE' => static::I_BLOCK_CODE,
            'CODE' => $iBlock['CODE'],
        ];

        $section = SectionTable::getList(['filter' => $filter])->fetch();

        if ($section === false) {
            $logIBlockId = \Dev\Site\Helpers\IBlock::getIBlockIdByCode(static::I_BLOCK_CODE);
            $addResult = SectionTable::add([
                'IBLOCK_ID' => $logIBlockId,
                'CODE' => $iBlock['CODE'],
                'NAME' => $iBlock['NAME'] . ' [' . $iBlock['CODE'] . ']',
                'TIMESTAMP_X' => new DateTime()
            ]);

            if ($addResult->isSuccess()) {
                return $addResult->getId();
            } else {
                throw new \Exception($addResult->getErrorMessages()[0]);
            }
        } else {
            return (int)$section['ID'];
        }
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getIBlockById(int $id): array
    {
        return IblockTable::getRow([
            'filter' => ['ID' => $id],
            'select' => ['ID', 'NAME', 'CODE']
        ]) ?? [];
    }
}
