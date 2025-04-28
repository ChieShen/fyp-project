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