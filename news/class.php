<?php

namespace crud\class;
?>

<?= '<script src="js/downloadfile.js"></script>' ?>
<?= '<script src="js/deletefile.js"></script>' ?>
<?= '<script src="js/createfile.js"></script>' ?>

<?php
class CRUD
{

    //Скачиваение файла
    public static function viewFile($resources)
    {
        foreach ($resources as $file) {
            $name = $file['name'];
            $path = $file['file'];

            // Вывод имени файла и кнопки "Скачать", "Удалить" и "Изменить".
            $result[] = '<b>' . $name . '</b><p>
                            <button class="download-btn" data-filename="' . $name . '" data-url="' . $path  . '" onclick="downloadFile(this)">Скачать файл</button>
                            <button class="delete-btn" data-file="' . $name . '">Удалить файл</button>
                            <a href="create.php?file=' . urlencode($file['file']) . '"><button>Просмотр файла</button></a><p>';
        }
        return $result;
    }

    //Добавление файла
    public static function addFile($disk)
    {
        echo '
                <form activ="/" method="post" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <input type="submit" name="submit" value="Загрузить">
                </form>
            ';

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
            $file = $_FILES["file"];

            $resource = $disk->getResource($file['name']);

            // загрузить файл на диск
            $resource->upload($file['tmp_name']);

            echo 'Файл <b>' . $file['name'] . '</b> успешно загрушен.';
        }
    }

    //Изменение файла
    public static function deleteFile($disk)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["file"])) {
            $filename = $_POST["file"];
            $resource = $disk->getResource($filename);
            $resource->has();
            $resource->delete();
        }
    }

    //Изменение файла
    public static function createFile($disk)
    {
        if (isset($_GET['file'])) {
            $file = $_GET['file'];
            // Здесь может быть логика для получения данных файла и их вывода на странице
            // Например, можно использовать file_get_contents() для чтения содержимого файла
            $fileContents = file_get_contents($file);

            // Получение значения параметра filename из строки
            if (preg_match('/filename=([^&]+)/', $file, $matches)) {
                $filename = urldecode($matches[1]);
            }

            echo '  <div align="center">
                        <form method="post" action="">
                            <h2>Содержимое файла || ' . $filename . ' ||</p>
                            <textarea name="newfile" rows="10" cols="100">' . $fileContents . '</textarea></h2>
                            <button>Сохранить</button>
                        </form>
                        <a href="/news/"><button>Отменить</button></a>
                    </div>
            ';
            $resources = $disk->getResources();

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["newfile"])) {
                foreach ($resources as $files) {
                    $name = $files['name'];
                    if ($filename == $name) {
                        $fileContent = $_POST['newfile'];
                        $tempFile = tempnam(sys_get_temp_dir(), 'temp_data');
                        file_put_contents($tempFile, $fileContent);
                        $resource = $disk->getResource($filename);
                        // перезапись файла на диск
                        $resource->upload($tempFile, true);
                        unlink($tempFile);
                        header("Location: /news/");
                        break;
                    }
                }
            }
        }
    }
}
