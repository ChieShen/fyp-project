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

            // Fetch options dynamically per group (hardcoded for now)
            const groupOptions = {
                group1: [
                    { value: "stu2", label: "Ben Lim" },
                    { value: "stu3", label: "Chloe Wong" },
                    { value: "stu4", label: "Lengthy Name Checking how the box handles" }
                ],
                group2: [
                    { value: "stu4", label: "Daniel Lee" },
                    { value: "stu5", label: "Eva Ng" }
                ]
            };

            const options = groupOptions[groupId] || [];

            showMessageBox({
                type: "transfer",
                titleText: "Transfer Leader Role",
                messageText: `Select a student from ${groupId} to transfer leadership`,
                confirmText: "Transfer",
                inputType: "select",
                inputOptions: options,
                onConfirm: (studentId) => {
                    console.log(`Transferring leader of ${groupId} from ${currentLeaderId} to ${studentId}`);
                    // TODO: Send API call or perform action
                }
            });

            // Optional: remove `?type=logout` if you reused it
            const url = new URL(window.location.href);
            url.searchParams.delete("type");
            window.history.replaceState({}, '', url);
        });
    });
});
