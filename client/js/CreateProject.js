document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const addFileBtn = document.getElementById('addFile');
    const form = document.querySelector('form');

    let files = [];

    addFileBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        Array.from(fileInput.files).forEach(file => {
            // Prevent duplicates
            if (files.some(f => f.name === file.name && f.size === file.size)) return;

            files.push(file);

            const fileEntry = document.createElement('div');
            fileEntry.className = 'fileItem';
            fileEntry.innerHTML = `
                <label>${file.name}</label>
                <button type="button" class="removeFile" id="removeFile">Remove</button>
            `;

            // Handle removal properly
            fileEntry.querySelector('.removeFile').addEventListener('click', () => {
                files = files.filter(f => f !== file);
                fileEntry.remove();
            });

            fileList.appendChild(fileEntry);
        });

        fileInput.value = ''; // allow re-selecting the same file
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault(); // prevent normal form submission

        const formData = new FormData(form);

        files.forEach(file => {
            formData.append('files[]', file);
        });

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (res.redirected) {
                window.location.href = res.url;
            } else {
                return res.text().then(alert);
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while uploading.");
        });
    });
});
