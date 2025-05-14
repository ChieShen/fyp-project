document.addEventListener('DOMContentLoaded', () => {
    const fName = document.getElementById('fName');
    const lName = document.getElementById('lName');
    const curPass = document.getElementById('curPass');
    const newPass = document.getElementById('newPass');
    const conPass = document.getElementById('conPass');

    const fNameError = document.getElementById('fNameError');
    const lNameError = document.getElementById('lNameError');
    const curPassError = document.getElementById('curPassError');
    const newPassError = document.getElementById('newPassError');
    const conPassError = document.getElementById('conPassError');

    function validateLive() {
        let isValid = true;

        // First name check
        if (!fName.value.trim()) {
            fNameError.textContent = "First name cannot be empty";
            isValid = false;
        } else {
            fNameError.textContent = "";
        }

        // Last name check
        if (!lName.value.trim()) {
            lNameError.textContent = "Last name cannot be empty";
            isValid = false;
        } else {
            lNameError.textContent = "";
        }

        // Password fields logic
        const cur = curPass.value.trim();
        const newP = newPass.value.trim();
        const con = conPass.value.trim();

        const anyFilled = cur || newP || con;

        curPassError.textContent = "";
        newPassError.textContent = "";
        conPassError.textContent = "";

        if (anyFilled) {
            if (!cur || !newP || !con) {
                if (!cur) curPassError.textContent = "Current password required";
                if (!newP) newPassError.textContent = "New password required";
                if (!con) conPassError.textContent = "Confirm password required";
                isValid = false;
            } else {
                if (newP.length < 8) {
                    newPassError.textContent = "New password must be at least 8 characters";
                    isValid = false;
                }

                if (con.length < 8) {
                    conPassError.textContent = "Confirm password must be at least 8 characters";
                    isValid = false;
                }

                if (newP !== con) {
                    conPassError.textContent = "Passwords do not match";
                    isValid = false;
                }
            }
        }

        return isValid;
    }

    // Live validation
    [fName, lName, curPass, newPass, conPass].forEach(input => {
        input.addEventListener('input', validateLive);
    });

    // Final validation on submit
    window.validateForm = function (e) {
        if (!validateLive()) {
            e.preventDefault(); // stop form submission
        }
    };
});
