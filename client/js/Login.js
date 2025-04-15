// Event listener for the focusout event
document.getElementById('username').addEventListener('focusout', function() {
    validateInput(this, 'usernameError', 'Username cannot be empty');
});
document.getElementById('password').addEventListener('focusout', function() {
    validateInput(this, 'passwordError', 'Password cannot be empty');
});

// Event listeners for input to clear the red border and error message when the user types
document.getElementById('username').addEventListener('input', function() {
    clearError(this, 'usernameError');
});
document.getElementById('password').addEventListener('input', function() {
    clearError(this, 'passwordError');
});

// Validate the input fields when focusout event occurs
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

function clearError(input, errorElementId) {
    const errorElement = document.getElementById(errorElementId);
    if (input.value.trim() !== '') {
        input.style.border = ''; // Reset border
        errorElement.textContent = ''; // Clear error message
    }
}

function validateForm(event) {
    // Get form elements
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const usernameError = document.getElementById('usernameError');
    const passwordError = document.getElementById('passwordError');
    let isValid = true;

    // Reset the errors and borders
    usernameError.textContent = '';
    passwordError.textContent = '';
    username.style.border = '';
    password.style.border = '';

    // Validate Username
    if (username.value.trim() === '') {
      isValid = false;
      username.style.border = '2px solid red';
      usernameError.textContent = 'Username cannot be empty';
    }

    // Validate Password
    if (password.value.trim() === '') {
      isValid = false;
      password.style.border = '2px solid red';
      passwordError.textContent = 'Password cannot be empty';
    }

    // If invalid, prevent form submission
    if (!isValid) {
      event.preventDefault();
    }
  }