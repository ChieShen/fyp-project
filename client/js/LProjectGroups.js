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
                messageText: `Select a student from Group ${groupId} to transfer leadership`,
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
                            // Check if the response is valid JSON
                            if (response.ok) {
                                return response.json();
                            } else {
                                throw new Error('Failed to fetch data');
                            }
                        })
                        .then(data => {
                            console.log(data);
                            if (data.status === 'success') {
                                location.reload(); // Or update UI dynamically
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

