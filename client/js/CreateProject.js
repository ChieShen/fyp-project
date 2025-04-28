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

document.addEventListener('DOMContentLoaded', () => {
    const fields = [
        { id: 'projectName', errorId: 'pNameError', errorMessage: 'Project Name cannot be empty' },
        { id: 'projectDesc', errorId: 'pDescError', errorMessage: 'Project Description cannot be empty' },
        { id: 'deadline', errorId: 'deadlineError', errorMessage: 'Deadline cannot be empty' },
        { id: 'groupCount', errorId: 'gcError', errorMessage: 'Enter a valid number of groups' },
        { id: 'maxMem', errorId: 'maxMemError', errorMessage: 'Enter a valid maximum number of members' }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.id);

        input.addEventListener('focusout', function () {
            if (field.id === 'groupCount' || field.id === 'maxMem') {
                validateNumberInput(this, field.errorId, field.errorMessage);
            } else {
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
    const groupCount = document.getElementById('groupCount');
    const maxMem = document.getElementById('maxMem');

    const pNameError = document.getElementById('pNameError');
    const pDescError = document.getElementById('pDescError');
    const deadlineError = document.getElementById('deadlineError');
    const gcError = document.getElementById('gcError');
    const maxMemError = document.getElementById('maxMemError');

    let isValid = true;

    // Reset errors
    [pNameError, pDescError, deadlineError, gcError, maxMemError].forEach(err => err.textContent = '');
    [projectName, projectDesc, deadline, groupCount, maxMem].forEach(input => input.style.border = '');

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
    }

    if (groupCount.value.trim() === '' || isNaN(groupCount.value) || parseInt(groupCount.value) <= 0) {
        isValid = false;
        groupCount.style.border = '2px solid red';
        gcError.textContent = 'Enter a valid number of groups';
    }

    if (maxMem.value.trim() === '' || isNaN(maxMem.value) || parseInt(maxMem.value) <= 0) {
        isValid = false;
        maxMem.style.border = '2px solid red';
        maxMemError.textContent = 'Enter a valid maximum number of members';
    }

    if (!isValid) {
        event.preventDefault();
        return false; // Important for inline onsubmit
    }

    return isValid;
}