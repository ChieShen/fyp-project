function validateCode(input) {
    const errorElement = document.getElementById('projectCodeError');
    const value = input.value.trim();

    if (value === '') {
        input.style.border = '2px solid red';
        errorElement.textContent = 'Project Code cannot be empty';
    } else if (!/^\d{6}$/.test(value)) {
        input.style.border = '2px solid red';
        errorElement.textContent = 'Project Code must be exactly 6 digits (0-9)';
    } else {
        input.style.border = '';
        errorElement.textContent = '';
    }
}

function showMessageBox(type) {
    const title = document.getElementById("messageTitle");
    const message = document.getElementById("message");
    const confirmBtn = document.getElementById("confirm");
    const messageBox = document.getElementById("messageBox");
    const messageBoxWrapper = document.getElementById('messageBoxWrapper');

    messageBox.style.display = "flex";
    messageBoxWrapper.style.display = "flex";

    // Clear old input if it exists
    const existingInput = document.getElementById("projectCode");
    if (existingInput) existingInput.remove();

    if (type === "join") {
        title.textContent = "Join A Project";
        message.textContent = "Enter Code to Join A Project";

        const input = document.createElement("input");
        input.type = "text";
        input.id = "projectCode";
        input.maxLength = 6;
        input.placeholder = "Enter 6-digit code";

        messageBox.insertBefore(input, confirmBtn);

        input.addEventListener("input", function () {
            validateCode(this);
        });

        confirmBtn.textContent = "Join";
        confirmBtn.onclick = () => {
            const code = input.value;
            if (!/^\d{6}$/.test(code)) return;
            console.log("Joining with code:", code);
        };
    } else if (type === "logout") {
        title.textContent = "Logout";
        message.textContent = "Are you sure you want to logout?";
        confirmBtn.textContent = "Logout";
        confirmBtn.onclick = () => {
            window.location.href = "/FYP2025/SPAMS/server/controllers/LogoutController.php";
        };
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const closeBtn = document.getElementById("close");
    closeBtn.addEventListener("click", () => {
        document.getElementById("messageBox").style.display = "none";
        document.getElementById("messageBoxWrapper").style.display = "none";
        const url = new URL(window.location.href);
        url.searchParams.delete("type");
        window.history.replaceState({}, '', url);
    });
})
