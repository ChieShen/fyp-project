document.addEventListener("DOMContentLoaded", () => {
    const createBtn = document.getElementById('createProject');
  
    if (createBtn) {
        createBtn.addEventListener("click", () => {
            window.location.href = `/FYP2025/SPAMS/client/pages/lecturer/CreateProject.php`;
        });
    }
  });

  document.addEventListener("DOMContentLoaded", () => {
    const headers = document.querySelectorAll(".columnName");
    const listBox = document.querySelector(".listTable");
    let currentSort = { column: "deadline", direction: 1 };

    function sortRows(column, direction) {
        const rows = Array.from(document.querySelectorAll(".dataRow"));

        rows.sort((a, b) => {
            let valA = a.getAttribute(`data-${column}`)?.toLowerCase() || "";
            let valB = b.getAttribute(`data-${column}`)?.toLowerCase() || "";

            if (column === "deadline") {
                return (new Date(valA) - new Date(valB)) * direction;
            }

            return valA.localeCompare(valB) * direction;
        });

        rows.forEach(row => listBox.appendChild(row));
    }

    function updateSortIndicators(column, direction) {
        headers.forEach(header => {
            header.classList.remove("selected");
            const icon = header.querySelector(".sortIcon");
            if (icon) icon.className = "sortIcon";
        });

        const selectedHeader = document.querySelector(`.columnName[data-column="${column}"]`);
        if (selectedHeader) {
            selectedHeader.classList.add("selected");
            const icon = selectedHeader.querySelector(".sortIcon");
            if (icon) icon.classList.add(direction === 1 ? "asc" : "desc");
        }
    }

    headers.forEach(header => {
        header.style.cursor = "pointer";
        header.addEventListener("click", () => {
            const column = header.getAttribute("data-column");
            if (!column) return;

            currentSort.direction = currentSort.column === column ? -currentSort.direction : 1;
            currentSort.column = column;

            sortRows(column, currentSort.direction);
            updateSortIndicators(column, currentSort.direction);
        });
    });

    sortRows(currentSort.column, currentSort.direction);
    updateSortIndicators(currentSort.column, currentSort.direction);
});