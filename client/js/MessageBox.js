function validateCode(input) {
    const errorElement = document.getElementById('projectCodeError');
    const value = input.value.trim();

    if (value === '') {
        input.style.border = '2px solid red';
        errorElement.textContent = 'Project Code cannot be empty';
    } else if (value.length != 6) {
        input.style.border = '2px solid red';
        errorElement.textContent = 'Project Code must be exactly 6 characters';
    } else {
        input.style.border = '';
        errorElement.textContent = '';
    }
}

function showMessageBox(options) {
    const { type, titleText, messageText, confirmText, onConfirm, inputType, inputOptions } = options;

    const title = document.getElementById("messageTitle");
    const message = document.getElementById("message");
    const confirmBtn = document.getElementById("confirm");
    const messageBox = document.getElementById("messageBox");
    const messageBoxWrapper = document.getElementById("messageBoxWrapper");

    messageBox.style.display = "flex";
    messageBoxWrapper.style.display = "flex";

    // Remove any old dynamic inputs
    const dynamicInput = document.getElementById("dynamicInput");
    if (dynamicInput) dynamicInput.remove();

    title.textContent = titleText || "";
    message.textContent = messageText || "";
    confirmBtn.textContent = confirmText || "Confirm";

    if (inputType === "text") {
        const input = document.createElement("input");
        input.type = "text";
        input.id = "dynamicInput";
        input.placeholder = "Enter value";
        messageBox.insertBefore(input, confirmBtn);
        input.addEventListener("input", () => {
            if (type === "join") validateCode(input);
        });
    }

    if (inputType === "select" && Array.isArray(inputOptions)) {
        const select = document.createElement("select");
        select.id = "dynamicInput";
        inputOptions.forEach(opt => {
            const option = document.createElement("option");
            option.value = opt.value;
            option.textContent = opt.label;
            select.appendChild(option);
        });
        messageBox.insertBefore(select, confirmBtn);
    }

    confirmBtn.onclick = () => {
        const input = document.getElementById("dynamicInput");
        const inputValue = input ? input.value : null;
        if (onConfirm) onConfirm(inputValue);
    };
}


document.addEventListener("DOMContentLoaded", () => {
    const closeBtn = document.getElementById("close");
    closeBtn.addEventListener("click", () => {
        document.getElementById("messageBox").style.display = "none";
        document.getElementById("messageBoxWrapper").style.display = "none";
        document.getElementById("projectCodeError").textContent = "";
        const url = new URL(window.location.href);
        url.searchParams.delete("type");
        window.history.replaceState({}, '', url);
    });
})
