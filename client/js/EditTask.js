document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.querySelector('textarea');

    textarea.addEventListener('input', function () {
        this.style.height = 'auto'; // Reset height
        this.style.height = this.scrollHeight + 'px'; // Set new height
    });
});

function liveValidate(inputEl, errorEl, message) {
    const validate = () => {
        if (inputEl.value.trim() === '') {
            inputEl.style.border = '2px solid red';
            errorEl.textContent = message;
        } else {
            inputEl.style.border = '';
            errorEl.textContent = '';
        }
    };

    inputEl.addEventListener('focusout', validate);
    inputEl.addEventListener('input', validate);
}

function isAtLeastOneChecked(checkboxes) {
    return Array.from(checkboxes).some(cb => cb.checked);
}

document.addEventListener('DOMContentLoaded', () => {
    const taskName = document.getElementById('taskName');
    const taskDesc = document.getElementById('taskDesc');
    const contributorCheckboxes = document.querySelectorAll('input[name="contributors[]"]');
    const tNameError = document.getElementById('tNameError');
    const tDescError = document.getElementById('tDescError');

    // Live input validation
    liveValidate(taskName, tNameError, 'Task Name cannot be empty');
    liveValidate(taskDesc, tDescError, 'Task Description cannot be empty');

    contributorCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const contributorError = document.getElementById('contribError');
            if (isAtLeastOneChecked(contributorCheckboxes)) {
                contributorError.textContent = '';
            }
        });
    });

    // Main validation on submit
    window.validateForm = function (event) {
        let valid = true;

        if (taskName.value.trim() === '') {
            taskName.style.border = '2px solid red';
            tNameError.textContent = 'Task Name cannot be empty';
            valid = false;
        }

        if (taskDesc.value.trim() === '') {
            taskDesc.style.border = '2px solid red';
            tDescError.textContent = 'Task Description cannot be empty';
            valid = false;
        }

        if (!isAtLeastOneChecked(contributorCheckboxes)) {
            let errorEl = document.getElementById('contribError');
            errorEl.textContent = 'Select at least one contributor';
            valid = false;
        }

        if (!valid) event.preventDefault();
    };
});
