// Функция для удаления файла с Яндекс.Диска
function deleteFile(filename) {
    const xhr = new XMLHttpRequest();
    const url = window.location.href; // Текущий URL для отправки на тот же PHP-скрипт

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("Файл * " +filename+ " * - успешно удален.");
        }
    };

    const params = "file=" + encodeURIComponent(filename);
    xhr.send(params);
}

document.addEventListener("DOMContentLoaded", function() {
    const deleteButtons = document.querySelectorAll(".delete-btn");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            const filename = this.getAttribute("data-file");
            deleteFile(filename);
        });
    });
});
