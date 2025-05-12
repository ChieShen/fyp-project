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
    const transferButtons = document.querySelectorAll(".transferBtn");

    transferButtons.forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault();

            const groupId = button.getAttribute("data-group-id");
            const currentLeaderId = button.getAttribute("data-current-leader");

            const membersJson = button.getAttribute("data-members");
            const options = JSON.parse(membersJson || "[]");

            showMessageBox({
                type: "transfer",
                titleText: "Transfer Leader Role",
                messageText: `Select a student to transfer leadership`,
                confirmText: "Transfer",
                inputType: "select",
                inputOptions: options,
                onConfirm: (studentId) => {
                    fetch('/FYP2025/SPAMS/server/controllers/LeaderTransferController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            groupID: groupId,
                            newLeaderID: studentId
                        }),
                    })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            } else {
                                throw new Error('Failed to fetch data');
                            }
                        })
                        .then(data => {
                            console.log(data);
                            if (data.status === 'success') {
                                location.reload();
                            } else {
                                alert("Failed to transfer leadership: " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error(error); // Log the full error for debugging
                            alert("An error occurred: " + error.message);
                        });

                }

            });

            // Clean up URL query if needed
            const url = new URL(window.location.href);
            url.searchParams.delete("type");
            window.history.replaceState({}, '', url);
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const removeBtns = document.querySelectorAll(".removeBtn");

    removeBtns.forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault();

            const groupId = button.getAttribute("data-group-id");
            const userId = button.getAttribute("data-user-id");
            const username = button.getAttribute("data-username");
            const grpname = button.getAttribute("data-grpname")

            showMessageBox({
                titleText: "Remove User From Group",
                messageText: `Are you sure you want to remove ${username} from ${grpname}?`,
                confirmText: "Confirm",
                onConfirm: () => {
                    fetch("/FYP2025/SPAMS/server/controllers/RemoveMemberController.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `groupID=${groupId}&userID=${userId}`
                    })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            } else {
                                throw new Error('Failed to fetch data');
                            }
                        })
                        .then(data => {
                            if (data.success) {
                                console.log(`${username} has been removed`);
                                location.reload();
                            } else {
                                alert("Failed to remove user: " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("An unexpected error occurred.");
                        });
                }

            });

            // Clean up URL query if needed
            const url = new URL(window.location.href);
            url.searchParams.delete("type");
            window.history.replaceState({}, '', url);
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const removeBtns = document.querySelectorAll(".deleteBtn");

    removeBtns.forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault();

            const groupId = button.getAttribute("data-group-id");
            const userId = button.getAttribute("data-user-id");
            const username = button.getAttribute("data-username");
            const grpname = button.getAttribute("data-grpname")

            showMessageBox({
                titleText: "Delete Group",
                messageText: `Are you sure you want to delete ${grpname}?`,
                confirmText: "Confirm",
                onConfirm: () => {
                    fetch('/FYP2025/SPAMS/server/controllers/DeleteGroupController.php', {
                        method: 'POST',
                        body: new URLSearchParams({ groupId }),
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Server error: ' + data.message);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Unexpected server error. Please try again later.');
                        });

                }

            });

            // Clean up URL query if needed
            const url = new URL(window.location.href);
            url.searchParams.delete("type");
            window.history.replaceState({}, '', url);
        });
    });
});