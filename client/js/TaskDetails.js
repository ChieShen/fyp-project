document.getElementById('fileInput').addEventListener('change', function () {
    const fileNameSpan = document.getElementById('fileName');
    if (this.files.length > 0) {
        fileNameSpan.textContent = this.files[0].name;
    } else {
        fileNameSpan.textContent = 'No file selected';
    }
});

document.querySelector('.uploadForm').addEventListener('submit', function (e) {
    const fileInput = document.getElementById('fileInput');
    if (!fileInput.files.length) {
        e.preventDefault();
    }
});