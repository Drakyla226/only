// JavaScript функция для скачивания файла с Яндекс.Диска
function downloadFile(button) {
    var fileUrl = button.getAttribute('data-url');
    var filename = button.getAttribute('data-filename'); // Получаем имя файла из атрибута data-filename

    // Выполнение AJAX-запроса для скачивания файла
    var xhr = new XMLHttpRequest();
    xhr.open('GET', fileUrl, true);
    xhr.responseType = 'blob';
    xhr.onload = function () {
        if (xhr.status === 200) {
            var blob = new Blob([xhr.response], {
                type: 'application/octet-stream'
            });

            // Создаем временную ссылку и автоматически кликаем по ней для скачивания файла
            var downloadUrl = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.style.display = 'none';
            a.href = downloadUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(downloadUrl);
        }
    };
    xhr.send();
}