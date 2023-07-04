<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

require_once(__DIR__ . '/class.php');

if ($this->startResultCache(false, array(($arParams["CACHE_GROUPS"] === "N" ? false : $USER->GetGroups())))) {
	if (!Loader::includeModule("iblock")) {
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	$arResult = MyComponentTask6::MyComponent();
	$this->includeComponentTemplate();
}
