<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require_once 'class.php';

$disk = new Arhitector\Yandex\Disk('y0_AgAAAAAZujLVAAouywAAAADnuqmbv_PLWtJ5TYCjbGilZQo0X7Rj7_c');
crud\class\CRUD::createFile($disk);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
