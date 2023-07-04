<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Задание 6");
?>
<body>
<?$APPLICATION->IncludeComponent(
	"demo:task6", 
	".default", 
	array(
		"IBLOCK_ID" => "",
		"IBLOCK_TYPE" => "test",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>
</body>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
