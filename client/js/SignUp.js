// Event listener for the focusout event
document.getElementById('username').addEventListener('focusout', function () {
    validateInput(this, 'usernameError', 'Username cannot be empty');
});
document.getElementById('fname').addEventListener('focusout', function () {
    validateInput(this, 'fnameError', 'First Name cannot be empty');
});
document.getElementById('lname').addEventListener('focusout', function () {
    validateInput(this, 'lnameError', 'Last Name cannot be empty');
});
document.getElementById('password').addEventListener('focusout', function () {
    validatePassword(this);
});
document.getElementById('confirmPassword').addEventListener('focusout', function () {
    validateConfirmPassword();
});

// Event listeners for input to clear the red border and error message when the user types
document.getElementById('username').addEventListener('input', function () {
    clearError(this, 'usernameError');
});
document.getElementById('fname').addEventListener('input', function () {
    clearError(this, 'fnameError');
});
document.getElementById('lname').addEventListener('input', function () {
    clearError(this, 'lnameError');
});
document.getElementById('password').addEventListener('input', function () {
    validatePassword(this); // Live check password length
});
document.getElementById('confirmPassword').addEventListener('input', function () {
    validateConfirmPassword(); // Live check confirm password match
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

function validatePassword(input) {
    const errorElement = document.getElementById('passwordError');
    const value = input.value.trim();

    if (value === '') {
        input.style.border = '2px solid red';
        errorElement.textContent = 'Password cannot be empty';
    } else if (value.length < 8) {
        input.style.border = '2px solid red';
        errorElement.textContent = 'Password must be at least 8 characters';
    } else {
        input.style.border = '';
        errorElement.textContent = '';
    }
}

function validateConfirmPassword() {
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirmPassword');
    const errorElement = document.getElementById('confirmError');

    if (confirmPassword.value.trim() === '') {
        confirmPassword.style.border = '2px solid red';
        errorElement.textContent = 'Confirm Password cannot be empty';
    } else if (confirmPassword.value !== password) {
        confirmPassword.style.border = '2px solid red';
        errorElement.textContent = 'Passwords do not match';
    } else {
        confirmPassword.style.border = '';
        errorElement.textContent = '';
    }
}

function clearError(input, errorElementId) {
    const errorElement = document.getElementById(errorElementId);
    if (input.value.trim() !== '') {
        input.style.border = ''; // Reset border
        errorElement.textContent = ''; // Clear error message
    }
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