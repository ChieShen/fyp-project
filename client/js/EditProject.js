document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.querySelector('textarea');

    textarea.addEventListener('input', function () {
        this.style.height = 'auto'; // Reset height
        this.style.height = this.scrollHeight + 'px'; // Set new height
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const addFileBtn = document.getElementById('addFile');
    const form = document.querySelector('form');

    let newFiles = [];
    let removedFileIds = [];

    const fileTitleDiv = document.querySelector('.fileTitle');
    const existingFiles = JSON.parse(fileTitleDiv.getAttribute('data-existingfiles'));

    existingFiles.forEach(file => {
        const fileEntry = document.createElement('div');
        fileEntry.className = 'fileItem';
        fileEntry.innerHTML = `
            <label>${file.displayName}</label>
            <button type="button" class="removeFile">Remove</button>
        `;

        fileEntry.querySelector('.removeFile').addEventListener('click', () => {
            removedFileIds.push(file.id);
            fileList.removeChild(fileEntry);
        });

        fileList.appendChild(fileEntry);
    });

    // Add new files dynamically
    addFileBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        Array.from(fileInput.files).forEach(file => {
            // Prevent duplicates
            if (newFiles.some(f => f.name === file.name && f.size === file.size)) return;

            newFiles.push(file);
            console.log('file added: ', file);

            const fileEntry = document.createElement('div');
            fileEntry.className = 'fileItem';
            fileEntry.innerHTML = `
                <label>${file.name}</label>
                <button type="button" class="removeFile">Remove</button>
            `;

            fileEntry.querySelector('.removeFile').addEventListener('click', () => {
                fileEntry.remove();
            });

            fileList.appendChild(fileEntry);
        });

        fileInput.value = ''; // reset to allow re-adding same file
    });

    // Handle form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        newFiles.forEach(file => {
            formData.append('files[]', file);
        });

        removedFileIds.forEach(id => {
            formData.append('removeFiles[]', id);
        });

        for (const [key, value] of formData.entries()) {
            if (key === 'files[]' && value instanceof File) {
                alert(`File: ${value.name}, size: ${value.size}, type: ${value.type}`);
            }
        }

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

document.addEventListener('DOMContentLoaded', () => {
    const fields = [
        { id: 'projectName', errorId: 'pNameError', errorMessage: 'Project Name cannot be empty' },
        { id: 'projectDesc', errorId: 'pDescError', errorMessage: 'Project Description cannot be empty' },
        { id: 'deadline', errorId: 'deadlineError', errorMessage: 'Deadline cannot be empty' }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.id);

        input.addEventListener('focusout', function () {
            if (field.id === 'groupCount' || field.id === 'maxMem') {
                validateNumberInput(this, field.errorId, field.errorMessage);
            }
            else if (field.id === 'deadline') {
                validateDateInput(this, field.errorId, field.errorMessage);
            }
            else {
                validateInput(this, field.errorId, field.errorMessage);
            }
        });

        input.addEventListener('input', function () {
            clearError(this, field.errorId);
        });
    });
});

function validateInput(input, errorElementId, errorMessage) {
    const errorElement = document.getElementById(errorElementId);
    if (input.value.trim() === '') {
        input.style.border = '2px solid red';
        errorElement.textContent = errorMessage;
    } else {
        input.style.border = '';
        errorElement.textContent = '';
    }
}

function validateDateInput(input, errorElementId, errorMessage) {
    const errorElement = document.getElementById(errorElementId);
    const selectedDate = new Date(input.value.trim());
    const currentDate = new Date();

    if (input.value.trim() === '') {
        input.style.border = '2px solid red';
        errorElement.textContent = errorMessage;
    } else if (selectedDate < currentDate) {
        input.style.border = '2px solid red';
        errorElement.textContent = 'Deadline cannot be in the past'; // Custom message for past date
    } else {
        input.style.border = '';
        errorElement.textContent = '';
    }
}

function validateNumberInput(input, errorElementId, errorMessage) {
    const errorElement = document.getElementById(errorElementId);
    const value = input.value.trim();
    if (value === '' || isNaN(value) || parseInt(value) <= 0) {
        input.style.border = '2px solid red';
        errorElement.textContent = errorMessage;
    } else {
        input.style.border = '';
        errorElement.textContent = '';
    }
}

function clearError(input, errorElementId) {
    const errorElement = document.getElementById(errorElementId);
    const value = input.value.trim();
    if (value !== '') {
        input.style.border = '';
        errorElement.textContent = '';
    }
}

function validateForm(event) {
    const projectName = document.getElementById('projectName');
    const projectDesc = document.getElementById('projectDesc');
    const deadline = document.getElementById('deadline');

    const pNameError = document.getElementById('pNameError');
    const pDescError = document.getElementById('pDescError');
    const deadlineError = document.getElementById('deadlineError');

    let isValid = true;

    // Reset errors
    [pNameError, pDescError, deadlineError].forEach(err => err.textContent = '');
    [projectName, projectDesc, deadline].forEach(input => input.style.border = '');

    if (projectName.value.trim() === '') {
        isValid = false;
        projectName.style.border = '2px solid red';
        pNameError.textContent = 'Project Name cannot be empty';
    }

    if (projectDesc.value.trim() === '') {
        isValid = false;
        projectDesc.style.border = '2px solid red';
        pDescError.textContent = 'Project Description cannot be empty';
    }

    if (deadline.value.trim() === '') {
        isValid = false;
        deadline.style.border = '2px solid red';
        deadlineError.textContent = 'Deadline cannot be empty';
    } else {
        // Check if the deadline is in the past
        const currentDate = new Date();
        const selectedDate = new Date(deadline.value);
        if (selectedDate < currentDate) {
            isValid = false;
            deadline.style.border = '2px solid red';
            deadlineError.textContent = 'Deadline cannot be in the past';
        }
    }

    if (!isValid) {
        event.preventDefault();
        return false; // Important for inline onsubmit
    }

    return isValid;
}
