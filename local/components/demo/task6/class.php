<?

class MyComponentTask6 extends CBitrixComponent
{

    public function MyComponent(): array
    {

        if (!isset($this->arParams['IBLOCK_ID']) || empty($this->arParams['IBLOCK_ID'])) :
            ShowError('Не задан тип инфоблока');
        endif;

        $arResult = CIBlockElement::GetList(
            array(
                "DATE_CREATE" => "ASC"
            ),
            array(
                "IBLOCK_TYPE" => $this->arParams['IBLOCK_TYPE'],
                "IBLOCK_ID" => $this->arParams['IBLOCK_ID'],
            ),
            false,
            false,
            array(
                "ID",
                "NAME",
                "IBLOCK_NAME"
            )
        );

        $arResultElements = array();
        while ($ob = $arResult->GetNextElement()) :
            $arFields = $ob->GetFields();
            $arResultElement = CIBlockElement::GetByID($arFields['ID']);
            if ($arElement = $arResultElement->GetNext()) :
                $iblockId = $arElement['IBLOCK_NAME'];
                $arResultElements[$iblockId][] = array(
                    'NAME' => $arFields['NAME'],
                );
            endif;
        endwhile;

        return $arResultElements;
    }
}
