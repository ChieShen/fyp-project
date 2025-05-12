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

document.addEventListener("DOMContentLoaded", () => {
    const deleteBtn = document.getElementById("deleteBtn");

    deleteBtn.addEventListener("click", (e) => {
        e.preventDefault();

        const taskId = deleteBtn.getAttribute("data-taskid");
        const taskName = deleteBtn.getAttribute("data-taskname");
        const projectId = deleteBtn.getAttribute("data-projectid");
        const groupId = deleteBtn.getAttribute("data-groupid");

        showMessageBox({
            titleText: "Delete Task",
            messageText: `Are you sure you want to delete task "${taskName}"?`,
            confirmText: "Delete",
            onConfirm: () => {
                const formData = new URLSearchParams();
                formData.append("action", "delete");
                formData.append("taskID", taskId);
                formData.append("projectID", projectId);
                formData.append("groupID", groupId);

                fetch("/FYP2025/SPAMS/server/controllers/TaskController.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: formData.toString()
                })
                .then(() => {
                    // Redirect user manually after deletion
                    window.location.href = `/FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=${projectId}&groupID=${groupId}`;
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while deleting the task.");
                });
            }
        });

        // Clean up URL query if needed
        const url = new URL(window.location.href);
        url.searchParams.delete("type");
        window.history.replaceState({}, '', url);
    });
});
