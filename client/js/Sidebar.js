document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggleBtn");

    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
    });
});

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
