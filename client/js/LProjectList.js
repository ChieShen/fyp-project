document.addEventListener("DOMContentLoaded", () => {
    const createBtn = document.getElementById('createProject');
  
    if (createBtn) {
        createBtn.addEventListener("click", () => {
            window.location.href = `/FYP2025/SPAMS/client/pages/lecturer/CreateProject.php`;
        });
    }
  });