<?php

namespace Dev\Site\Agents;


class IBlock
{

    public const I_BLOCK_CODE = 'LOG';

    public static function clearOldLogs()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Dev\Site\Helpers\IBlock::getIBlockIdByCode(static::I_BLOCK_CODE);
            $numElementsToDelete = 10;
            $rsElements = \CIBlockElement::GetList(
                ['TIMESTAMP_X' => 'DESC'],
                ['IBLOCK_ID' => $iblockId,],
                false,
                false,
                ['ID']
            );
            $i = 0;
            while ($arElement = $rsElements->Fetch()) :
                if ($i >= $numElementsToDelete) :
                    \CIBlockElement::Delete($arElement['ID']);
                endif;
                $i++;
            endwhile;
        }
    }
}
