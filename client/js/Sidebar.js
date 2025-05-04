document.addEventListener('DOMContentLoaded', () => {
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar a');

    sidebarLinks.forEach(link => {
        const linkPath = new URL(link.href, window.location.origin).pathname;

        if (currentPath.includes(linkPath)) {
            link.classList.add('active');
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const logoutBtn = document.getElementById("logoutBtn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", (e) => {
            e.preventDefault(); // Prevent immediate navigation
            showMessageBox({
                titleText: "Logout",
                messageText: "Are you sure you want to logout?",
                confirmText: "Logout",
                onConfirm: () => {
                    console.log("Item deleted.");
                }
            });

            const url = new URL(window.location.href);
            url.searchParams.set("type", "logout");
            window.history.replaceState({}, '', url);
        });
    }

    const params = new URLSearchParams(window.location.search);
    const type = params.get("type");
    if (type) {
        const url = new URL(window.location.href);
        url.searchParams.delete("type");
        window.history.replaceState({}, '', url);
    }
});


document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleBtn");
    const page = document.querySelector(".page");

    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        page.classList.toggle("sidebar-collapsed");
    });
});
