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
          showMessageBox("join");
          const url = new URL(window.location.href);
          url.searchParams.set("type", "join");
          window.history.replaceState({}, '', url);
      });
  }

  // Handle URL param on page load
  const params = new URLSearchParams(window.location.search);
  const type = params.get("type");
  if (type === "join" || type === "logout") {
      showMessageBox(type);
  }
});

