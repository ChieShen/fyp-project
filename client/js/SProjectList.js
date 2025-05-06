document.addEventListener('DOMContentLoaded', () => {
    const bars = document.querySelectorAll('.progress-bar');

    bars.forEach(bar => {
        const percent = parseInt(bar.dataset.progress);

        // Reset width to 0 for animation to apply
        bar.style.width = '0%';
        bar.textContent = '';

        // Delay applying final width to trigger transition
        setTimeout(() => {
            bar.style.width = percent + '%';
            bar.textContent = percent + '%';
        }, 100); // 100ms delay ensures transition kicks in
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const joinBtn = document.getElementById('joinProject');

    if (joinBtn) {
        joinBtn.addEventListener("click", () => {
            showMessageBox({
                type: "join",
                titleText: "Join A Project",
                messageText: "Enter Code to Join A Project",
                confirmText: "Join",
                inputType: "text",
                onConfirm: (code) => {
                    const upperCode = code.trim().toUpperCase();

                    if (!/^[A-Z0-9]{6}$/.test(upperCode)) {
                        document.getElementById("projectCodeError").textContent = "Invalid format";
                        return;
                    }

                    fetch("/FYP2025/SPAMS/server/controllers/JoinProjectController.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ joinCode: upperCode })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = data.redirectUrl; // Redirect to project page
                            } else {
                                document.getElementById("projectCodeError").textContent = data.message || "Invalid code";
                            }
                        })
                        .catch(err => {
                            console.error("Join code error:", err);
                            document.getElementById("projectCodeError").textContent = "Something went wrong.";
                        });
                }

            });

            const url = new URL(window.location.href);
            url.searchParams.set("type", "join");
            window.history.replaceState({}, '', url);
        });
    }
});

