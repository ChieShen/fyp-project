document.getElementById('fileInput').addEventListener('change', function () {
    const fileNameSpan = document.getElementById('fileName');
    if (this.files.length > 0) {
        fileNameSpan.textContent = this.files[0].name;
    } else {
        fileNameSpan.textContent = 'No file selected';
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const transferButton = document.getElementById("transferBtn");

    transferButton.addEventListener("click", (e) => {
        e.preventDefault();

        const groupId = transferButton.getAttribute("data-group-id");
        const currentLeaderId = transferButton.getAttribute("data-current-leader");

        const membersJson = transferButton.getAttribute("data-members");
        const options = JSON.parse(membersJson || "[]");

        showMessageBox({
            type: "transfer",
            titleText: "Transfer Leader Role",
            messageText: "Select a student to transfer leadership",
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
                        console.error(error);
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
