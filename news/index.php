<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// передать OAuth-токен зарегистрированного приложения.
require_once 'class.php';

use Arhitector\Yandex\Disk;
use crud\class\CRUD;


try {
    $disk = new Disk('y0_AgAAAAAZujLVAAouywAAAADnuqmbv_PLWtJ5TYCjbGilZQo0X7Rj7_c');
    // Получение списка ресурсов (файлов и папок) на Яндекс.Диске
    $resources = $disk->getResources();

    $downloadFile = CRUD::viewFile($resources);
    
    foreach ($downloadFile as $file) {
        echo $file;
    }

    CRUD::addFile($disk);

    CRUD::deleteFile($disk);

    
} catch (Exception $e) {
    // Обработка ошибок при запросе к API Яндекс.Диска
    echo 'Ошибка при запросе к API Яндекс.Диска: ' . $e->getMessage();
}
?>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>