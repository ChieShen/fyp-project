document.addEventListener('DOMContentLoaded', () => {
    const fields = [
        { id: 'username', errorId: 'usernameError', errorMessage: 'Username cannot be empty' },
        { id: 'fname', errorId: 'fnameError', errorMessage: 'First Name cannot be empty' },
        { id: 'lname', errorId: 'lnameError', errorMessage: 'Last Name cannot be empty' }
    ];

    // Set up input and focusout listeners for regular fields
    fields.forEach(({ id, errorId, errorMessage }) => {
        const input = document.getElementById(id);

        input.addEventListener('focusout', () => {
            validateInput(input, errorId, errorMessage);
        });

        input.addEventListener('input', () => {
            clearError(input, errorId);
        });
    });

    // Special handling for password fields
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');

    password.addEventListener('focusout', () => validatePassword(password));
    password.addEventListener('input', () => validatePassword(password));

    confirmPassword.addEventListener('focusout', validateConfirmPassword);
    confirmPassword.addEventListener('input', validateConfirmPassword);
});

// Validation functions
function validateInput(input, errorElementId, errorMessage) {
    const errorElement = document.getElementById(errorElementId);
    if (input.value.trim() === '') {
        showError(input, errorElement, errorMessage);
    } else {
        clearError(input, errorElementId);
    }
}

function validatePassword(input) {
    const errorElement = document.getElementById('passwordError');
    const value = input.value.trim();

    if (value === '') {
        showError(input, errorElement, 'Password cannot be empty');
    } else if (value.length < 8) {
        showError(input, errorElement, 'Password must be at least 8 characters');
    } else {
        clearError(input, 'passwordError');
    }
}

function validateConfirmPassword() {
    const passwordVal = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirmPassword');
    const errorElement = document.getElementById('confirmError');

    if (confirmPassword.value.trim() === '') {
        showError(confirmPassword, errorElement, 'Confirm Password cannot be empty');
    } else if (confirmPassword.value !== passwordVal) {
        showError(confirmPassword, errorElement, 'Passwords do not match');
    } else {
        clearError(confirmPassword, 'confirmError');
    }
}

function showError(input, errorElement, message) {
    input.style.border = '2px solid red';
    errorElement.textContent = message;
}

function clearError(input, errorElementId) {
    const errorElement = document.getElementById(errorElementId);
    input.style.border = '';
    errorElement.textContent = '';
}

function validateForm(event) {
    const username = document.getElementById('username');
    const fname = document.getElementById('fname');
    const lname = document.getElementById('lname');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const usernameError = document.getElementById('usernameError');
    const fnameError = document.getElementById('fnameError');
    const lnameError = document.getElementById('lnameError');
    const passwordError = document.getElementById('passwordError');
    const confirmError = document.getElementById('confirmError');

    let isValid = true;

    // Reset previous styles and errors
    usernameError.textContent = '';
    fnameError.textContent = '';
    lnameError.textContent = '';
    passwordError.textContent = '';
    confirmError.textContent = '';
    username.style.border = '';
    fname.style.border = '';
    lname.style.border = '';
    password.style.border = '';
    confirmPassword.style.border = '';

    // Username validation
    if (username.value.trim() === '') {
        isValid = false;
        username.style.border = '2px solid red';
        usernameError.textContent = 'Username cannot be empty';
    }

    if (fname.value.trim() === '') {
        isValid = false;
        fname.style.border = '2px solid red';
        fnameError.textContent = 'First Name cannot be empty';
    }

    if (lname.value.trim() === '') {
        isValid = false;
        lname.style.border = '2px solid red';
        lnameError.textContent = 'Last Name cannot be empty';
    }

    // Password validation
    if (password.value.trim() === '') {
        isValid = false;
        password.style.border = '2px solid red';
        passwordError.textContent = 'Password cannot be empty';
    } else if (password.value.trim().length < 8) {
        isValid = false;
        password.style.border = '2px solid red';
        passwordError.textContent = 'Password must be at least 8 characters';
    }

    // Confirm password validation
    if (confirmPassword.value.trim() === '') {
        isValid = false;
        confirmPassword.style.border = '2px solid red';
        confirmError.textContent = 'Confirm Password cannot be empty';
    } else if (confirmPassword.value !== password.value) {
        isValid = false;
        confirmPassword.style.border = '2px solid red';
        confirmError.textContent = 'Passwords do not match';
    }

    if (!isValid) {
        event.preventDefault();
        return false; // Important for inline onsubmit
    }

    return true;
}