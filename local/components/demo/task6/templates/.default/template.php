<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$iblockCount = count($arResult);

foreach ($arResult as $iblockName => $elements) :
    if ($iblockCount != 1) :
        echo '<h2>Информационный блок ' . $iblockName . '</h2>';
    endif;
    foreach ($elements as $element) :
        echo '<p>' . $element['NAME'] . '</p>';
    endforeach;
endforeach;
